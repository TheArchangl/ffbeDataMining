<?php

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Reader\MissionResponseReader;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\GameHelper;
    use Solaris\FFBE\Mst\SkillMstList;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    // strings
    Strings::readTable('MST_MONSTER_SKILL_NAME');

    // skillset
    $entries = [];
    foreach (GameFile::loadMst('F_MONSTER_SKILL_SET_MST') as $row) {
        $id     = (int) $row['monster_skill_set_id'];
        $skills = GameHelper::readIntArray(rtrim($row['monster_skill_ids'], ','));

        $entries[$id] = [
            'name'    => $row['name'],
            'monster' => Strings::getString('MST_MONSTER_NAME', $id) ?? Strings::getString('MST_MONSTER_NAME', substr($id, 0, -1) . '0'),
            'skills'  => $skills
        ];
    }

    $data = toJSON($entries, true, true);
    //    $data = preg_replace_callback('~{\s+([^{]+)\s+}~', function ($match) {
    //        return '{' . preg_replace('~\s+~', ' ', $match[1] . '}');
    //    }, $data);
    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/monster_skillsets.json", $data);


    // Monster skills
    $reader = new MissionResponseReader($region, $container[SkillMstList::class], true);
    $reader->readAllSkills(GameFile::loadMst('F_MONSTER_SKILL_MST'));
    $reader->saveMonsterSkills(DATA_OUTPUT_DIR . "/{$region}/monster_skills.json");