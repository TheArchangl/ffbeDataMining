<?php
    /**
     * User: aEnigma
     * Date: 23.08.2017
     * Time: 11:47
     */

    require_once dirname(__DIR__) . "/bootstrap.php";

    $dictionary = [];

    $files = glob(DATA_INPUT_DIR . \Sol\FFBE\GameFile::getRegion() . "/F_TEXT_*");
     var_dump($files);

    foreach ($files as $file) {
    }
