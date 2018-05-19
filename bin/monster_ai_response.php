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
    $region     = 'gl';
    $mission_id = (8991106);

    require_once __DIR__ . "/client_update.php";

    // read data
    GameFile::setRegion($region);

    $files = glob(CLIENT_DIR . "missions\\{$region}\\*\\{$mission_id}\\*");
    if (empty($files))
        die ("No file found");

    $entries = array_map("file_get_contents", $files);
    $entries = array_map(function ($entry) { return json_decode($entry, true); }, $entries);
    $entries = GameFile::replaceKeysRecursive($entries);

    foreach (glob('C:\Users\aEnigma\Desktop\client\files\gl\F_TEXT_*.txt') as $file)
        Strings::readFile($file);

    foreach (glob(DATA_DECODED_DIR . "gl/F_TEXT_*.txt") as $file)
        Strings::readFile($file);

    // output
    $reader = new MissionResponseReader($region, $container[\Solaris\FFBE\Mst\SkillMstList::class]);
    foreach ($entries as $entry)
        if (isset($entry['body']['data']))
            $reader->readResponse($entry['body']['data']);


    $reader->saveOutput(DATA_OUTPUT_DIR . "/monster_ai_response_result.txt", true);
    $reader->saveMonsterSkills(DATA_OUTPUT_DIR . '/monster_skills.json');