<?php

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Updater;
    use Sol\FFBE\UpdaterJP;
    use Solaris\FFBE\Client\ClientGL;
    use Solaris\FFBE\Helper\Environment;

    require_once dirname(__DIR__) . '/bootstrap.php';

    $region ??= 'gl';
    //$region = 'gl';
    // $region = 'jp';
    $find_ver = true;
    #$find_ver = false;


    switch ($region) {
        case 'gl':
            $temp_dir  = CLIENT_DIR . '/files';
            $client_gl = new ClientGL("{$temp_dir}/gl");
            Environment::setInstance($client_gl);

            $updater = new Updater($client_gl, DATA_BACKUP_DIR . '/gl');
            $files   = [
                'F_TEXT_DESCRIPTION_FORMAT_1:en',
                'F_TEXT_DESCRIPTION_FORMAT_2:en',
                'F_TEXT_DESCRIPTION_FORMAT_3:en',
                'F_TEXT_DESCRIPTION_FORMAT_4:en',
                'F_TEXT_DESCRIPTION_FORMAT_5:en',
                'F_TEXT_DESCRIPTION_FORMAT_6:en',
            ];
            break;

        case 'jp':
            $updater = new UpdaterJP(DATA_BACKUP_DIR . '/jp');
            $files   = [
                // 'F_MISSION_MST'
                // 'PCm9K3no', // F_AI_MST
                // "wJ>E09yxD", // F_MONSTER_SKILL_MST
                // "B9K8ULHc", // F_MONSTER_SKILL_SET_MST
                // 'F_SACRIFICE_MST',
            ];
            break;

        default:
            throw new \LogicException('No region');
    }

    // download
    GameFile::init();
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
        $key = GameFile::getFileKey($name);
        if ($key === '-' || empty($key))
            continue;

        foreach ($versions as $version) {
            $infile  = DATA_ENCODED_DIR . "/{$region}/{$name}_v{$version}.txt";
            $outfile = DATA_BACKUP_DIR . "/{$region}/{$name}_v{$version}.txt";

            if (! file_exists($infile))
                continue;

            GameFile::decodeFile(
                $infile,
                $outfile,
                $key
            );
        }
    }
