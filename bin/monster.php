<?php
    /**
     * User: aEnigma
     * Date: 04.02.2017
     * Time: 17:11
     */

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\GameHelper;
    use Solaris\FFBE\Mst\MetaMstList;
    use Solaris\FFBE\Mst\MonsterSkillMstList;
    use Solaris\Formatter\SkillFormatter;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";
    require_once dirname(__DIR__) . "/../ffbe-discord/tmp/init_strings.php";

    /*
        // skills
        $mskills = new MonsterSkillMstList();
        $mskills->readFile();

        $meta = new MetaMstList();
        $meta->addList($skills);
        $meta->addList($mskills);

        $entries = [];
        foreach ($mskills->getEntries() as $skill)
            $entries[] = sprintf("[%d] %40s: %s", $skill->id, $skill->getName(), SkillFormatter::format($skill, $meta, "; "));

        file_put_contents(DATA_OUTPUT_DIR . "/{$region}/monster_skills.", join("\n", $entries));
    */

    // dictionary
    $entries = [];
    foreach (GameFile::loadMst('F_MONSTER_DICTIONARY_MST') as $row) {
        $id     = (int) $row['monster_id'];
        $name   = Strings::getString('MST_MONSTERDICTIONARY_NAME', $id) ?? $row['name'];
        $tribes = GameHelper::readIntArray($row['tribe_id']);
        $tribes = array_map(function ($tribe_id) { return \Solaris\FFBE\Helper\Strings::getString('MST_TRIBE_NAME', $tribe_id); }, $tribes);

        $entries[$id] = [
            'name'   => $name,
            'index'  => (int) $row['order_index'],
            'listed' => $row['DispDict'] == 1,
            'races'  => $tribes,
        ];
    }

    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/monster_dict.json", toJSON($entries, true));

