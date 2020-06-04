<?php
    /**
     * User: aEnigma
     * Date: 17.02.2017
     * Time: 22:50
     */

    ini_set('serialize_precision', -1);
    @ini_set('zend.assertions', false);
    ini_set('memory_limit', '2G');

    use Pimple\Container;
    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\Client\ClientGL;
    use Solaris\FFBE\Client\ClientJP;
    use Solaris\FFBE\Mst\AbilityMstList;
    use Solaris\FFBE\Mst\LimitBurstMstList;
    use Solaris\FFBE\Mst\MagicMstList;
    use Solaris\FFBE\Mst\MetaMstList;
    use Solaris\FFBE\Mst\SkillMstList;

    const ROOT_DIR         = __DIR__;
    const DATA_BACKUP_DIR  = __DIR__ . '/dat_bak/';
    const DATA_ENCODED_DIR = __DIR__ . '/dat_enc/';
    const DATA_INPUT_DIR   = __DIR__ . '/dat_dec/';
    const DATA_OUTPUT_DIR  = __DIR__ . '/data/';
    const CLIENT_DIR       = '/mnt/c/Arbeit/FFBE/client/'; # 'C:\Arbeit\FFBE\client\\';

    require_once __DIR__ . '/vendor/autoload.php';

    GameFile::init();
    $container = new Container();

    // region
    $region = $region ?? 'gl';
    if (strtolower($argv[1] ?? $region) == 'jp')
        $region = 'jp';

    GameFile::setRegion($region);

    $container           = new Container();
    $container['region'] = $region;

    // assert dirs exist
    foreach ([DATA_BACKUP_DIR, DATA_ENCODED_DIR, DATA_INPUT_DIR, DATA_OUTPUT_DIR] as $dir)
        if (($d = "{$dir}/{$region}") && ! is_dir($d) && ! mkdir($d, 0777, true) && ! is_dir($d))
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $d));


    // fix Skills
    $container[SkillMstList::class] = static function () use ($region) {
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
            Strings::readTable($table);

        $ability_mst    = new AbilityMstList();
        $magic_mst      = new MagicMstList();
        $limitburst_mst = new LimitBurstMstList();

        $client = $region === 'gl'
            ? new ClientGL('', false)
            : new ClientJP('', false);

        $skill_mst = new MetaMstList($client);
        $skill_mst->addList($ability_mst);
        $skill_mst->addList($magic_mst);
        $skill_mst->addList($limitburst_mst);

        print "Reading Skills\n";
        print "\tAbilities\n";
        $ability_mst->readFile();
        print "\tMagic\n";
        $magic_mst->readFile();
        print "\tLimitbursts\n";
        $limitburst_mst->readFile();

        print "\tPostprocessing\n";
        SkillMstList::processEffects($skill_mst);

        print "\tDone\n";

        return $skill_mst;
    };