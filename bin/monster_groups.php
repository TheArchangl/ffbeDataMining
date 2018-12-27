<?php
    /**
     * User: aEnigma
     * Date: 14.06.2017
     * Time: 17:15
     */

    use Solaris\FFBE\Helper\Strings;

    //require_once "../bootstrap.php";
    require_once "../../ffbe-discord/tmp/request_helper.php";
    require_once "../../ffbe-discord/tmp/init_strings.php";

    // setup
    $region     = 'gl';
    $mission_id = "{9510105,9510106}";

    // get data
    $files = glob(CLIENT_DIR . "missions\\{$region}\\*\\{$mission_id}\\*.json", GLOB_BRACE);
    natsort($files);


    // input
    /** @var Mission[] $missions */
    $missions = [];

    foreach ($files as $file) {
        $data = file_get_contents($file);
        $data = json_decode($data, true);
        if (!is_array($data)) {
            var_dump(["error", $file]);
            continue;
        }

        // read basic
        $data = replaceRequestKeys($data);
        $data = $data['body']['data'] ?? null;

        if ($data == null)
            continue;

        $mission_id = $data['MissionStartRequest'][0]['mission_id'] ?? null;
        if ($mission_id == null)
            continue;

        if (isset($missions[$mission_id]))
            $mission = $missions[$mission_id];

        else
            $mission = $missions[$mission_id] = new Mission($mission_id);

        $mission->readResponse($data);
    }


    foreach ($missions as $mission)
        $mission->printMissions();

    class Mission {
        public $id;
        public $runs     = 0;
        public $info     = [];
        public $data     = [];
        public $groups   = []; // battle groups
        public $monsters = [];

        /**
         * @param $id
         */
        public function __construct($id) { $this->id = $id; }


        /**
         * @param array $data
         */
        public function readResponse(array $data) {
            $this->runs++;

            $this->readGroups($data);
            $this->readLoot($data);
        }

        public function printMissions() {
            $name = Strings::getString('MST_MISSION_NAME', $this->id);

            print " + {$name} ({$this->id}) [{$this->runs}]\n";

            // iterate over waves
            ksort($this->data);
            foreach ($this->data as $wave_num => $groups) {
                $count = array_sum($groups);
                printf("    Wave {$wave_num} [{$count} - %.1f%% (%.1f%%)]\n", number_format($count / $this->runs * 100, 1), $this->info[$wave_num]);

                ksort($groups);

                foreach ($groups as $group_id => $count) {
                    print "        Group {$group_id} [{$count} - " . number_format($count / $this->runs * 100, 1) . "%]\n";

                    foreach ($this->groups[$group_id] as $monster_id) {
                        // $loot         = $this->$this->monsters[$monster_id] ?? [];
                        // $loot         = printTable($loot, true);
                        foreach ($this->monsters[$monster_id] as $part => $monster)
                            print "            ($monster_id.{$part}) {$monster['name']}\n";
                    }
                }

                print "\n";
            }

            print "\n";


            print "# Monsters\n";
            foreach ($this->monsters as $monster_id => $monster)
                foreach ($monster as $part => $loot_tables) {
                    $name = Strings::getString('MST_MONSTER_NAME', $monster_id);
                    print "    ({$monster_id}.{$part}) {$name}\n";
                    print $this->printTable($loot_tables);
                    print "\n";
                }

            print "\n";
        }

        /**
         * @param array $data
         */
        protected function readLoot(array $data) {
            foreach ($data['MonsterPartsMst'] as $entry) {
                $id   = $entry['monster_unit_id'];
                $part = $entry['monster_parts_num'];

                $entry = [
                    'name'         => Strings::getString('MST_MONSTER_NAME', $id) ?? $entry['name'],
                    'normal'       => $this->parseTable($entry["loot_table"]),
                    'unique'       => $this->parseTable($entry["loot_table_unique"]),
                    'rare'         => $this->parseTable($entry["loot_table_rare"]),
                    'steal_normal' => $this->parseTable($entry["steal_table"]),
                    'steal_unique' => $this->parseTable($entry["steal_table_unique"]),
                    'steal_rare'   => $this->parseTable($entry["steal_table_rare"]),
                    'steal_gil'    => $entry["steal_gil"],
                    'drop_gil'     => $entry["gil"],
                ];

                if (isset($this->monsters[$id][$part]) && $this->monsters[$id][$part] != $entry) {
                    var_dump([
                                 'old' => $this->monsters[$id][$part],
                                 'new' => $entry
                             ]);
                    die();
                }

                $this->monsters[$id][$part] = $entry;
            }
        }

        /**
         * @param string $string
         *
         * @return array
         */
        private function parseTable($string) {
            if (empty($string))
                return [];

            $entries = explode(',', $string);

            $array = [];
            foreach ($entries as $val) {
                [$type, $id, $a, $b] = explode(':', $val);

                $table = \Solaris\FFBE\GameHelper::TEXT_TYPE[$type];
                $name  = \Solaris\FFBE\Helper\Strings::getString($table, $id) ?? "{$type}:{$id}";
                $val   = "{$name}-{$a}-{$b}";

                $array[] = $val;
            }

            return $array;
        }

        /**
         * @param array $loot
         *
         * @return string
         */
        private function printTable(array $loot) {
            $loot = array_filter($loot);

            $string = '';
            foreach ($loot as $table => $items) {
                $string .= vsprintf("        %12s   %s%s", [
                    $table,
                    is_array($items) ? implode(', ', $items) : $items,
                    "\n",
                ]);
            }

            return $string;
        }

        /**
         * @param array $data
         */
        private function readGroups(array $data) {
            // battle groups
            $groups = [];
            foreach ($data['BattleGroupMst'] as $entry) {
                $group_id            = $entry['battle_group_id'];
                $groups[$group_id][] = $entry['monster_unit_id'];
            }


            foreach ($groups as $group_id => $monster_ids) {
                if (isset($this->groups[$group_id]))
                    if ($this->groups[$group_id] != $monster_ids)
                        var_dump($this->groups[$group_id], $monster_ids) || die();

                    else
                        continue;

                $this->groups[$group_id] = $monster_ids;
            }

            // wave battles
            foreach ($data['MissionPhaseMst'] ?? [] as $phase) {
                $battle_group_id = $phase['battle_group_id'];
                if ($battle_group_id == '')
                    continue;

                $wave_num = $phase['wave_num'];

                @$this->data[$wave_num][$battle_group_id]++;


                if (isset($this->info[$wave_num]))
                    assert($this->info[$wave_num] == $phase['weight']) or var_dump([$wave_num, $this->info, $phase]);
                else
                    $this->info[$wave_num] = (int) $phase['weight'];
            }

            // explorations
            foreach ($data['EncountInfo'] ?? [] as $entry) {
                $fight_id        = "{$entry['EncountFieldID']}.{$entry['EncountNum']}";
                $battle_group_id = $entry['battle_group_id'];

                $i = $entry['order_index'] - 1;

                if ($i > 0 || $battle_group_id == '')
                    continue;

                @$this->data[$fight_id][$battle_group_id]++;
            }

            foreach ($data['ScenarioBattleInfo'] ?? [] as $phase) {
                $scenario_id     = $phase['scenario_battle_id'];
                $battle_group_id = $phase['battle_group_id'];

                if ($battle_group_id == '')
                    continue;

                @$this->data[$scenario_id][$battle_group_id]++;
            }
        }
    }
