<?php
    /**
     * User: aEnigma
     * Date: 17.02.2017
     * Time: 22:50
     */

    ini_set('serialize_precision', -1);
    ini_set('zend.assertions', false);
    ini_set('memory_limit', '2G');

    use Sol\FFBE\GameFile;
    use Solaris\FFBE\Client\ClientGL;
    use Solaris\FFBE\Client\ClientJP;
    use Solaris\FFBE\Mst\SkillMstList;

    const ROOT_DIR         = __DIR__;
    const DATA_BACKUP_DIR  = __DIR__ . "/dat_bak/";
    const DATA_ENCODED_DIR = __DIR__ . "/dat_enc/";
    const DATA_INPUT_DIR   = __DIR__ . "/dat_dec/";
    const DATA_OUTPUT_DIR  = __DIR__ . "/data/";
    const CLIENT_DIR       = 'C:\Arbeit\FFBE\client\\';

    require_once __DIR__ . "/vendor/autoload.php";

    \Sol\FFBE\GameFile::init();
    $container = new \Pimple\Container();

    // region
    $region = 'gl';
    if (strtolower($argv[1] ?? $region) == 'jp')
        $region = 'jp';

    GameFile::setRegion($region);

    $container           = new \Pimple\Container();
    $container['region'] = $region;

    // assert dirs exist
    foreach ([DATA_BACKUP_DIR, DATA_ENCODED_DIR, DATA_INPUT_DIR, DATA_OUTPUT_DIR] as $dir)
        if (!is_dir("{$dir}/$region"))
            mkdir("{$dir}/$region", 0777, true);

    // fix Skills
    $container[SkillMstList::class] = function () use ($region) {
        // strings, register hints
        $files = [
            'MST_ABILITY_NAME'     => 'F_TEXT_ABILITY_NAME',
            'MST_MAGIC_NAME'       => 'F_TEXT_MAGIC_NAME',
            'MST_LIMIT_BURST_NAME' => 'F_TEXT_LIMIT_BURST_NAME',
            'MST_ITEM_NAME'        => 'F_TEXT_ITEM_NAME',
            'MST_ITEM_EQUIP_NAME'  => 'F_TEXT_ITEM_EQUIP_NAME',
            'MST_TRIBE'            => 'F_TEXT_TRIBE',
            'MST_BEAST_NAME'       => 'F_TEXT_BEAST_NAME',
            'MST_UNITS_NAME'       => 'F_TEXT_UNITS_NAME',
        ];

        foreach ($files as $table => $filename)
            \Sol\FFBE\Strings::readTable($table);

        $ability_mst    = new \Solaris\FFBE\Mst\AbilityMstList();
        $magic_mst      = new \Solaris\FFBE\Mst\MagicMstList();
        $limitburst_mst = new \Solaris\FFBE\Mst\LimitBurstMstList();

        $client = $region == 'gl'
            ? new ClientGL('', false)
            : new ClientJP('', false);

        $skill_mst = new \Solaris\FFBE\Mst\MetaMstList($client);
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

    class Env {
        public static $container;
    }

    Env::$container = $container;