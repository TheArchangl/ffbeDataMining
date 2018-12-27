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
    $mission_id = "7780106";

    // get data
    $files = glob(CLIENT_DIR . "missions\\{$region}\\*\\{$mission_id}\\*.json");
    natsort($files);


    // input
    $monsters      = [];
    $missions      = [];
    $battle_groups = [];
    $mission_runs  = [];
    /** @var Wave[] $waves */
    $waves = [];

    $last_mission = null;
    foreach ($files as $file) {
        $data = file_get_contents($file);
        $data = json_decode($data, true);
        if (!is_array($data)) {
            var_dump(["error", $file]);
            continue;
        }

        $data = replaceRequestKeys($data);
        $data = $data['body']['data'] ?? null;

        if ($data == null)
            continue;

        $mission_id = $data['MissionStartRequest'][0]['mission_id'] ?? null;
        if ($mission_id == null)
            continue;

        // ScenarioBattleGroupMst
        // BattleGroupMst
        // EncountInfo?

        @$mission_runs[$mission_id]++;

        // read groups
        /** @var Monster[][] $groups */
        $groups = [];
        foreach ($data['BattleGroupMst'] as $entry) {
            $group_id            = $entry['battle_group_id'];
            $groups[$group_id][] = $entry['monster_unit_id'];
        }


        foreach ($groups as $group_id => $monster_ids) {
            if (isset($battle_groups[$group_id])) {
                if ($battle_groups[$group_id] != $monster_ids) {
                    var_dump($battle_groups[$group_id], $monster_ids);
                    die();
                }
            } else {
                $battle_groups[$group_id] = $monster_ids;
            }
        }

        // read loot
        foreach ($data['MonsterPartsMst'] as $entry) {
            $id   = $entry['monster_unit_id'];
            $part = $entry['monster_parts_num'];

            $entry = [
                'name'         => Strings::getString('MST_MONSTER_NAME', $id) ?? $entry['name'],
                'normal'       => parseTable($entry["loot_table"]),
                'unique'       => parseTable($entry["loot_table_unique"]),
                'rare'         => parseTable($entry["loot_table_rare"]),
                'steal_normal' => parseTable($entry["steal_table"]),
                'steal_unique' => parseTable($entry["steal_table_unique"]),
                'steal_rare'   => parseTable($entry["steal_table_rare"]),
                'steal_gil'    => $entry["steal_gil"],
                'drop_gil'     => $entry["gil"],
            ];

            if (isset($monsters[$id][$part]) && $monsters[$id][$part] != $entry) {
                var_dump(['old' => $monsters[$id][$part], 'new' => $entry]);
                die();
            }

            $monsters[$id][$part] = $entry;
        }

        // wavebattle
        foreach ($data['MissionPhaseMst'] ?? [] as $phase) {
            $battle_group_id = $phase['battle_group_id'];
            if ($battle_group_id == '')
                continue;

            $wave_num = $phase['wave_num'];

            //if (!array_search($battle_group_id, $missions[$mission_id][$wave_num]))
            @$missions[$mission_id][$wave_num][$battle_group_id]++;

            if (isset($waves[$wave_num]))
                assert($waves[$wave_num] == $phase['weight']);
            else
                $waves[$wave_num] = (int) $phase['weight'];
        }

        // exploration
        foreach ($data['EncountInfo'] ?? [] as $entry) {
            $fight_id        = "{$entry['EncountFieldID']}.{$entry['EncountNum']}";
            $battle_group_id = $entry['battle_group_id'];

            $i = $entry['order_index'] - 1;
            // $battle_groups[$battle_group_id][$i] = $entry['monster_unit_id'];
            if ($i > 0 || $battle_group_id == '')
                continue;

            @$missions[$mission_id][$fight_id][$battle_group_id]++;
        }

        foreach ($data['ScenarioBattleInfo'] ?? [] as $phase) {
            $scenario_id     = $phase['scenario_battle_id'];
            $battle_group_id = $phase['battle_group_id'];

            // $battle_groups[$battle_group_id][$phase['order_index'] - 1] = $phase['monster_unit_id'];
            if ($battle_group_id == '')
                continue;

            @$missions[$mission_id][$scenario_id][$battle_group_id]++;
        }
    }
    //ksort($waves);
    $waves = array_filter($waves, function ($val) { return $val < 100; });
    ksort($waves);
    print_r($waves);

    ksort($missions);
    foreach ($missions as $mission_id => $waves) {
        $sum          = $mission_runs[$mission_id];
        $mission_name = Strings::getString('MST_MISSION_NAME', $mission_id);
        print " + {$mission_name} ({$mission_id}) [{$sum}]\n";

        ksort($waves);
        foreach ($waves as $wave_num => $groups) {
            $count = array_sum($groups);
            print "    Wave {$wave_num} [{$count} - " . number_format($count / $sum * 100, 1) . "%]\n";

            ksort($groups);

            foreach ($groups as $group_id => $count) {
                print "        Group {$group_id} [{$count} - " . number_format($count / $sum * 100, 1) . "%]\n";

                foreach ($battle_groups[$group_id] as $monster_id) {
                    // $loot         = $monsters[$monster_id] ?? [];
                    // $loot         = printTable($loot, true);
                    foreach ($monsters[$monster_id] as $part => $monster)
                        print "            ($monster_id.{$part}) {$monster['name']}\n";
                }
            }

            print "\n";
        }
        print "\n";
    }

    print "# Monsters\n";
    foreach ($monsters as $monster_id => $monster)
        foreach ($monster as $part => $loot_tables) {
            $monster_name = Strings::getString('MST_MONSTER_NAME', $monster_id);
            print "    ({$monster_id}.{$part}) {$monster_name}\n";
            print printTable($loot_tables, false);
            print "\n";
        }

        function parseTable($string) {
            if (empty($string))
                return [];

            $entries = explode(',', $string);

            $array = [];
            foreach ($entries as $val) {
                [$type, $id, $a, $b] = explode(':', $val);

                $table = \Solaris\FFBE\GameHelper::TEXT_TYPE[$type];
                $name  = \Solaris\FFBE\Helper\Strings::getString($table, $id)
                    ?? "{$type}:{$id}";
                $val   = "{$name}-{$a}-{$b}";

                $array[] = $val;
            }

            return $array;
        }

        function printTable($loot) {
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