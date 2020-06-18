<?php
    /**
     * User: aEnigma
     * Date: 14.06.2017
     * Time: 17:15
     */

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\GameHelper;

    require_once '../bootstrap.php';
    require_once __DIR__ . '/read_strings.php';
    ini_set('assert.active', 1);

    // setup
    $region     = 'gl';
    $dungeon_id = '*';
    $mission_id = '30700101';


    // get data
    $files  = glob(CLIENT_DIR . "missions\\{$region}\\{$dungeon_id}\\{$mission_id}\\*.json", GLOB_BRACE);
    $reader = new MonsterGroupReader();
    $reader->readFiles($files);
    $reader->printOutput();

    class GroupContainer {
        /** @var MonsterGroup[] */
        public array $groups = [];

        public function readRow(array $row): self {
            $id = $row['battle_group_id'];

            if (isset($this->groups[$id]))
                $this->groups[$id]->monsters[$row['order_index'] - 1] = $row['monster_unit_id'];

            else
                $this->groups[$id] = MonsterGroup::readRow($row);


            return $this;
        }
    }

    class MonsterGroup {
        public int    $group_id;
        public int    $order_index;
        public array  $monsters = [];

        public static function readRow(array $row): self {
            $new                                    = new static();
            $new->order_index                       = $row['order_index'];
            $new->group_id                          = $row['battle_group_id'];
            $new->monsters[$row['order_index'] - 1] = $row['monster_unit_id'];

            return $new;
        }
    }

    class MonsterGroupReader {
        // input
        protected bool             $is_exploration = false;
        protected array            $monster_loot   = [];
        protected array            $missions       = [];
        protected GroupContainer   $battle_groups;
        protected array            $mission_runs   = [];
        protected array            $waves          = [];

        public function __construct() {
            $this->battle_groups = new GroupContainer();
        }

        public function readFiles(array $files): void {
            foreach ($files as $file) {
                $data = file_get_contents($file);
                $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
                $data = GameFile::replaceKeysRecursive($data);
                $data = $data['body']['data'] ?? null;

                if ($data == null)
                    continue;

                $mission_id = $data['MissionStartRequest'][0]['mission_id'] ?? null;
                if ($mission_id == null)
                    continue;

                // ScenarioBattleGroupMst
                // BattleGroupMst
                // EncountInfo?
                @$this->mission_runs[$mission_id]++;

                // read groups
                foreach ($data['BattleGroupMst'] as $entry)
                    $this->battle_groups->readRow($entry);

                // read loot
                foreach ($data['MonsterPartsMst'] as $entry) {
                    $id   = $entry['monster_unit_id'];
                    $part = $entry['monster_parts_num'];

                    $entry = [
                        'name'         => Strings::getString('MST_MONSTER_NAME', $id) ?? $entry['name'],
                        // 'ai_id'        => (int) $entry['ai_id'],
                        'normal'       => $this->parseTable($entry['loot_table']),
                        'unique'       => $this->parseTable($entry['loot_table_unique']),
                        'rare'         => $this->parseTable($entry['loot_table_rare']),
                        'steal_normal' => $this->parseTable($entry['steal_table']),
                        'steal_unique' => $this->parseTable($entry['steal_table_unique']),
                        'steal_rare'   => $this->parseTable($entry['steal_table_rare']),
                        'steal_gil'    => $entry['steal_gil'],
                        'drop_gil'     => $entry['gil'],
                    ];

                    if (isset($this->monster_loot[$id][$part]) && $this->monster_loot[$id][$part] != $entry) {
                        var_dump(['old' => $this->monster_loot[$id][$part], 'new' => $entry]);
                        die();
                    }

                    $this->monster_loot[$id][$part] = $entry;
                }

                // wavebattle
                foreach ($data['MissionPhaseMst'] ?? [] as $phase) {
                    $battle_group_id = $phase['battle_group_id'];
                    $wave_num        = $phase['wave_num'];

                    @$this->missions[$mission_id][$wave_num][$battle_group_id]++;

                    if (isset($this->waves[$mission_id][$wave_num]))
                        assert($this->waves[$mission_id][$wave_num] == $phase['weight']);

                    else
                        $this->waves[$mission_id][$wave_num] = (int) $phase['weight'];
                }

                // exploration
                foreach ($data['EncountInfo'] ?? [] as $entry) {
                    #var_dump($entry);
                    #die();

                    $fight_id        = $entry['EncountFieldID']; #"{$entry['EncountFieldID']}.{$entry['EncountNum']}";
                    $battle_group_id = $entry['battle_group_id'];

                    #$i = $entry['order_index'] - 1;
                    if ($battle_group_id == '')
                        continue;

                    $this->missions[$mission_id][$fight_id]['exploration'] = true;
                    @$this->missions[$mission_id][$fight_id][$battle_group_id]++;
                }

                foreach ($data['ScenarioBattleInfo'] ?? [] as $phase) {
                    $scenario_id     = $phase['scenario_battle_id'];
                    $battle_group_id = $phase['battle_group_id'];

                    if ($battle_group_id == '')
                        continue;

                    @$this->missions[$mission_id][$scenario_id][$battle_group_id]++;
                }
            }

            ksort($this->missions);
            ksort($this->waves);
        }

        public function parseTable($string): array {
            if (empty($string))
                return [];

            $entries = explode(',', $string);

            $array = [];
            foreach ($entries as $val) {
                [$type, $id, $a, $b] = explode(':', $val);

                $table = GameHelper::TEXT_TYPE[$type];
                $name  = Strings::getString($table, $id) ?? "{$type}:{$id}";
                $val   = "{$name} x{$a} ({$b}%)";

                $array[] = $val;
            }

            return $array;
        }

        public function printOutput(): void {
            foreach ($this->missions as $mission_id => $waves) {
                $sum          = $this->mission_runs[$mission_id];
                $mission_name = Strings::getString('MST_MISSION_NAME', $mission_id) ?? "Mission {$mission_id}";
                print " + {$mission_name} ({$mission_id}) [{$sum}]\n";

                ksort($waves);
                foreach ($waves as $wave_num => $groups) {
                    $explo  = $groups['exploration'] ?? false;
                    $type   = $explo ? 'Zone' : 'Wave';
                    $groups = array_filter($groups, 'is_int');
                    $count  = array_sum($groups);

                    printf("    {$type} {$wave_num} [{$count}");
                    if (! $explo)
                        printf(' - %.1f%% | %.1f%%]', $count / $sum * 100, $this->waves[$mission_id][$wave_num] ?? 100);
                    else
                        print ']';
                    print("\n");

                    ksort($groups);

                    foreach ($groups as $group_id => $count) {
                        print "        Group {$group_id} [{$count}";
                        if (! $explo)
                            print ' - ' . number_format($count / $sum * 100, 1) . ' %]';
                        else
                            print ']';

                        print("\n");

                        foreach ($this->battle_groups->groups[$group_id]->monsters as $monster_id) {
                            // $loot         = $this->monsters[$monster_id] ?? [];
                            // $loot         = printTable($loot, true);
                            foreach ($this->monster_loot[$monster_id] as $part => $monster)
                                print "            ({$monster_id}.{$part}) {$monster['name']}\n";
                        }
                    }

                    print "\n";
                }
                print "\n";
            }

            print "# Monsters\n";
            foreach ($this->monsters as $monster_id => $monster)
                foreach ($monster as $part => $loot_tables) {
                    $monster_name = Strings::getString('MST_MONSTER_NAME', $monster_id);
                    print "    ({$monster_id}.{$part}) {$monster_name}\n";
                    print $this->printTable($loot_tables);
                    print "\n";
                }

        }

        protected function printTable($loot): string {
            $loot = array_filter($loot);

            $string = '';
            foreach ($loot as $table => $items)
                if ($table != 'name')
                    $string .= vsprintf('        %12s   %s%s', [$table, is_array($items) ? implode(', ', $items) : $items, "\n",]);

            return $string;
        }
    }