<?php
    /**
     * User: aEnigma
     * Date: 24.01.2017
     * Time: 17:39
     */

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    $region = $region ?? 'gl';
    $lang   = 0; // EN

    // unit data
    $units    = [];
    $unit_map = [];


    echo "Units\n";
    $reader = new \Sol\FFBE\Reader\UnitReader($region);
    $reader->save(join('/', [
        DATA_OUTPUT_DIR,
        $region,
        'units.json'
    ]));


    echo "Limitbursts\n";
    $reader = new \Sol\FFBE\Reader\LimitburstReader($region, $container[\Solaris\FFBE\Mst\SkillMstList::class]);
    $reader->save(join('/', [
        DATA_OUTPUT_DIR,
        $region,
        'limitbursts.json'
    ]));
