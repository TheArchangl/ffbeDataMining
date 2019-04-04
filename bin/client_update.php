<?php

    require_once dirname(__DIR__) . "/bootstrap.php";

    const OVERWRITE = false;
    $client_dir = CLIENT_DIR . "files\\{$region}";

    //
    echo "Updating from {$region} client files\n";

    $updated = [];
    $files   = file_get_contents("{$client_dir}/versions.json");
    $files   = json_decode($files, true);
    foreach ($files as $k => $entry) {
        $name = $entry['Name'];
        $ver  = $entry['Version'];

        $path_in = "{$client_dir}/{$name}.txt";
        if (!file_exists($path_in)) {
            echo "Could not find file for {$name} v{$ver}\n";
            continue;
        }

        $file_entry = \Sol\FFBE\GameFile::getEntry($name);
        if ($file_entry == null) {
            echo "Skipping {$name}\n";
            continue;
        }

        // copy to backup
        $mod_time = filemtime($path_in);
        $path_out = DATA_BACKUP_DIR . "/{$region}/{$name}_v{$ver}.txt";
        if (OVERWRITE || !is_file($path_out)) {
            copy($path_in, $path_out);
            touch($path_out, $mod_time);
            $updated = true;
        }

        // update most recent file
        $max_ver  = max(\Sol\FFBE\GameFile::getFileVersions($file_entry) ?: [0]);
        $path_out = DATA_INPUT_DIR . "/{$region}/{$name}.txt";

        if ((OVERWRITE || !is_file($path_out) && $ver >= $max_ver)) {
            echo "\t{$file_entry->getName()} -> v{$ver}\n";
            copy($path_in, $path_out);
            touch($path_out, $mod_time);
        }
    }