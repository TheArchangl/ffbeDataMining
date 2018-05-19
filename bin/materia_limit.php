<?php
    /**
     * User: aEnigma
     * Date: 26.01.2017
     * Time: 22:02
     */

    require_once "keys.php";
    require_once "Strings.php";
    require_once dirname(__DIR__) . "/helpers.php";

    $key  = 'CVERk9KN';
    $file = 'BX0pRc8A';

    $materia = file_get_contents(__DIR__ . '/data/materia.json');
    $materia = json_decode($materia, true);

    $unique = [];
    foreach (readGameFile($file) as $entry) {
        $id    = $entry['materia_id'];
        $limit = $entry['limit'];

        $unique[] = [$materia[$id]['name'], $limit];
    }

    $string = json_encode($unique);
    $string = str_replace('],', "],\n\t", $string);

    echo $string;