<?php
    /**
     * User: aEnigma
     * Date: 29.05.2017
     * Time: 00:07
     */

    use Sol\FFBE\Strings;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    echo "Reading Recipes\n";
    $entries = [];

    foreach (\Sol\FFBE\GameFile::loadRaw('F_RECIPE_MST') as $row) {
        $id    = (int)$row['MpNE6gB5']; // MstKey::RECIPE_ID
        $type  = (int)$row['gak9Gb3N']; // MstKey::CRAFT_TYPE
        $item  = (int)$row['6uIYE15X']; // MstKey::CRAFT_RESULT
        $time  = (int)$row['M5VLKe2F'];
        $count = (int)$row['Qy5EvcK1'];

        $source = (\Sol\FFBE\GameFile::getRegion() == 'gl')
            ? Strings::getString('MST_RECIPE_EXPLAINLONG', $id)
            : $row['VC9F3eJn']; // in JP

        $mats = [];
        foreach (\Solaris\FFBE\GameHelper::readParameters($row['HS7V6Ww1'], ':,') as $mat)
            $mats["{$mat[0]}:{$mat[1]}"] = (int)$mat[2];

        $entries[$id] = [
            'name'             => null,
            'compendium_id'    => null,
            'compendium_shown' => null,
            'item'             => "{$type}:{$item}",
            'time'             => $time,
            'mats'             => $mats,
            'count'            => $count,
            'source'           => $source,
        ];
    }

    foreach (\Sol\FFBE\GameFile::loadMst('F_RECIPE_BOOK_MST') as $row) {
        $id = (int)$row['staFu8U1']; // 8Qt4Ccew ?

        $entries[$id] = [
            'name' => (\Sol\FFBE\GameFile::getRegion() == 'gl')
                ? Strings::getString('MST_RECIPEBOOK_NAME', $id)
                : $row['name'],

            'compendium_id'    => (int)$row['order_index'],
            'compendium_shown' => (bool)($row['DispDict'] ?? false),
        ] + $entries[$id];
    }

    $data = toJSON($entries);
    file_put_contents(DATA_OUTPUT_DIR . "{$region}/recipes.json", $data);