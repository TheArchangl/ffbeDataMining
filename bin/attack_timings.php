<?php
    /**
     * User: aEnigma
     * Date: 02.03.2017
     * Time: 16:25
     */

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";
    require_once "skills_parse.php";

    // base attack
    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;

    $units = [];
    foreach (GameFile::loadMst('F_UNIT_MST') as $k => $row) {
        $name = Strings::getString('MST_UNIT_NAME', $row['unit_id'], 0);

        $arr = readParameters(str_replace('-', '@', $row['attack_frames']), ':@');

        $frames = [];
        foreach ($arr as $atk)
            $frames[] = $atk[0];

        $last   = array_pop($arr);
        $wait   = $row['Wait'];
                $total  = $last[0] + $wait;
        $frames = implode('-', $frames);

        $units[$name][$row['rarity']] = [$total, $wait, $frames];
    }

    $rows = [];
    foreach ($units as $name => $evos) {
        // all the same?
        $unique = true;
        $entry  = current($evos);
        foreach ($evos as $evo)
            if ($evo[1] != $entry[1]) {
                $unique = false;
                break;
            }


        if ($unique)
            $rows[$entry[0]][$name] = [$entry[1], $entry[2]];

        // $rows[$total][$name] = str_pad("{$name}", 24, ' ') . "\t{$frames}\n";

        else
            foreach ($evos as $rarity => $entry)
                $rows[$entry[0]][$name . " {$rarity}★"] = [$entry[1], $entry[2]];
        // print str_pad("{$name}", 20, ' ') . " {$rarity}★ \t{$frames}\n";
    }

    ksort($rows);
    foreach ($rows as $total => $units)
        foreach ($units as $name => $entry)
            printf("% 3d\t%-20s\t%20s\t% 3s\n", $total, $name, $entry[1], $entry[0]);
    // echo json_encode($units, JSON_PRETTY_PRINT);