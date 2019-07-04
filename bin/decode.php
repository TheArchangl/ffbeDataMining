<?php

    use Sol\FFBE\GameFile;

    require_once dirname(__DIR__) . "/bootstrap.php";

    // Git!
    GameFile::decodeAll($region);


    // die();
    //
    // foreach (glob(ROOT_DIR . "/dat_enc/jp/*.dat") as $file) {
    //     $filename = basename($file);
    //     $filename = substr($filename, -12, -4);
    //     $key      = GameFile::getFileKey($filename);
    //
    //     if ($key != null && $key != '-')
    //         GameFile::decodeFile($file, ROOT_DIR . "/dat_dec/jp/{$filename}.dat", $key);
    // }