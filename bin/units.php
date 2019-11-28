<?php
    /**
     * User: aEnigma
     * Date: 24.01.2017
     * Time: 17:39
     */

    // $region = 'jp';

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

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

    //
    echo "UoL\n";
    $entries = [];
    foreach (\Sol\FFBE\GameFile::loadMst('F_GACHA_SELECT_UNIT_MST') as $row) {
        assert($row['e4mG8jTc'] == 10) or die ("UoC: cost");
        assert($row['date_end'] == "2030-12-31 23:59:59") or die ("UoC: end date");

        //
        $id           = (int) $row['unit_evo_id'];
        $entries[$id] = [
            'name' => \Sol\FFBE\Strings::getString('MST_UNIT_NAME', $id) ?? $reader->getUnit($id)['name'],
            'date' => $row['date_start'],
            //            'date_end'   => $row['date_end'],
        ];
    }

    uksort($entries, function ($a, $b) use ($entries) {
      return $entries[$a]['date'] <=> $entries[$b]['date'] ?: $a <=> $b;
    });

    $file = join('/', [DATA_OUTPUT_DIR, $region, 'unit_selection.json']);
    file_put_contents($file, json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

