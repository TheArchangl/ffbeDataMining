<?php
    /**
     * User: aEnigma
     * Date: 17.02.2017
     * Time: 22:50
     */

    ini_set('serialize_precision', -1);
    ini_set('zend.assertions', false);

    use Sol\FFBE\GameFile;
    use Solaris\FFBE\Client\ClientGL;
    use Solaris\FFBE\Client\ClientJP;
    use Solaris\FFBE\Mst\AbilitySkillMst;
    use Solaris\FFBE\Mst\SkillMstList;
    const ROOT_DIR         = __DIR__;
    const DATA_ENCODED_DIR = __DIR__ . "/dat_enc/";
    const DATA_DECODED_DIR = __DIR__ . "/dat_raw/";
    const DATA_OUTPUT_DIR  = __DIR__ . "/data/";
    const CLIENT_DIR       = 'C:\Arbeit\FFBE\client\\';

    require_once __DIR__ . "/vendor/autoload.php";

    \Sol\FFBE\GameFile::init();

    // strings
    $files = [
        'F_TEXT_ABILITY_NAME',
        'F_TEXT_MAGIC_NAME',
        'F_TEXT_LIMIT_BURST_NAME',
        'F_TEXT_ITEM_NAME',
        'F_TEXT_ITEM_EQUIP_NAME',
        'F_TEXT_TRIBE',
        'F_TEXT_BEAST_NAME',
        'F_TEXT_UNITS_NAME'
    ];

    foreach ($files as $filename)
        \Solaris\FFBE\Helper\Strings::readFile(GameFile::getFilePath($filename));

    // region
    $region = 'gl';
    if (strtolower($argv[1] ?? $region) == 'jp')
        $region = 'jp';

    GameFile::setRegion($region);

    $container = new \Pimple\Container();
    $container['region'] = $region;

    // fix Skills
    $container[SkillMstList::class] = function () use ($region) {
        $ability_mst    = new \Solaris\FFBE\Mst\AbilityMstList();
        $magic_mst      = new \Solaris\FFBE\Mst\MagicMstList();
        $limitburst_mst = new \Solaris\FFBE\Mst\LimitBurstMstList();

        $skill_mst = new \Solaris\FFBE\Mst\MetaMstList($region == 'gl' ? new ClientGL("", false) : new ClientJP('', false));
        $skill_mst->addList($ability_mst);
        $skill_mst->addList($magic_mst);
        $skill_mst->addList($limitburst_mst);

        print "Reading Skills\n";
        $ability_mst->readFile(GameFile::getFilePath('F_ABILITY_MST'));
        $magic_mst->readFile(GameFile::getFilePath('F_MAGIC_MST'));
        $limitburst_mst->readFile(GameFile::getFilePath('F_LIMITBURST_MST'));

        SkillMstList::processEffects($skill_mst);

        print "\tDone\n";

        return $skill_mst;
    };

