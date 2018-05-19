<?php
    /**
     * User: aEnigma
     * Date: 04.02.2017
     * Time: 17:11
     */

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    $files = getFileList();

    $rows = GameFile::loadMst('F_MONSTER_DICTIONARY_MST'); // compendium
    $rows =  GameFile::loadMst('hc8Ham29'); // compendium

    $entries = [];
    foreach ($rows as $k => $row) {
        $id = $row['monster_id'] ?? -1;
        if ($id < 0)
            continue;

        $row['name'] = Strings::getString('MST_MONSTERDICTIONARY_NAME', $id);

//        $games     = readIntArray($row['game_id']); // title?
//        $games     = array_map(function ($val) { return Sol\FFBE\Strings::getString('MST_GAME_TITLE_NAME', $val, 0); }, $games);
//        $row['games?'] = $games;

        $entries[] = $row;
    }

    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/analyze.json", toJSON(arrayGroupValues($entries), false));
    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/monsters.json", toJSON($entries, false));