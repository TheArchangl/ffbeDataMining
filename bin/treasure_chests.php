<?php
    /**
     * User: aEnigma
     * Date: 26.05.2017
     * Time: 00:55
     */

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    $entries = [];
    foreach (\Sol\FFBE\GameFile::loadMst('FieldTreasureMstList') as $row) {
        $id = (int)$row['treasure_id'];
        if ($id == 1234)
            continue;
        var_dump($row);
        die();

        $reward = parseReward($row['treasure_content']);
        $entries[$id] = [$reward[2], (int)$reward[3]];
    }

    echo toJSON($entries);