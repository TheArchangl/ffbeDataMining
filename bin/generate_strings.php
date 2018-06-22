<?php
    /**
     * User: gkubach
     * Date: 22.06.2018
     * Time: 15:24
     */

    use Sol\FFBE\Strings;

    require_once __DIR__ . "/../bootstrap.php";

    const BLACKLIST = [
        'F_TEXT_BATTLE_SCRIPT.txt',
        'F_TEXT_MONSTER_SKILL_SET_NAME.txt',
        'F_TEXT_MAP_OBJECT.txt',
        //        'F_TEXT_TEXT_EN.txt',
    ];

    switch ($region) {
        case "gl":

            $files = glob(CLIENT_DIR . "files/gl/F_TEXT_*.txt");
            $files = array_filter($files, function ($file) { return !in_array(basename($file), BLACKLIST); });

            foreach ($files as $file)
                Strings::readFile($file);


            $strings = Strings::getAll();
            uksort($strings, function ($a, $b) { return strnatcmp($a, $b); });

            $output = [];
            foreach ($strings as $k => $strs) {
                $arr = $strs + array_fill(0, 6, null);
                ksort($arr);

                if (preg_match('~^(.+?)_(\d+)~', $k, $match)) {
                    [$_, $table, $id] = $match;
                    $output[$table][$id] = $arr;
                } else {
                    if (empty($k) || ctype_digit($k))
                        continue;

                    $output['misc'][$k] = $arr;
                }
            }

            ksort($output);

            foreach ($output as $file => $strings)
                file_put_contents(
                    DATA_OUTPUT_DIR . "/{$region}/strings/{$file}.json",
                    json_encode((object) $strings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
                );
            break;


        case "jp":
            break;
    }