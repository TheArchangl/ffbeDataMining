<?php
    /**
     * User: aEnigma
     * Date: 26.01.2017
     * Time: 22:02
     */

    use Sol\FFBE\MstList\IconMstList;
    use Sol\FFBE\Reader\SkillReader;
    use Solaris\FFBE\Mst\SkillMstList;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    //ini_set('assert.active', 0);
    ini_set('memory_limit', '2G');
    IconMstList::init();

    // GameFile::setRegion('jp');

    var_dump($container[SkillMstList::class]->getEntry(202790));
    echo "Skills\n";
    $reader = new SkillReader($region, $container[SkillMstList::class]);
    $reader->save(join('/', [
        DATA_OUTPUT_DIR,
        $region,
        'skills.json'
    ]));
