<?php

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;

    require_once __DIR__ . '/../bootstrap.php';

    const BLACKLIST = [
        'F_TEXT_BATTLE_SCRIPT',
        'F_TEXT_MONSTER_SKILL_SET_NAME',
        'F_TEXT_MAP_OBJECT',
        'F_TEXT_ANALYTICS_ITEMS',
        'F_TEXT_ANALYTICS_LOCALIZE',
        'F_TEXT_DEFINE',
        'F_TEXT_DESCRIPTION_FORMAT',
        'F_TEXT_DESCRIPTION_FORMAT_1',
        'F_TEXT_DESCRIPTION_FORMAT_2',
        'F_TEXT_DESCRIPTION_FORMAT_3',
        'F_TEXT_DESCRIPTION_FORMAT_4',
        'F_TEXT_DESCRIPTION_FORMAT_5',
        //        'F_TEXT_TEXT_EN.txt',
    ];

    const WHITELIST = [
        'MST_GAMBIT_NAME',
        'MST_GAMBIT_EXPLAIN',
        'MST_MORALE_BEFORE_BATTLE_RULE',
        'MST_MORALE_IN_BATTLE_RULE',
    ];

    $region ??= 'gl';

    $cmd  = strtolower(realpath($argv[0]));
    $full = in_array($cmd, array_map(static fn($path) => strtolower(realpath($path)), [__FILE__, __DIR__ . '/run_all.php']), true);
    if (! $full)
        /** @noinspection UsingInclusionReturnValueInspection */
        return require __DIR__ . '/read_strings.php';

    if (! in_array(__DIR__ . '/client_update.php', get_included_files()))
        require_once __DIR__ . '/client_update.php';

    echo "Parsing strings\n";

    switch ($region) {
        case 'gl':
            // overwrite
            $files = glob(CLIENT_DIR . 'files/gl/*/F_TEXT_*.txt');
            $files = array_filter($files, static function ($file) { return ! in_array(basename($file, '.txt'), BLACKLIST, true); });

            foreach ($files as $file)
                Strings::readFile($file, basename(dirname($file)));

            // read manual overwrite
            $file = ROOT_DIR . '/strings/gl/manual.json';
            $data = file_get_contents($file);
            $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

            foreach ($data as $k => $row) {
                $row = (array) $row;

                Strings::setEntry($k, $row);
            }
            break;


        case 'jp':
            // fill jp data
            $msts = [
                'F_GAME_TITLE_MST'         => 'MST_GAME_TITLE_NAME',
                'F_MAGIC_MST'              => 'MST_MAGIC_NAME',
                'F_MISSION_MST'            => 'MST_MISSION_NAME',
                'F_ITEM_MST'               => 'MST_ITEM_NAME',
                'F_MATERIA_MST'            => 'MST_MATERIA_NAME',
                'F_RECIPE_BOOK_MST'        => 'MST_RECIPEBOOK_NAME',
                'F_IMPORTANT_ITEM_MST'     => 'MST_IMPORTANT_ITEM_NAME',
                'F_UNIT_MST'               => 'MST_UNIT_NAME',
                'F_EQUIP_ITEM_MST'         => 'MST_EQUIP_ITEM_NAME',
                'F_VISION_CARD_MST'        => 'MST_VISION_CARD_NAME',
                'F_MONSTER_DICTIONARY_MST' => 'MST_MONSTER_NAME',
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
    $strings = Strings::getEntries();
    uksort($strings, 'strnatcmp');

    $output = [];
    $base   = array_fill(0, count(\Solaris\FFBE\Helper\Strings::LANGUAGE_ID), null);
    foreach ($strings as $k => $strs) {
        if ($region !== 'gl')
            $strs = $strs[0];

        if (preg_match('~^(.*?)_(\d+(?:[,_]\d+)*)$~', $k, $match)) {
            $output[$match[1]][$match[2]] = $strs;
        }
        else {
            if (empty($k) || ctype_digit($k) || $k[-1] === '_')
                continue;

            $k = utf8_encode($k);

            $output['misc'][$k] = $strs;
        }
    }

    foreach ($output as $file => $strings) {
        if (isset(WHITELIST[$file]) || count($strings) > 10)
            continue;

        foreach ($strings as $k => $string)
            $output['misc']["{$file}_{$k}"] = $string;

        unset($output[$file]);
    }


    // output
    $dir = ROOT_DIR . "/strings/{$region}";
    if (! is_dir($dir) && ! mkdir($dir, 0777, true) && ! is_dir($dir))
        throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));

    foreach ($output as $file => $strings) {
        ksort($strings);
        foreach ($strings as $k => $strs)
            if (is_array($strs))
                ksort($strings[$k]);

        $data = json_encode((object) $strings, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        file_put_contents("{$dir}/{$file}.json", $data);
    }