<?php
    /**
     * User: aEnigma
     * Date: 29.06.2017
     * Time: 12:58
     */

    require_once dirname(__DIR__) . "/bootstrap.php";
    //
    // $files = glob(__DIR__ . "/dat_enc/*/Ver*.*");
    //
    // foreach ($files as $file) {
    //     var_dump($file);
    //     $name = basename($file);
    //     $dir  = dirname($file);
    //     list($version, $hash) = sscanf($name, "Ver%d_%8s");
    //
    //     $entry = \Sol\FFBE\GameFile::getEntry($hash);
    //     // var_dump($name);
    //     // die();
    //     if ($entry == null || empty($entry->getName()) || $entry->getName() == '-')
    //         continue;
    //
    //     $new_name = "{$entry->getName()}_v{$version}.txt";
    //     $new_file = "{$dir}/{$new_name}";
    //     if (file_exists($new_file))
    //         continue;
    //
    //     rename($file, $new_file);
    //     echo "{$file} -> {$dir}/{$new_name}\n";
    // }
    //
    // die();
    // take first version of everything and commit it to git
    $region = 'gl';
    $files  = glob(__DIR__ . "/dat_enc/{$region}/*.txt");
    natsort($files);

    $filevers = [];

    foreach ($files as $file) {
        $name = basename($file);
        $dir  = dirname($file);
        list($filename, $version) = explode('_v', substr($name, 0, -4));

        $filevers[$filename][] = (int)$version;
    }

    // git init before
    chdir(DATA_DECODED_DIR . "/{$region}");
    // shift first version off
    foreach ($filevers as $name => $versions) {
        $first = array_shift($filevers[$name]);
        // $key =  \Sol\FFBE\GameFile::getFileKey($name);
        // if ($key == '-' || empty($key))
        //     continue;
        //
        // \Sol\FFBE\GameFile::decodeFile(
        //     DATA_ENCODED_DIR . "/{$region}/{$name}_v{$first}.txt",
        //     DATA_DECODED_DIR . "{$region}/{$name}.txt",
        //     $key
        // );
    }


    foreach ($filevers as $name => $versions)
        foreach ($versions as $version) {
            $key =  \Sol\FFBE\GameFile::getFileKey($name);
            if ($key == '-' || empty($key))
                continue;

            $outfile = DATA_DECODED_DIR . "{$region}/{$name}.txt";
            \Sol\FFBE\GameFile::decodeFile(
                DATA_ENCODED_DIR . "/{$region}/{$name}_v{$version}.txt",
                $outfile,
                $key
            );

            echo `git add "{$outfile}"`;
            echo `git commit -m "{$name} v{$version}`;
        }