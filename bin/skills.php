<?php
    /**
     * User: aEnigma
     * Date: 26.01.2017
     * Time: 22:02
     */

    use Sol\FFBE\MstList\IconMstList;
    use Sol\FFBE\Reader\LimitburstReader;
    use Sol\FFBE\Reader\SkillReader;
    use Solaris\FFBE\Mst\SkillMstList;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    //ini_set('assert.active', 0);
    ini_set('memory_limit', '2G');
    IconMstList::init();

    // GameFile::setRegion('jp');

    echo "Skills\n";
    $reader = new SkillReader($region, $container[SkillMstList::class]);
    $reader->saveAbilities(join('/', [DATA_OUTPUT_DIR, $region, 'skills_ability.json']));
    $reader->savePassives(join('/', [DATA_OUTPUT_DIR, $region, 'skills_passive.json']));
    $reader->saveMagic(join('/', [DATA_OUTPUT_DIR, $region, 'skills_magic.json']));

    //
    echo "Limitbursts\n";
    $reader = new LimitburstReader($region, $container[SkillMstList::class]);
    $reader->save(join('/', [
        DATA_OUTPUT_DIR,
        $region,
        'limitbursts.json'
    ]));