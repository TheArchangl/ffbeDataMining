<?php
    /**
     * User: aEnigma
     * Date: 14.04.2017
     * Time: 01:48
     */

    use Solaris\FFBE\Helper\Strings;

    require_once dirname(__DIR__)  . "/bootstrap.php";
    require_once dirname(__DIR__)  . "/tmp/request_helper.php";
    require_once dirname(__DIR__)  . "/tmp/init_strings.php";

    $region     = 'jp';
    $mission_id = 8150106;
    //
    $directory = CLIENT_DIR . "missions/{$region}/*/{$mission_id}";
    var_dump($directory);
    $files     = glob("{$directory}/*.json");

    natsort($files);

    // input
    $drops        = [];
    $monsters     = [];
    $last_dungeon = null;
    foreach ($files as $file) {
        $data = file_get_contents($file);
        $data = json_decode($data, true);
        $data = replaceRequestKeys($data);
        $data = $data['body']['data'] ?? null;

        if ($data == null)
            continue;

        $mission_id = $data['MissionStartRequest'][0]['mission_id'] ?? null;
        if ($mission_id == null)
            continue;

        $dungeon_id = substr($mission_id, -4, -2);

        /** @var Monster[][] $groups */
        $groups = [];
        foreach ($data['BattleGroupMst'] as $entry) {
            $monster     = new Monster();
            $monster->id = $entry['monster_unit_id'];

            $groups[$entry['battle_group_id']][] = $monster;
        }

        foreach ($data['MonsterPartsMst'] as $entry) {
            $id = $entry['monster_unit_id'];

            if (!isset($monsters[$id])) {
                $monsters[$id] = [
                    'normal'       => parseTable($entry["loot_table"]),
                    'unique'       => parseTable($entry["loot_table_unique"]),
                    'rare'         => parseTable($entry["loot_table_rare"]),
                    'steal_normal' => parseTable($entry["steal_table"]),
                    'steal_unique' => parseTable($entry["steal_table_unique"]),
                    'steal_rare'   => parseTable($entry["steal_table_rare"]),
                    'steal_gil'    => $entry["steal_gil"],
                    'drop_gil'     => $entry["gil"],
                ];
            } else {
                $a = [
                    'normal'       => parseTable($entry["loot_table"]),
                    'unique'       => parseTable($entry["loot_table_unique"]),
                    'rare'         => parseTable($entry["loot_table_rare"]),
                    'steal_normal' => parseTable($entry["steal_table"]),
                    'steal_unique' => parseTable($entry["steal_table_unique"]),
                    'steal_rare'   => parseTable($entry["steal_table_rare"]),
                    'steal_gil'    => $entry["steal_gil"],
                    'drop_gil'     => $entry["gil"],
                ];
                assert($monsters[$id] == $a) || var_dump([$a, $monsters[$id]]) || die();
            }
        }

        // wavebattle
        /** @var Wave[] $waves */
        $waves = [];
        foreach ($data['MissionPhaseMst'] ?? [] as $phase) {
            $battle_group_id = $phase['battle_group_id'];
            if ($battle_group_id == '')
                continue;

            $wave           = new Wave($battle_group_id);
            $wave->monsters = $groups[$battle_group_id];

            $waves[] = $wave;
        }

        // set loot
        foreach ($data['WaveBattleInfo'] ?? [] as $entry) {
            $battle_group_id = $entry['battle_group_id'];
            $index           = $entry['order_index'] - 1;

            $loot = [
                'normal' => parseTable($entry['loot_table'] ?? ''),
                'unique' => parseTable($entry['loot_table_unique'] ?? ''),
                'rare'   => parseTable($entry['loot_table_rare'] ?? ''),
            ];
            $loot = array_filter($loot);

            if (empty($loot))
                continue;

            $groups[$battle_group_id][$index]->loot = array_map('current', $loot);
        }

        // exploration
        foreach ($data['ScenarioBattleInfo'] ?? [] as $phase) {
            $scenario_id     = $phase['scenario_battle_id'];
            $battle_group_id = $phase['battle_group_id'];
            if ($battle_group_id == '')
                continue;

            $group = $groups[$battle_group_id];
            $wave  = $waves[$scenario_id] ?? new Wave($battle_group_id);

            foreach ($group as $monster)
                $wave->monsters[] = $monster;

            $waves[$scenario_id] = $wave;
        }

        // output
        if ($dungeon_id != $last_dungeon) {
            // $dungeon_name = Strings::getString('MST_DUNGEONS_NAME', $dungeon_id);
            #echo "\n# Stage {$dungeon_id}\n";
            $last_dungeon = $dungeon_id;
        }

        $mission_name = Strings::getString('MST_MISSION_NAME', $mission_id);
        $showLoot     = false;
        // echo "# Mission {$mission_name} ({$mission_id})\n";

        foreach ($waves as $k => $wave) {
            // echo "# [{$wave->id}] Wave " . ($k + 1);

            foreach ($wave->monsters as $monster)
                $drops[".{$monster->id}"][] = $monster->loot ?: [];
            // $drops["{$wave->id}.{$monster->id}"][] = $monster->loot ?: [];
            /*
            foreach ($wave->monsters as $monster) {
                $name = str_pad($monster->getName(), 18);
                echo "\n[{$wave->id}]({$monster->id}) {$name} {$monster->loot}";

                if ($showLoot) {
                    $loot = $monsters[$monster->id];
                    $loot = array_filter($loot);
                    if (empty($loot))
                        echo "-";

                    else
                        foreach ($loot as $type => $table) {
                            if (is_array($table))
                                $table = join(', ', $table) ?: '-';

                            echo "\n\t    * {$type} {$table}";
                        }

                }
            */
            // echo "\n";
        }
        // echo "\n";
    }

    // output
    ksort($drops);
    foreach ($drops as $monster => $entries) {
        $counts = [];
        foreach ($entries as $entry)
            foreach ($entry as $table => $item)
                @$counts[$table][$item]++;

        $count = count($entries);
        [$battle_group_id, $monster_id] = explode('.', $monster);
        $monster_name = Strings::getString('MST_MONSTER_NAME', $monster_id);

        print "[$monster] {$monster_name} x{$count}\n";
        foreach ($counts as $table => $entry) {
            // arsort($entry);

            $drop_table = implode(', ', $monsters[$monster_id][$table]);
            print "\t{$table}: {$drop_table}\n";
            foreach ($entry as $item => $count)
                printf("\t\t%-24s %3d\n", $item, $count);
            print "\n";
        }
        print "\n\n";

    }

    function parseTable($string) {
        if (empty($string))
            return [];

        $entries = explode(',', $string);

        $array = [];
        foreach ($entries as $val) {
            [$type, $id, $a, $b] = explode(':', $val);

            $strtable = \Solaris\FFBE\GameHelper::TEXT_TYPE[$type];
            $val      = \Solaris\FFBE\Helper\Strings::getString($strtable, $id) . "-{$a}-{$b}";

            $array[] = $val;
        }

        return $array;
    }

    class Wave {
        public $id;

        /** @var Monster[] */
        public $monsters = [];

        /**
         * @param $id
         */
        public function __construct($id) { $this->id = $id; }
    }

    class Monster {
        public $id;
        public $loot;

        public function getName() {
            return Strings::getString('MST_MONSTER_NAME', $this->id) ?? $this->id;
        }
    }