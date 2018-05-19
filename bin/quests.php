<?php
    /**
     * User: aEnigma
     * Date: 11.02.2017
     * Time: 03:18
     */


    require_once "keys.php";
    require_once "Strings.php";
    require_once dirname(__DIR__) . "/helpers.php";

    $files = getFileList();

    $entries = readGameFile('2Px75LpY'); // quest
    $entries = readGameFile('myGc0U5v'); // quest sub
