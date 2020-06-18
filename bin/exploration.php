<?php
    /**
     * User: aEnigma
     * Date: 22.05.2017
     * Time: 14:24
     */

    use Sol\FFBE\GameFile;
    use Solaris\FFBE\Helper\Strings;

    require_once __DIR__ . "/../bootstrap.php";
    require_once __DIR__ . "/read_strings.php";

    ini_set('memory_limit', '2G');


    // import all requests
    $region     = 'gl';
    $mission_id = 30700102;

    $files = glob(CLIENT_DIR . "missions\\{$region}\\*\\{$mission_id}\\*.json");
    natsort($files);

    $rewards = [];
    foreach ($files as $file) {
        $data = file_get_contents($file);
        $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        $data = GameFile::replaceKeysRecursive($data);

        $data = $data['body']['data'];

        $mission_id = $data['MissionStartRequest'][0]['mission_id'];
        // Monster
        $monsters = [];
        foreach ($data['MonsterMst'] as $entry) {
            $monster_id      = $entry['monster_unit_id'];
            $monster_unit_id = $entry['monster_unit_id'];

            $name = Strings::getString('MST_MONSTER_NAME', $monster_id) ?? $entry['name'];

            $monsters[$monster_unit_id] = $name;
        }

        // EncountInfo
        // BattleGroupMst
        $encounters     = ['random' => [], 'story' => []];
        $rng_encounters =& $encounters['random'];
        foreach ($data['EncountInfo'] as $entry) {
            // foreach ($data['BattleGroupMst'] as $entry) {
            $battle_group_id = $entry['battle_group_id'];
            $monster_unit_id = $entry['monster_unit_id'];
            $compendium_id   = $entry['order_index']; // monster_parts_num

            // $loot_tables = join('#', [
            //     $entry['loot_table'],
            //     $entry['loot_table_rare'],
            //     $entry['loot_table_unique'],
            //     $entry['loot_table_unit'],
            // ]);

            $name = $monsters[$monster_unit_id];

            $rng_encounters[$battle_group_id][$compendium_id][] = $name;
            // [
            //     'name' => $name,
            //     'loot' => $loot_tables,
            // ];
        }

        $story_encounters =& $encounters['story'];
        foreach ($data['ScenarioBattleInfo'] as $entry) {
            // foreach ($data['BattleGroupMst'] as $entry) {
            $battle_group_id = $entry['battle_group_id'];
            $monster_unit_id = $entry['monster_unit_id'];
            $compendium_id   = $entry['order_index'];

            $loot_tables = join('#', [
                $entry['loot_table'],
                $entry['loot_table_rare'],
                $entry['loot_table_unique'],
                $entry['loot_table_unit'],
            ]);

            $name = $monsters[$monster_unit_id];

            $story_encounters[$battle_group_id][$compendium_id][] = [
                'name' => $name,
                'loot' => $loot_tables,
            ];

            // var_dump($encounters);
        }

        //

    }

    ksort($rewards);

    var_dump($story_encounters);
