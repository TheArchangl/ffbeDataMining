<?php


    use Solaris\FFBE\AES;

    require_once dirname(__DIR__) . "/bootstrap.php";

    const OVERWRITE = false;
    $client_dir = CLIENT_DIR . "files\\{$region}";

    //
    echo "Updating from client files\n";

    $files   = file_get_contents("{$client_dir}/versions.json");
    $files   = json_decode($files, true);
    $updated = [];
    foreach ($files as $k => $entry) {
        $name = $entry['Name'];
        $ver  = $entry['Version'];

        $path_in  = "{$client_dir}/{$name}.txt";
        $path_out = realpath(DATA_ENCODED_DIR . "/{$region}") . "/{$name}_v{$ver}.txt";


        if (!file_exists($path_in))
            continue;

        $file_entry = \Sol\FFBE\GameFile::getEntry($name);
        if ($file_entry == null) {
            echo "Skipping {$name}\n";
            continue;
        }

        $max = max(\Sol\FFBE\GameFile::getFileVersions($file_entry) ?: [0]);

        if ($ver > $max)
            echo "WARNING {$ver} > {$max} for {$name}\n";

        if (file_exists($path_out))
            continue;

        $data = file_get_contents($path_in);
        $data = AES::encodeGameFile($data, $file_entry->getKey());

        file_put_contents($path_out, $data);

        $updated[] = $name;
    }

    // run decode.php !
    foreach ($updated as $filename) {
        $entry = \Sol\FFBE\GameFile::getEntry($filename);
        $ver   = \Sol\FFBE\GameFile::getFileVersions($entry, $region);
        $ver   = max($ver);

        echo "\t{$entry->getName()} -> v{$ver}\n";

        \Sol\FFBE\GameFile::decodeFile(DATA_ENCODED_DIR . "/{$region}/{$filename}_v{$ver}.txt",  DATA_DECODED_DIR. "/{$region}/{$filename}.txt", $entry->getKey());
    }

    echo "\n";
