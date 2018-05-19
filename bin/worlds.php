<?php
    /**
     * User: aEnigma
     * Date: 05.12.2017
     * Time: 16:03
     */

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    /** @noinspection PhpUnhandledExceptionInspection */
    Strings::readFile("F_TEXT_WORLD_NAME");
    Strings::readFile("F_TEXT_LAND_NAME");
    Strings::readFile("F_TEXT_AREA_NAME");

    $entries = [];
    // WORLD
    foreach (GameFile::loadMst('F_WORLD_MST') as $row) {
        $world_id = (int)$row['world_id'];
        $names    = ($region == 'gl')
            ? Strings::getStrings('MST_DUNGEONS_NAME', $world_id) ?? []
            : [$row['name']];

        $entries[$world_id] = [
            'names'   => $names,
            'regions' => [],
        ];
    }

    // LAND
    foreach (GameFile::loadMst('F_LAND_MST') as $row) {
        $world_id  = (int)$row['world_id'];
        $region_id = (int)$row['region_id'];
        $names     = ($region == 'gl')
            ? Strings::getStrings('MST_LAND_NAME', $region_id) ?? []
            : [$row['name']];

        $entries[$world_id]['regions'][$region_id] = [
            'names'      => $names,
            'subregions' => [],
        ];
    }

    // AREA
    foreach (GameFile::loadMst('F_AREA_MST') as $row) {
        $world_id     = (int)$row['world_id'];
        $region_id    = (int)$row['region_id'];
        $subregion_id = (int)$row['subregion_id'];
        $names        = ($region == 'gl')
            ? Strings::getStrings('MST_AREA_NAME', $subregion_id) ?? []
            : [$row['name']];


        $entries[$world_id]['regions'][$region_id]['subregions'][$subregion_id] = [
            'names'    => $names,
            'dungeons' => [],
        ];
    }

    // DUNGEONS
    foreach (GameFile::loadMst('F_DUNGEON_MST') as $row) {
        $world_id     = (int)$row['world_id'];
        $region_id    = (int)$row['region_id'];
        $subregion_id = (int)$row['subregion_id'];
        $dungeon_id   = (int)$row['dungeon_id'];

        $name = ($region == 'gl')
            ? Strings::getString('MST_DUNGEONS_NAME', $dungeon_id)
            : $row['name'];

        $entries[$world_id]['regions'][$region_id]['subregions'][$subregion_id]['dungeons'][$dungeon_id] = $name;
    }

    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/worlds.json", toJSON($entries, false));