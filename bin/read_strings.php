<?php


    use Sol\FFBE\Strings;

    require_once __DIR__ . '/../bootstrap.php';

    echo "Reading strings\n";

    $files = glob(ROOT_DIR . "/strings/{$region}/*.json");
    foreach ($files as $file) {
        $name = basename($file, '.json');
        $data = file_get_contents($file);
        $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

        if (in_array($name, ['manual', 'misc']))
            foreach ($data as $k => $row)
                Strings::setEntry($k, (array) $row);


        else
            foreach ($data as $k => $row) {
                Strings::setEntry("{$name}_{$k}", (array) $row);
            }
    }

    echo "\tDone.\n";