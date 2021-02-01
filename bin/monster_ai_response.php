<?php
    /**
     * User: aEnigma
     * Date: 17.02.2017
     * Time: 23:01
     */

    namespace Sol\FFBE;

    assert_options(ASSERT_ACTIVE, true);
    assert_options(ASSERT_EXCEPTION, false);

    use Sol\FFBE\Reader\MissionResponseReader;
    use Solaris\FFBE\Mst\SkillMstList;

    require_once dirname(__DIR__) . '/bootstrap.php';
    require_once __DIR__ . '/../helpers.php';
    $region     = 'gl';
    $mission_id = '935*';

    // setup
    $max_num = 50;

    // read data
    $files = glob(CLIENT_DIR . "missions/{$region}/*/{$mission_id}/*");
    if (empty($files))
        die ('No file found');

    GameFile::setRegion($region);

    // newest first
    natsort($files);
    rsort($files);

    // limit to 100 files
    shuffle($files);
    if (count($files) > $max_num)
        $files = array_slice($files, 0, $max_num);

    echo "Reading files\n";

    $missions = [];
    foreach ($files as $file) {
        $data = file_get_contents($file);
        $data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        $data = GameFile::replaceKeysRecursive($data);
        $data = $data['body']['data'] ?? null;
        if ($data == null)
            continue;

        $row = $data['MissionStartRequest'][0];
        $id  = (int) $row['mission_id'];

        $missions[$id][] = $data;
    }

    echo "\tDone.\n\n";

    // update files & read strings
    require_once __DIR__ . '/read_strings.php';

    // output
    $reader  = null;
    $outfile = DATA_OUTPUT_DIR . '/monster_ai_response_result.txt';

    if (is_file($outfile)) {
        $fh = new \SplFileObject($outfile, 'w');
        $fh->ftruncate(0);
        unset($fh);
    }

    echo "\nParsing files\n";

    uksort($missions, 'strnatcmp');
    foreach ($missions as $mission_id => $entries) {
        $reader = new MissionResponseReader($region, $container[SkillMstList::class]);
        foreach ($entries as $data)
            print($reader->readResponse($data) . PHP_EOL);

        $reader->saveOutput($outfile, true, true);
        $reader->saveOutput(ROOT_DIR . "/ai/{$reader->getMissionID()}.{$region}.py");
    }

    #if ($reader instanceof MissionResponseReader)
    #    $reader->saveMonsterSkills(DATA_OUTPUT_DIR . '/monster_skills.json');

    echo "\tDone.\n";