<?php
    /**
     * User: aEnigma
     * Date: 30.07.2019
     * Time: 15:39
     */

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    if ($region != 'gl')
        exit();

    #    \Sol\FFBE\GameFile::setRegion('jp');

    //
    echo "Hidden skills\n";
    $entries = [];
    $reverse = [];

    foreach (\Sol\FFBE\GameFile::loadMst('F_HIDDEN_SKILL_MST') as $row) {
        ['uNQgv5QM' => $hidden_id, 'gil' => $gil_cost, '3V7vwrcM' => $points, 'kjK5eW55' => $skill_id, 'ULAYukCb' => $next] = $row;

        $entries[$hidden_id] = [
            'units'    => [],
            'gil_cost' => (int) $gil_cost,
            'ep_cost'  => (int) $points,
            'skill_id' => (int) $skill_id,
            'next_id'  => empty($next) ? null : (int) $next,
        ];
    }

    foreach (\Sol\FFBE\GameFile::loadMst('F_UNIT_SERIES_HIDDEN_SKILL_MST') as $row) {
        ['unit_id' => $unit_id, 'uNQgv5QM' => $hidden_id] = $row;

        if (!isset($entries[$hidden_id]))
            throw new LogicException();

        $entries[$hidden_id]['units'][] = (int) $unit_id;
    }

    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/unit_latent_skills.json", toJSON($entries));