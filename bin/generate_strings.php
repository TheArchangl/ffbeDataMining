<?php
    /**
     * User: gkubach
     * Date: 22.06.2018
     * Time: 15:24
     */

    use Sol\FFBE\Strings;

    require_once __DIR__ . "/../bootstrap.php";

    const BLACKLIST = [
        'F_TEXT_BATTLE_SCRIPT',
        'F_TEXT_MONSTER_SKILL_SET_NAME',
        'F_TEXT_MAP_OBJECT',
        'F_TEXT_ANALYTICS_ITEMS',
        'F_TEXT_ANALYTICS_LOCALIZE',
        //        'F_TEXT_TEXT_EN.txt',
    ];

    echo "Reading strings\n";

    /**
     * generate file map
     *
     *
     * $map   = [];
     * $files = glob(CLIENT_DIR . "files/gl/F_TEXT_*.txt");
     * foreach ($files as $file) {
     * $name = basename($file, '.txt');
     * if (in_array($name, BLACKLIST))
     * continue;
     *
     * $data = file_get_contents($file);
     * $data = preg_replace('~^([^\^]+?)(_\d+)*\^(.*?)$~m', '$1', $data);
     * $data = preg_split('~\r?\n~', $data);
     * $data = array_filter($data);
     * $data = array_count_values($data);
     *
     * foreach ($data as $k => $count)
     * if ($count > 3)
     * $map[$k][] = $name;
     * }
     * ksort($map);
     * foreach ($map as $type => $files) {
     * $files = array_map(function ($f) { return "'{$f}'"; }, $files);
     * $files = join(', ', $files);
     *
     * echo "  '{$type}' => [{$files}],\n";
     * }
     * die();
     */


    switch ($region) {
        case "gl":
            $files = glob(CLIENT_DIR . "files/gl/F_TEXT_*.txt");
            $files = array_filter($files, function ($file) { return !in_array(basename($file, '.txt'), BLACKLIST); });

            foreach ($files as $file)
                Strings::readFile($file);


            $strings = Strings::getEntries();
            uksort($strings, function ($a, $b) { return strnatcmp($a, $b); });

            $output = [];
            foreach ($strings as $k => $strs) {
                if (count($strs) < 3) {
                    var_dump($k);
                    continue;
                }

                $strs = array_pad($strs, 6, null);

                if (preg_match('~^(.+?)_(\d+[_]?)+$~', $k, $match)) {
                    [$_, $table, $id] = $match;
                    $output[$table][$id] = $strs;
                } else {
                    if (empty($k) || ctype_digit($k))
                        continue;

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

            $dir = DATA_OUTPUT_DIR . "/{$region}/strings";
            if (!is_dir($dir))
                mkdir($dir, 0777, true);

            foreach ($output as $file => $strings) {
                ksort($strings);

                file_put_contents(
                    "{$dir}/{$file}.json",
                    json_encode((object) $strings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
                );
            }
            break;


        case "jp":
            break;
    }