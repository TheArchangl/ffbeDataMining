<?php
    /**
     * User: aEnigma
     * Date: 11.06.2017
     * Time: 19:25
     */

    use Sol\FFBE\GameFile;
    use Solaris\FFBE\Helper\Strings;

    require_once "../bootstrap.php";

    $se_id = '760';
    $region = 'gl';
    $files  = glob(CLIENT_DIR . "missions\\{$region}\\{$se_id}*\\*\\*.json");

    natsort($files);
    $missions     = [];
    $last_dungeon = null;
    foreach ($files as $file) {
        $data = file_get_contents($file);
        $data = json_decode($data, true);
        $data = GameFile::replaceKeysRecursive($data);

        $data = $data['body']['data'];

        $mission_id = $data['MissionStartRequest'][0]['mission_id'] ?? null;
        if ($mission_id == null)
            continue;

        if (in_array($mission_id, $missions))
            continue;

        $missions[] = $mission_id;

        $dungeon_id = substr($mission_id, -4, -2);

        $groups = [];
        foreach ($data['BattleGroupMst'] as $entry)
            $groups[$entry['battle_group_id']][] = $entry['monster_unit_id'];

        $monsters = [];
        foreach ($data['MonsterPartsMst'] as $entry) {
            $monsters[$entry['monster_unit_id']][$entry['monster_parts_num']] = [
                'normal      ' => parseTable($entry["loot_table"]),
                'unique      ' => parseTable($entry["loot_table_unique"]),
                'rare        ' => parseTable($entry["loot_table_rare"]),
                'steal_normal' => parseTable($entry["steal_table"]),
                'steal_unique' => parseTable($entry["steal_table_unique"]),
                'steal_rare  ' => parseTable($entry["steal_table_rare"]),
                'steal_gil   ' => $entry["steal_gil"],
                ' drop_gil   ' => $entry["gil"],
            ];
        }

        $waves = [];
        foreach ($data['MissionPhaseMst'] as $phase) {
            $battle_group_id = $phase['battle_group_id'];
            if ($battle_group_id == '')
                continue;

            $group = $groups[$battle_group_id];
            $wave  = [];

            foreach ($group as $id)
                $wave[$id][] = $monsters[$id];

            $waves[] = $wave;
        }

        // battleinfo ? check
        //WaveBattleInfo

        // output
        if ($dungeon_id != $last_dungeon) {
            // $dungeon_name = Strings::getString('MST_DUNGEONS_NAME', $dungeon_id);
            echo "\n# Stage {$dungeon_id}\n";
            $last_dungeon = $dungeon_id;
        }

        $mission_name = Strings::getString('MST_MISSION_NAME', $mission_id);
        echo "# Mission {$mission_name} ({$mission_id})\n";

        foreach ($waves as $k => $wave) {
            echo "\t# Wave " . ($k + 1);

            foreach ($wave as $monster_id => $entries)
                foreach ($entries as $monster_parts)
                    foreach ($monster_parts as $monster_part_num => $loot) {
                        $name = Strings::getString('MST_MONSTER_NAME', $monster_id);
                        echo "\n\t\t- {$monster_id}: {$name}";

                        $loot = array_filter($loot);
                        if (empty($loot))
                            echo "-";

                        else
                            foreach ($loot as $type => $table) {
                                if (is_array($table))
                                    $table = join(', ', $table) ?: '-';

                                echo "\n\t\t\t* {$type} {$table}";
                            }

                        // echo "\n";
                    }
            echo "\n";
        }
        echo "\n";

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