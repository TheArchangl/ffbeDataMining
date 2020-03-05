<?php

    use Sol\FFBE\GameFile;

    require_once dirname(__DIR__) . '/bootstrap.php';

    const OVERWRITE = false;
    $client_dir = CLIENT_DIR . "files/{$region}";
    // $code       = ['en', 'cn', 'ko', 'fr', 'de', 'es', 'id', 'th',];

    //
    echo "Updating from {$region} client files\n";

    $updated = [];
    $files = file_get_contents("{$client_dir}/versions.json") or die();
    $files = json_decode($files, true, 512, JSON_THROW_ON_ERROR);
    foreach ($files as $k => $entry) {
        if ($entry['Type'] == 2)
            continue;

        $name    = $entry['Name'];
        $ver     = $entry['Version'];
        $path_in = isset($entry['Language'])
            ? "{$client_dir}/{$entry['LanguageCode']}/{$name}.txt"
            : "{$client_dir}/{$name}.txt";

        if (! file_exists($path_in)) {
            echo "\tCould not find file for {$name} v{$ver}\n";
            continue;
        }


        $file_entry = GameFile::getEntry($name);
        if ($file_entry == null) {
            echo "\tSkipping {$name}\n";
            continue;
        }

        $lang = $entry['LanguageCode'] ?? '__';
        // update most recent file
        $mod_time = filemtime($path_in);
        $max_ver  = max(GameFile::getFileVersions($file_entry, $region, $entry['LanguageCode'] ?? '') ?: [0]);
        $path_out = ! empty($entry['LanguageCode'])
            ? DATA_INPUT_DIR . "/{$region}/{$entry['LanguageCode']}/{$name}.txt"
            : DATA_INPUT_DIR . "/{$region}/{$name}.txt";

        if (! is_dir($dir = dirname($path_out)) && ! mkdir($dir, 0777, true) && ! is_dir($dir))
            throw new RuntimeException("Could not create directory '{$dir}'");

        if (OVERWRITE || $ver > $max_ver || ! file_exists($path_out) || $mod_time > filemtime($path_out)) {
            echo "\t{$file_entry->getName()} {$lang} -> v{$ver}\n";
            copy($path_in, $path_out);
            touch($path_out, $mod_time);
        }

        // copy to backup
        $path_out = isset($entry['Language'])
            ? DATA_BACKUP_DIR . "/{$region}/{$entry['LanguageCode']}/{$name}_v{$ver}.txt"
            : DATA_BACKUP_DIR . "/{$region}/{$name}_v{$ver}.txt";

        if (! is_dir($dir = dirname($path_out)) && ! mkdir($dir, 0777, true) && ! is_dir($dir))
            throw new RuntimeException("Could not create directory '{$dir}'");

        if (OVERWRITE || ! is_file($path_out)) {
            copy($path_in, $path_out);
            touch($path_out, $mod_time);
        }
    }