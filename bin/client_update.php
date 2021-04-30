<?php

    use Sol\FFBE\GameFile;

    require_once dirname(__DIR__) . '/bootstrap.php';

    const OVERWRITE = false;
    $client_dir = CLIENT_DIR . "files/{$region}";
    $changes    = false;
    // $code       = ['en', 'cn', 'ko', 'fr', 'de', 'es', 'id', 'th',];

    //
    echo "Updating from {$region} client files\n";

    $files = file_get_contents("{$client_dir}/versions.json") or die('File not found');
    $files = json_decode($files, true, 512, JSON_THROW_ON_ERROR);

    foreach ($files as $k => $entry) {
        if ($entry['Type'] === 2)
            continue;

        $name    = $entry['Name'];
        $ver     = $entry['Version'];
        $lang    = $entry['LanguageCode'] ?? '__';

        // Missing GameFile entry
        $file_entry = GameFile::getEntry($name);
        if ($file_entry === null) {
            echo "\tSkipping {$name}\n";
            continue;
        }

        // missing file
        $path_in = isset($entry['Language'])
            ? "{$client_dir}/{$entry['LanguageCode']}/{$name}.txt"
            : "{$client_dir}/{$name}.txt";

        if (! file_exists($path_in)) {
            echo "\tCould not find file for {$name} v{$ver}\n";
            continue;
        }


        // update most recent file
        $mod_time = filemtime($path_in);
        $max_ver  = max(GameFile::getFileVersions($file_entry, $region, $entry['LanguageCode'] ?? '') ?: [0]);
        $path_out = ! empty($entry['LanguageCode'])
            ? DATA_INPUT_DIR . "/{$region}/{$entry['LanguageCode']}/{$name}.txt"
            : DATA_INPUT_DIR . "/{$region}/{$name}.txt";

        if (OVERWRITE || $ver > $max_ver || ! file_exists($path_out) || $mod_time > filemtime($path_out)) {
            if (! is_dir($dir = dirname($path_out)) && ! mkdir($dir, 0777, true) && ! is_dir($dir))
                throw new RuntimeException("Could not create directory '{$dir}'");

            $changes = true;
            echo "\t{$file_entry->getName()} {$lang} -> v{$ver}\n";
            copy($path_in, $path_out);
            touch($path_out, $mod_time);
        }

        // copy to backup
        $path_out = isset($entry['Language'])
            ? DATA_BACKUP_DIR . "/{$region}/{$entry['LanguageCode']}/{$name}_v{$ver}.txt"
            : DATA_BACKUP_DIR . "/{$region}/{$name}_v{$ver}.txt";

        if (OVERWRITE || ! is_file($path_out)) {
            if (! is_dir($dir = dirname($path_out)) && ! mkdir($dir, 0777, true) && ! is_dir($dir))
                throw new RuntimeException("Could not create directory '{$dir}'");

            copy($path_in, $path_out);
            touch($path_out, $mod_time);
        }
    }

    // update or read strings
    if ($changes)
        require_once __DIR__ . '/generate_strings.php';

    else
        require_once __DIR__ . '/read_strings.php';