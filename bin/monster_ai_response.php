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


    // setup
    $max_num    = 50;
    $region     = 'jp';
    $mission_id = "9091102";

    require_once __DIR__ . "/client_update.php";

    // read data
    GameFile::setRegion($region);

    $files = glob(CLIENT_DIR . "missions\\{$region}\\*\\{$mission_id}\\*");

    if (empty($files))
        die ("No file found");

    // newest first
    natsort($files);
    rsort($files);


    // limit to 100 files
    if (count($files) > $max_num) {
        shuffle($files);
        $files = array_slice($files, 0, $max_num);
    }

    $entries = array_map("file_get_contents", $files);
    $entries = array_map(function ($entry) { return json_decode($entry, true); }, $entries);
    $entries = GameFile::replaceKeysRecursive($entries);

    // update ai etc
    require_once __DIR__ . "/client_update.php";

    // load strings
    foreach (glob(CLIENT_DIR . '\files\gl\F_TEXT_*.txt') as $file) {
        if (strpos($file, 'MONSTER_SKILL_SET_NAME') !== false)
            continue;

        Strings::readFile($file);
    }

    // output
    $reader = new MissionResponseReader($region, $container[\Solaris\FFBE\Mst\SkillMstList::class]);
    foreach ($entries as $entry)
        if (isset($entry['body']['data']))
            $reader->readResponse($entry['body']['data']);


    $reader->saveOutput(DATA_OUTPUT_DIR . "/monster_ai_response_result.txt", true);
    $reader->saveMonsterSkills(DATA_OUTPUT_DIR . '/monster_skills.json');