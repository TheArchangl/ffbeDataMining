<?php
    /**
     * User: aEnigma
     * Date: 11.02.2017
     * Time: 03:33
     */

    use Sol\FFBE\GameFile;
    use Sol\FFBE\MstList\IconMstList;
    use Sol\FFBE\Strings;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    IconMstList::init();
    // StorySubMstList
    // story events in battle: BattleScriptMstList
    // $rows = GameFile::loadMst('ChallengeMstList');
    // $rows = \Sol\FFBE\GameFile::loadMst('MapResourceMstList');

    // LandMstList -> regions
    $routes = [];
    foreach (GameFile::loadMst('F_MAP_ROUTE_MST') as $row) {
        $route_id = $row['AVE2m0TD'];
        $route    = readParameters($row['5BD2EqeT'], ',:');

        $routes[$route_id] = $route;
    }

    // towns
    $towns = [];
    foreach (GameFile::loadMst('F_TOWN_MST') as $row) {
        $town_id = (int)$row['TownId'];
        $names   = ($region == 'gl')
            ? Strings::getStrings('MST_TOWN_NAME', $town_id) ?? []
            : [$row['name']];

        $towns[$town_id] = [
            'names'        => $names,
            'world_id'     => (int)$row['world_id'],
            'region_id'    => (int)$row['region_id'],
            'subregion_id' => (int)$row['subregion_id'],
            'position'     => readIntArray($row['display_position'], ':'),
            'icon'         => \Sol\FFBE\MstList\IconMstList::getFilename($row['icon_id']),

            // 'route'        => $route,
            // 'temp'         => [
            //     'switch_open'        => $row['switch_open'] ?? null,
            //     'switch_info'        => $row['switch_info'] ?? null,
            //     'switch_non_info'    => $row['switch_non_info'] ?? null,
            //     'effect_switch_info' => $row['effect_switch_info'] ?? null,
            // ],
        ];
    }
    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/towns.json", toJSON($towns, false));

    $entries = [];
    foreach (GameFile::loadMst('F_DUNGEON_MST') as $row) {
        $dungeon_id = $row['dungeon_id'];
        $names      = ($region == 'gl')
            ? Strings::getStrings('MST_DUNGEONS_NAME', $dungeon_id) ?? []
            : [$row['name']];

        $entry = [
            'names'        => $names,
            'world_id'     => (int)$row['world_id'],
            'region_id'    => $row['region_id'],
            'subregion_id' => $row['subregion_id'],
            'position'     => readIntArray($row['display_position'], ':'),
            'icon'         => IconMstList::getFilename($row['icon_id']),

            // 'route'        => $route,

            // // temp
            // 'temp' => [
            //     'switch_open' => $row['switch_open'] ?? null,
            //     'switch_info' => $row['switch_info'] ?? null,
            //     'switch_non_info' => $row['switch_non_info'] ?? null,
            //     'effect_switch_info' => $row['effect_switch_info'] ?? null,
            // ],
        ];

        // 69fVGgcI type ['story', 'vortex']
        // 9Pb24aSy type? ['', 'story', 'vortex']
        // DutE7B3F prequisiste?
        // juA0Z4m7 prequisiste?
        // MxLFKZ13 start_position? coords on map ?
        // U9hr20s7 cutscene?
        // amG29ZFs event entry / icon see LM1APs6u.dat
        // q4f9IdMs ???
        // rFd5CiV2 type? ['town', 'dungeon']
        // uv60hgDW story id? submap?


        $entries[$dungeon_id] = $entry;
    }

    // mission names
    foreach (GameFile::loadMst('MissionMstList') as $row) {
        $mission_id = (int)$row['mission_id'];
        $dungeon_id = (int)$row['dungeon_id'];
        $name       = ($region == 'gl')
            ? Strings::getString('MST_MISSION_NAME', $mission_id)
            : $row['name'];

        $entries[$dungeon_id]['missions'][$mission_id] = $name;
    }

    /*
        file_put_contents(DATA_OUTPUT_DIR . "/{$region}/analyze.json", toJSON(
        arrayGroupValues(
            array_map(
                function ($row) { return ['name' => Strings::getString('MST_DUNGEONS_NAME', $row['dungeon_id']) ?? $row['name']] + $row; },
                GameFile::loadMst('F_DUNGEON_MST')
            )),
        false));
     */
    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/dungeons.json", toJSON($entries, false));