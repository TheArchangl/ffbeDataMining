<?php

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;

    require_once __DIR__ . '/../bootstrap.php';
    require_once __DIR__ . '/client_update.php';

    const BLACKLIST = [
        'F_TEXT_BATTLE_SCRIPT',
        'F_TEXT_MONSTER_SKILL_SET_NAME',
        'F_TEXT_MAP_OBJECT',
        'F_TEXT_ANALYTICS_ITEMS',
        'F_TEXT_ANALYTICS_LOCALIZE',
        'F_TEXT_DEFINE',
        'F_TEXT_DESCRIPTION_FORMAT'
        //        'F_TEXT_TEXT_EN.txt',
    ];

    $full = in_array(strtolower(realpath($argv[0])), array_map(fn($path) => strtolower(realpath($path)), [__FILE__, __DIR__ . '/runAll.php']), true);
    if (! $full)
        return require __DIR__ . '/read_strings.php';

    echo "Parsing strings\n";

    switch ($region) {
        case 'gl':
            /*
            // fill jp data
            $msts = [
                'F_GAME_TITLE_MST'     => 'MST_GAME_TITLE_NAME',
                'F_MAGIC_MST'          => 'MST_MAGIC_NAME',
                'F_MISSION_MST'        => 'MST_MISSION_NAME',
                'F_ITEM_MST'           => 'MST_ITEM_NAME',
                'F_MATERIA_MST'        => 'MST_MATERIA_NAME',
                'F_RECIPE_BOOK_MST'    => 'MST_RECIPEBOOK_NAME',
                'F_IMPORTANT_ITEM_MST' => 'MST_IMPORTANT_ITEM_NAME',
                'F_UNIT_MST'           => 'MST_UNIT_NAME',
                'F_EQUIP_ITEM_MST'     => 'MST_EQUIP_ITEM_NAME',
            ];

            foreach ($msts as $mst => $table) {
                foreach (GameFile::loadMst($mst) as $row) {
                    $id   = current($row);
                    $name = $row['name'];

                    Strings::setString($table, $id, $name);
                }
            }
            */

            // overwrite
            $files = glob(CLIENT_DIR . 'files/gl/*/F_TEXT_*.txt');
            $files = array_filter($files, static function ($file) { return ! in_array(basename($file, '.txt'), BLACKLIST, true); });

            foreach ($files as $file)
                Strings::readFile($file, basename(dirname($file)));

            // read manual overwrite
            $file = ROOT_DIR . "/strings/gl/manual.json";
            $data = file_get_contents($file);
            $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

            foreach ($data as $k => $row)
                Strings::setEntry($k, (array) $row);
            break;


        case 'jp':
            // fill jp data
            $msts = [
                'F_GAME_TITLE_MST'     => 'MST_GAME_TITLE_NAME',
                'F_MAGIC_MST'          => 'MST_MAGIC_NAME',
                'F_MISSION_MST'        => 'MST_MISSION_NAME',
                'F_ITEM_MST'           => 'MST_ITEM_NAME',
                'F_MATERIA_MST'        => 'MST_MATERIA_NAME',
                'F_RECIPE_BOOK_MST'    => 'MST_RECIPEBOOK_NAME',
                'F_IMPORTANT_ITEM_MST' => 'MST_IMPORTANT_ITEM_NAME',
                'F_UNIT_MST'           => 'MST_UNIT_NAME',
                'F_EQUIP_ITEM_MST'     => 'MST_EQUIP_ITEM_NAME',
                'F_VISION_CARD_MST'    => 'MST_VISION_CARD_NAME',
            ];

            foreach ($msts as $mst => $table) {
                foreach (GameFile::loadMst($mst) as $row) {
                    $id   = current($row);
                    $name = $row['name'];

                    Strings::setString($table, $id, $name);
                }
            }

            // overwrite with gl if possible
            $files = glob(CLIENT_DIR . 'files/gl/en/F_TEXT_*.txt');
            $files = array_filter($files, static function ($file) { return ! in_array(basename($file, '.txt'), BLACKLIST, true); });

            foreach ($files as $file)
                Strings::readFile($file, 'en');

            break;
    }

    // write to file
    echo "Writing strings\n";

    if (! $full) {
        // ghetto __main__
        echo "\tSkip!\n";

        return;
    }

    $strings = Strings::getEntries();
    uksort($strings, 'strnatcmp');

    $output = [];
    $base   = array_fill(0, count(\Solaris\FFBE\Helper\Strings::LANGUAGE_ID), null);
    foreach ($strings as $k => $strs) {
        if ($region !== 'gl')
            $strs = $strs[0];

        if (preg_match('~^(.*?)_(\d+(?:_\d+)*)$~', $k, $match)) {
            $output[$match[1]][$match[2]] = $strs;
        }
        else {
            if (empty($k) || ctype_digit($k) || $k[-1] == '_')
                continue;

            $k = utf8_encode($k);

            $output['misc'][$k] = $strs;
        }
    }

    foreach ($output as $file => $strings) {
        if (count($strings) > 20)
            continue;

        foreach ($strings as $k => $string)
            $output['misc']["{$file}_{$k}"] = $string;

        unset($output[$file]);
    }

    //            ksort($output);

    $dir = ROOT_DIR . "/strings/{$region}";
    if (! is_dir($dir))
        mkdir($dir, 0777, true);

    foreach ($output as $file => $strings) {
        ksort($strings);
        foreach ($strings as $k => $strs)
            if (is_array($strs))
                ksort($strings[$k]);

        $data = json_encode((object) $strings, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT, 512);
        file_put_contents("{$dir}/{$file}.json", $data);
    }