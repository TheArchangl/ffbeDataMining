<?php
    /**
     * User: aEnigma
     * Date: 17.02.2017
     * Time: 23:01
     */

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Reader\MissionResponseReader;
    use Solaris\FFBE\Helper\Strings;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    GameFile::setRegion('jp');
    // input
    $monsters = [
        // temp
        [
            'monster_id'  => 900010780 ,
            'skillset_id' => 900010780 ,
            'ai_id'       => 900010780 ,
        ],
        // 3* Levi?
//        [
//            'monster_id'  => 503001102,
//            'skillset_id' => 503001102,
//            'ai_id'       => 503001102,
//            'name'        => 'Leviathan'
//        ]
        // // xmas Gingerbread
        // [
        //     'monster_id'  => 900010382,
        //     'skillset_id' => 900000032,
        //     'ai_id'       => 900010382,
        // ],
        // [
        //     'monster_id'  => 900010386,
        //     'skillset_id' => 900000033,
        //     'ai_id'       => 900010386,
        // ],
        // // FF4 event
        // [
        //     'monster_id'  => 307081001,
        //     'skillset_id' => 307081001 ,
        //     'ai_id'       => 307081001 ,
        // ],
        // [
        //     'monster_id'  => 900010281,
        //     'skillset_id' => 900000025 ,
        //     'ai_id'       => 10000013 ,
        // ],
        // [
        //     'monster_id'  => 900010282,
        //     'skillset_id' => 900000026 ,
        //     'ai_id'       => 10000012 ,
        // ],
        // Halloween
        // [
        //     'monster_id'  => 900010340,
        //     'skillset_id' => 900010340 ,
        //     'ai_id'       => 900010340 ,
        // ],
        // [
        //     'monster_id'  => 900010339,
        //     'skillset_id' => 900010339 ,
        //     'ai_id'       => 900010339 ,
        // ],
        // // dunno anymore
        // [
        //     'monster_id'  => 404021,
        //     'skillset_id' => 404021001,
        //     'ai_id'       => 404021001,
        // ],
        // // 3* Espers
        // [
        //     'monster_id'  => 205511,
        //     'skillset_id' => 205511002,
        //     'ai_id'       => 205511002,
        // ],
        // [
        //     'monster_id'  => 405031,
        //     'skillset_id' => 405031002,
        //     'ai_id'       => 405031002,
        // ],
        // // Grand Marlboro
        // [
        //     'monster_id'  => 900000,
        //     'skillset_id' => 900000022,
        //     'ai_id'       => 900000019,
        //     'name' => 'Great Malboro'
        // ],
        // [
        //     'monster_id'  => 900000,
        //     'skillset_id' => 900000023,
        //     'ai_id'       => 900000020,
        //     'name' => 'Small Malboro'
        // ],
        // // Grand Seraph
        // [
        //     'monster_id'  => 304121,
        //     'skillset_id' => 304121000,
        //     'ai_id'       => 304121000,
        // ],
        // [
        //     'monster_id'  => 304052,
        //     'skillset_id' => 304052001,
        //     'ai_id'       => 304052003,
        // ],
        // [
        //     'monster_id'  => 304052,
        //     'skillset_id' => 304052001,
        //     'ai_id'       => 304052004,
        // ],
        // // Phoenix
        // [
        //     'monster_id'  => 302041,
        //     'skillset_id' => 302041000 ,
        //     'ai_id'       => 302041000 ,
        //     'name' => 'Phoenix'
        // ],
        // // Ramuh, Shiva JP
        // [
        //     'monster_id'  => 401011,
        //     'skillset_id' => 401011001,
        //     'ai_id'       => 401011001,
        // ],
        // [
        //     'monster_id'  => 20502,
        //     'skillset_id' => 205021002,
        //     'ai_id'       => 205021002,
        // ],
        // [
        //     'monster_id'  => 20503,
        //     'skillset_id' => 205031002,
        //     'ai_id'       => 205031002,
        // ],
        // // BDFE JP
        // [
        //     'monster_id'  => 403041000,
        //     'skillset_id' => 403041000,
        //     'ai_id'       => 403041000 ,
        //     // 'name'        => 'Cid 1',
        // ],
        // // Cid
        // [
        //     'monster_id'  => 105141000,
        //     'skillset_id' => 105141000,
        //     'ai_id'       => 105141000 ,
        //     'name'        => 'Cid 1',
        // ],
        // [
        //     'monster_id'  => 105141001,
        //     'skillset_id' => 105141001,
        //     'ai_id'       => 105141001,
        //     'name'        => 'Cid 2',
        // ],
        // [
        //     'monster_id'  => 304101000,
        //     'skillset_id' => 304101000,
        //     'ai_id'       => 304101000,
        //     // 'name'        => 'Cid 2',
        // ],

        #region old
        //
        // // Mog King
        // [
        //     'monster_id'  => 900000021,
        //     'skillset_id' => 900000021,
        //     'ai_id'       => 900000018,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 900000018, 0),
        // ],
        // JP 3*
        // [
        //     'name' => 'Siren 3*',
        //     'monster_id'  => 202001002,
        //     'skillset_id' => 202001002,
        //     'ai_id'       => 202001002,
        // ],
        // [
        //     'name' => 'Ifrit 3*',
        //     'monster_id'  => 301021001,
        //     'skillset_id' => 301021001,
        //     'ai_id'       => 301021001,
        // ],
        // // Mog King
        // [
        //     'monster_id'  => 508001000,
        //     'skillset_id' => 508001000,
        //     'ai_id'       => 508001000,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 508001000, 0),
        // ],
        // [
        //     'monster_id'  => 4050510,
        //     'skillset_id' => 405051000,
        //     'ai_id'       => 405051000,
        //     'name'        => 'Gilgamesh'
        // ],
        // [
        //     'monster_id'  => 501001,
        //     'skillset_id' => 501001003,
        //     'ai_id'       => 501001003,
        //     'name'        => 'Ifrit Raid 3',
        // ],
        // // Red chocobo
        // [
        //     'monster_id'  => 202033,
        //     'skillset_id' => 202033000,
        //     'ai_id'       => 202033000,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 202033000, 0),
        // ],
        // // // Ifrit & Siren
        //     [
        //     'monster_id'  => 301028,
        //     'skillset_id' => 301028000,
        //     'ai_id'       => 301028000,
        // ],
        // [
        //     'monster_id'  => 202008,
        //     'skillset_id' => 202008000,
        //     'ai_id'       => 202008000,
        // ],
        // [
        //     'monster_id'  => 3080414,
        //     'skillset_id' => 3080410,
        //     'ai_id'       => 3080410,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 3080410, 0),
        // ],
        // [
        //     'monster_id'  => 305081000,
        //     'skillset_id' => 305081000,
        //     'ai_id'       => 305081000,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 305081000, 0),
        // ],
        // // Fire dragon
        // [
        //     'monster_id'  => 407016000,
        //     'skillset_id' => 407016000,
        //     'ai_id'       => 407016000,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 407016000, 0),
        // ],
        // // Fake Antenolla
        // [
        //     'monster_id'  => 411002001,
        //     'skillset_id' => 411002001,
        //     'ai_id'       => 411002005,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 411002001, 0),
        // ],
        // [
        //     'monster_id'  => 411002001,
        //     'skillset_id' => 411002002,
        //     'ai_id'       => 411002006,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 411002002, 0),
        // ],
        // [
        //     'monster_id'  => 411002001,
        //     'skillset_id' => 411002003,
        //     'ai_id'       => 411002007,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 411002003, 0),
        // // ],
        // // Bahamut ELT
        // [
        //     'monster_id'  => 407031003,
        //     'skillset_id' => 407031003,
        //     'ai_id'       => 407031001,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 407031003, 0),
        // ],
        // // Bahamut Trial
        // [
        //     'monster_id'  => 407031004,
        //     'skillset_id' => 407031004,
        //     'ai_id'       => 407031002,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 407031003, 0),
        // ],
        // // Echidna
        // [
        //     'monster_id'  => 900010231,
        //     'skillset_id' => 900000019,
        //     'ai_id'       => 900000017,
        //     // 'ai_id'       => 411002008,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 900010231, 0),
        // ]
        // // Mikazuchi & Dragon
        // [
        //     'monster_id'  => 407051000,
        //     'skillset_id' => 407051000,
        //     'ai_id'       => 407051000,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 407051000, 0),
        // ],
        // [
        //     'monster_id'  => 307041000,
        //     'skillset_id' => 307041000,
        //     'ai_id'       => 307041000,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 307041000, 0),
        // ],
        // // Wilhelm
        // [
        //     'monster_id'  => 205371000,
        //     'skillset_id' => 205371000,
        //     'ai_id'       => 205371000,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 205371000, 0),
        // ],
        // [
        //     'monster_id'  => 105035000,
        //     'skillset_id' => 105035000,
        //     'ai_id'       => 105035000,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 105035000, 0),
        // ],
        // // Titan Trial & Heart
        // [
        //     'monster_id'  => 505111004,
        //     'skillset_id' => 505111004,
        //     'ai_id'       => 505111004,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 505111004, 0),
        // ],
        // [
        //     'monster_id'  => 505111100,
        //     'skillset_id' => 505111100,
        //     'ai_id'       => 505111100,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 505111100, 0),
        // ],
//         // 2* Levi JP
//         [
//             'monster_id'  => 503001101,
//             'skillset_id' => 503001101,
//             'ai_id'       => 503001101,
////             'name'        => 'Levi 2*',//Strings::getString('MST_MONSTER_NAME', 505111004, 0),
//         ],
        // // Orthros
        // [
        //     'monster_id'  => 303021001,
        //     'skillset_id' => 303021001,
        //     'ai_id'       => 303021001,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 303021000, 0),
        // ],
        // [
        //     'monster_id'  => 504001001,
        //     'skillset_id' => 504001001,
        //     'ai_id'       => 504001001,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 504001001, 0),
        // ],
        // // Maxwell + Elt
        // [
        //     'monster_id'  => 405061000,
        //     'skillset_id' => 405061000,
        //     // 'skillset_id' => 900000020,
        //     'ai_id'       => 405061000,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 405061000, 0),
        // ],
        // // Robo + Arms
        // [
        //     'monster_id'  => 406058000 ,
        //     'skillset_id' => 406058000 ,
        //     'ai_id'       => 406058000 ,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 406058000 , 0),
        // ],
        // [
        //     'monster_id'  => 406078000 ,
        //     'skillset_id' => 406078000 ,
        //     'ai_id'       => 406078000 ,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 406078000 , 0),
        // ],
        // [
        //     'monster_id'  => 406068000 ,
        //     'skillset_id' => 406068000 ,
        //     'ai_id'       => 406068000 ,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 406068000 , 0),
        // ],
        // [
        //     'monster_id'  => 406078000 ,
        //     'skillset_id' => 406078001 ,
        //     'ai_id'       => 406078001 ,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 406078000 , 0) . " (rezzed)",
        // ],
        // debug
        // [
        //     'monster_id'  => 999016300 ,
        //     'skillset_id' => 999016300 ,
        //     'ai_id'       => 999016300 ,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 999016300 , 0),
        // ],
        // spherimorph?
        // [
        //     'monster_id'  => 308011000 ,
        //     'skillset_id' => 308011000 ,
        //     'ai_id'       => 308011000 ,
        //     'name'        => Strings::getString('MST_MONSTER_NAME', 308011000 , 0),
        // ],
        #endregion
    ];

    // load strings
    foreach (glob('C:\Users\aEnigma\Desktop\client\files\gl\F_TEXT_*.txt') as $file)
        Strings::readFile($file);

    foreach (glob(DATA_INPUT_DIR . "gl/F_TEXT_*.txt") as $file)
        Strings::readFile($file);

    // output
    $data = [
        'MonsterPartsMst' => array_map(
            function ($arr) {
                return [
                    'name'                 => $arr['name']
                        ?? Strings::getString('MST_MONSTER_NAME', $arr['monster_id'])
                        ?? 'Unknown Monster',
                    'monster_unit_id'      => $arr['monster_id'],
                    'monster_parts_num'    => 0,
                    'monster_skill_set_id' => $arr['skillset_id'],
                    'ai_id'                => $arr['ai_id'],
                ];
            }, $monsters)
    ];

    $reader = new MissionResponseReader($region, $container[\Solaris\FFBE\Mst\SkillMstList::class]);
    $reader->readResponse($data);
    $reader->saveOutput(DATA_OUTPUT_DIR . "/monster_ai_result.txt", false);
    //$reader->saveMonsterSkills(__DIR__ . '/data/monster_skills.json');