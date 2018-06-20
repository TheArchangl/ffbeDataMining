<?php
    /**
     * User: aEnigma
     * Date: 04.02.2017
     * Time: 17:11
     */

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\Mst\MonsterSkillMstList;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";
    require_once "../../ffbe-discord/tmp/init_strings.php";


    $mskills = new MonsterSkillMstList();
    $mskills->readFile();

    $meta = new \Solaris\FFBE\Mst\MetaMstList();
    $meta->addList($skills);
    $meta->addList($mskills);

    $entries = [];
    foreach ($mskills->getEntries() as $skill)
        $entries[] = sprintf("[%d] %40s: %s", $skill->id, $skill->getName(), \Solaris\Formatter\SkillFormatter::format($skill, $meta, "; "));

    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/monster_skills.txt", join("\n", $entries));
