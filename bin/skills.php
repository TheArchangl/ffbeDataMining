<?php
    /**
     * User: aEnigma
     * Date: 26.01.2017
     * Time: 22:02
     */

    use Sol\FFBE\MstList\IconMstList;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    //ini_set('assert.active', 0);
    ini_set('memory_limit', '2G');
    IconMstList::init();

    // GameFile::setRegion('jp');

    echo "Skills\n";
    $reader = new \Sol\FFBE\Reader\SkillReader($region, $container[\Solaris\FFBE\Mst\SkillMstList::class]);
    $reader->save(join('/', [
        DATA_OUTPUT_DIR,
        $region,
        'skills.json'
    ]));
