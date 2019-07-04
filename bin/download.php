<?php

    require_once dirname(__DIR__) . "/bootstrap.php";

    //$region = 'gl';
    // $region = 'jp';
    // $find_ver = true;
    $find_ver = false;

    switch ($region) {
        case 'gl':
            $updater = new \Sol\FFBE\Updater(DATA_ENCODED_DIR . "/gl");
            $files   = [
                'F_TEXT_MONSTER_SKILL_SET_NAME'
            ];
            break;

        case 'jp':
            $updater = new \Sol\FFBE\UpdaterJP(DATA_ENCODED_DIR . "/jp");
            $files   = [
                // 'F_MISSION_MST'
                // 'PCm9K3no', // F_AI_MST
                // "wJ>E09yxD", // F_MONSTER_SKILL_MST
                // "B9K8ULHc", // F_MONSTER_SKILL_SET_MST
                // 'F_SACRIFICE_MST',
            ];
            break;

        default:
            throw new \LogicException("No region");
    }

    // download
    $result = $updater->run($files, $find_ver);
    // $result = [
    //     'F_BEAST_EXPLAIN_MST' => [9],
    //     'F_TEXT_AWARD_NAME' => [37],
    //     'F_TEXT_BATTLE_SCRIPT' => [57],
    //     'F_TEXT_EXPN_STORY' => [4],
    // ];

    // if ($result != [])
    //    \Sol\FFBE\GameFile::decodeAll($region);
    // exit;

    // decode
    chdir(DATA_INPUT_DIR . "/{$region}");
    foreach ($result as $name => $versions) {
        $key = \Sol\FFBE\GameFile::getFileKey($name);
        if ($key == '-' || empty($key))
            continue;

        foreach ($versions as $version) {
            $infile  = DATA_ENCODED_DIR . "/{$region}/{$name}_v{$version}.txt";
            $outfile = DATA_BACKUP_DIR . "/{$region}/{$name}_v{$version}.txt";

            if (!file_exists($infile))
                continue;

            \Sol\FFBE\GameFile::decodeFile(
                $infile,
                $outfile,
                $key
            );

            if ($region == 'gl') {
                // add to git
                echo `git add "{$outfile}"`;
                echo `git commit -m "{$name} v{$version}`;
            }
        }
    }
