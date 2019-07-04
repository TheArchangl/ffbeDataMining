<?php
    /**
     * User: aEnigma
     * Date: 17.02.2017
     * Time: 23:01
     */

    namespace Sol\FFBE;

    use Sol\FFBE\Reader\MissionResponseReader;
    use Solaris\FFBE\Helper\Strings;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once __DIR__ . "/../helpers.php";
    $region     = 'jp';
    $mission_id = "9091109";

    // setup
    $max_num = 50;
    require_once __DIR__ . "/client_update.php";
    require_once __DIR__ . "/generate_strings.php";

    // read data
    GameFile::setRegion($region);

    $files = glob(CLIENT_DIR . "missions\\{$region}\\*\\{$mission_id}\\*");
    if (empty($files))
        die ("No file found");

    // newest first
    natsort($files);
    rsort($files);

    // limit to 100 files
    shuffle($files);
    if (count($files) > $max_num)
        $files = array_slice($files, 0, $max_num);

    $missions = [];
    foreach ($files as $file) {
        $data = file_get_contents($file);
        $data = json_decode($data, true);
        $data = GameFile::replaceKeysRecursive($data);
        $data = $data['body']['data'] ?? null;
        if ($data == null)
            continue;

        $row = $data['MissionStartRequest'][0];
        $id  = (int) $row['mission_id'];

        $missions[$id][] = $data;
    }

    // update ai etc
    require_once __DIR__ . "/client_update.php";

    // load strings
    foreach (glob(CLIENT_DIR . '\files\gl\F_TEXT_*.txt') as $file) {
        if (strpos($file, 'MONSTER_SKILL_SET_NAME') !== false)
            continue;

        Strings::readFile($file);
    }

    // output
    $reader  = null;
    $outfile = DATA_OUTPUT_DIR . "/monster_ai_response_result.txt";

    if (is_file($outfile))
        unlink($outfile);

    uksort($missions, 'strnatcmp');
    foreach ($missions as $mission_id => $entries) {
        $reader = new MissionResponseReader($region, $container[\Solaris\FFBE\Mst\SkillMstList::class]);
        foreach ($entries as $data)
            $reader->readResponse($data);

        $reader->saveOutput($outfile, true, true);
    }

    if ($reader instanceof MissionResponseReader)
        $reader->saveMonsterSkills(DATA_OUTPUT_DIR . '/monster_skills.json');