<?php

    namespace Sol\FFBE;

    use Exception;

    /**
     * User: aEnigma
     * Date: 24.01.2017
     * Time: 19:53
     */
    class Strings {
        const LANGUAGES    = 6;
        const FILE_MAP     = [
            'MST_ABILITY_LONGDESCRIPTION'               => 'uxMPAs4y',
            'MST_ABILITY_NAME'                          => 'eSB5Ry3E',
            'MST_ABILITY_PARAM_MSG'                     => '3oM745kA',
            'MST_ABILITY_SHORTDESCRIPTION'              => '8GJBzdQV',
            'MST_ARCHIVE_NAME_TOTAL_LOGIN'              => 'QR3zeiJr',
            'MST_AREA_NAME'                             => '4qiOcmtJ',
            'MST_AWARD_DESCRIPTION'                     => 'TlrlvFx5',
            'MST_AWARD_NAME'                            => 'mGPBqPdv',
            'MST_AWARD_TYPE'                            => 'soohaWOo',
            'MST_BEASTSKILL_DESCRIPTION'                => 'Yjq14lAB',
            'MST_BEASTSKILL_NAME'                       => 'k51drhZ6',
            'MST_BEAST_NAME'                            => '148O76GI',
            'MST_CAPTURE_INFO'                          => 'Ir2B1EBL',
            'MST_CHALLENGE_NAME'                        => '4bQbA2FH',
            'MST_CHARACTER_NAME'                        => 'JIqrNzje',
            'MST_CLSM_GRADE_NAME'                       => 'Y2rngIF3',
            'MST_CLSM_MONSTER_GROUP_NAME'               => 'q4z5D08C',
            'MST_DAILY_QUEST_COMPLETE'                  => 'vxztyzHJ',
            'MST_DAILY_QUEST_DES'                       => 'W4525rcx',
            'MST_DAILY_QUEST_DETAIL'                    => 'gCfFcx75',
            'MST_DAILY_QUEST_NAME'                      => 'FBBD8vv6',
            'MST_DIAMOND_NAME'                          => 'mcQeT7mq',
            'MST_DUNGEONS_NAME'                         => 'AyVqkz2B', // GPNXLUJP
            'MST_EQUIP_ITEM_LONGDESCRIPTION'            => 'CD4giPVu',
            'MST_EQUIP_ITEM_NAME'                       => 'E0NdslwL',
            'MST_EQUIP_ITEM_SHORTDESCRIPTION'           => 'Nao9HYWk',
            'MST_EXCHANGESHOP_ITEM_NAME'                => 'r1ZLxyyg',
            'MST_GACHA_NAME'                            => 'ZJz5QwAy',
            'MST_GAME_TITLE_NAME'                       => '3CzC5zn7',
            'MST_IMPORTANT_ITEM_LONGDESCRIPTION'        => 'aJ4tvgSq',
            'MST_IMPORTANT_ITEM_NAME'                   => 'TIKwbf3D',
            'MST_IMPORTANT_ITEM_SHOP'                   => 'JO7UqqJ6',
            'MST_IMPORTANT_ITEM_SHORTDESCRIPTION'       => 'YBqAUJt1',
            'MST_ITEM_LONGDESCRIPTION'                  => '9NCIDltW',
            'MST_ITEM_NAME'                             => 'VhkhtvDn',
            'MST_ITEM_SHORTDESCRIPTION'                 => 'IAPS1jOu',
            'MST_JOB_NAME'                              => 'yUkwbFyc',
            'MST_LAND_NAME'                             => 'sKLZVYWQ',
            'MST_LIMITBURST_DESCRIPTION'                => 'EUsG7rlQ',
            'MST_LIMITBURST_NAME'                       => 'XBS8hLZD',
            'MST_MAGIC_LONGDESCRIPTION'                 => 'Jcavjyxo',
            'MST_MAGIC_NAME'                            => '1ZqISaBp',
            'MST_MAGIC_SHORTDESCRIPTION'                => 'Hs9KVVnj',
            'MST_MAP_OBJECT_NAME'                       => '15KaBQci',
            'MST_MATERIA_LONGDESCRIPTION'               => 'QEbXmDTD',
            'MST_MATERIA_NAME'                          => '2Eg5s20D',
            'MST_MATERIA_SHORTDESCRIPTION'              => '8g18k8jD',
            'MST_MISSION_NAME'                          => 'pa6vblsG',
            'MST_MONSTERDICTIONARY_LONGDESCRIPTION'     => 'xe6RH45K',
            'MST_MONSTERDICTIONARY_NAME'                => 'F1VQsGpG',
            'MST_MONSTERDICTIONARY_SHORTDESCRIPTION'    => 'zf7USTwU',
            'MST_MONSTERPARTDICTIONARY_NAME'            => 'P4c5fq2t',
            'MST_MONSTER_NAME'                          => '0xkPiwVI',
            'MST_MONSTER_SKILL_NAME'                    => 'F1z92dkt',
            'MST_MONSTER_SKILL_SET_NAME'                => '76oKZdNU',
            'MST_NPC_NAME'                              => 'A55coosK',
            'MST_PICTURE_STORY_NAME'                    => 'j525zYCH',
            'MST_QUESTSUB_GOAL_DETAIL'                  => 'vb5Nom5d',
            'MST_QUESTSUB_NAME'                         => 'uuU68I2u',
            'MST_QUESTSUB_STORY'                        => 'fULaqIeB',
            'MST_QUESTSUB_TARGET_PARAM'                 => 'Cw3B65ql',
            'MST_QUEST_NAME'                            => 'NMwfx1lf',
            'MST_RB_ABILITY_GROUP_DES'                  => '3sxyv1w9',
            'MST_RB_ABILITY_GROUP_NAME'                 => '69zUY4Zb',
            'MST_RB_BONUS_RULE_DES'                     => '6YYynT87',
            'MST_RB_BONUS_RULE_NAME'                    => '10Ew2Rth',
            'MST_RB_FORBIDDEN_INFO_DES'                 => 'M6bRb5Eg',
            'MST_RB_FORBIDDEN_INFO_NAME'                => 'DaNvFWp7',
            'MST_RB_LS_NAME'                            => '12qZAZuh',
            'MST_RB_LS_REWARD_DES'                      => 'SGAZ9JAc',
            'MST_RB_LS_REWARD_TITLE'                    => 'x1vjjR7b',
            'MST_RB_SS_BONUS_INFO_NAME'                 => '690B2rHu',
            'MST_RB_SS_FORBIDDEN_INFO_NAME'             => '8Vi3mrDS',
            'MST_RB_SS_NAME'                            => 'xt6O7WKT',
            'MST_RB_SS_REWARD_DES'                      => 'vTrf5ALU',
            'MST_RB_SS_REWARD_TITLE'                    => 'UXR63E61',
            'MST_RECIPEBOOK_NAME'                       => 'tFnHkR8G',
            'MST_RECIPE_EXPLAINLONG'                    => 'F_TEXT_RECIPE_EXPLAIN_LONG',
            'MST_SCENARIOBATTLE_NAME'                   => 'TGxop4tW',
            'MST_SEASON_EVENT_ABILITY_TYPE_DESCRIPTION' => 'q81b55dv',
            'MST_SEASON_EVENT_ABILITY_TYPE_NAME'        => 'xT67VZAS',
            'MST_SEASON_EVENT_DESCRIPTION'              => 'mKbhB8ai',
            'MST_SEASON_EVENT_NAME'                     => 'FxaCYmHE',
            'MST_SHOP_NAME'                             => 'NYz5Oxm4',
            'MST_STORYSUB_NAME'                         => 'hiiVWxXJ',
            'MST_STORY_NAME'                            => 'fKGHnuPm',
            'MST_TELEPO_NAME'                           => 'ca5XNnWD',
            'MST_TICKER'                                => 'RUPcXt7J',
            'MST_TOWNSTORE_COMMENT'                     => 'SZXTrTgq',
            'MST_TOWNSTORE_NAME'                        => 'h23JuUGF',
            'MST_TOWN_DESCRIPTION'                      => 'KLoYS0Tj',
            'MST_TOWN_NAME'                             => 'N12vEZpN',
            'MST_TOWN_STORE_OWNER_NAME'                 => 'KFL34pbm',
            'MST_TRIBE_NAME'                            => 'Z6OfsPv9',
            'MST_TROPHY_EXPLAIN'                        => 'pNeHXqpJ',
            'MST_TROPHY_METER_SERIF'                    => '7BfBBf9E',
            'MST_UNIT_EXPLAIN_AFFINITY'                 => 'Zfw0jmyn',
            'MST_UNIT_EXPLAIN_DESCRIPTION'              => 'w6U2ntyZ',
            'MST_UNIT_EXPLAIN_EVOLUTION'                => '7tfppWVS',
            'MST_UNIT_EXPLAIN_FUSION'                   => 'TpbDECdR',
            'MST_UNIT_EXPLAIN_SHOP'                     => '3uEWl5CV',
            'MST_UNIT_EXPLAIN_SUMMON'                   => 'hWE8dJMC',
            'MST_UNIT_NAME'                             => 'sZE3Lhgj',
            'MST_SUBLIMATION_AFTER_EXPLAIN'             => 'JF89DHPE',
            'MST_EXPN_NAME'                             => 'N1FxjkHa',
            'MST_EXPN_DESC'                             => 'N1FxjkHa',
            'MST_RULE_COND'                             => 'a6kiwI22',
        ];
        const PLACEHOLDERS = ['NA', 'N/A', '<NA>', 'collab', 'collabo', '<temp>'];

        /** @var bool[] */
        protected static $loaded = [
            'CURRENCY_NAME' => true,
        ];

        /** @var string[][] */
        protected static $strings = [
            'CURRENCY_NAME_30'   => ['Gil'],
            'CURRENCY_NAME_50'   => ['Lapis'],
            'CURRENCY_NAME_51'   => ['Friend Points'],
            'CURRENCY_NAME_9990' => ['Energy'],
        ];

        public static function getString($type, $id, $key = 0) {
            return self::getStrings($type, $id)[$key] ?? null;
        }

        public static function getStrings($type, $id) {
            // if (GameFile::getRegion() == 'jp')
            //     return null;

            if (static::hasBeenLoaded($type) !== true)
                static::loadStrings($type);

            $arr = static::$strings["{$type}_{$id}"] ?? null;
            if ($arr == null)
                return null;

            $arr = array_map("trim", $arr);
            $arr = array_filter($arr, 'strlen');
            $arr = $arr + array_fill(0, self::LANGUAGES, null);
            ksort($arr);

            return $arr;
        }

        /**
         * Read all files in the file map
         *
         * @throws Exception
         */
        public static function readAll() {
            foreach (static::FILE_MAP as $filename)
                static::readFile($filename);
        }

        /**
         * @param string $filename
         *
         * @throws Exception
         */
        public static function readFile($filename) {
            // $region = GameFile::getRegion();
            $entry = GameFile::getEntry($filename);
            if ($entry == null)
                throw new \LogicException("{$filename} could not be resolved");

            $filename = $entry->getName();
            $file     = DATA_DECODED_DIR . "gl/{$filename}.txt";

            if (!file_exists($file))
                throw new \Exception($filename);

            $data = file_get_contents($file);
            $data = explode("\n", $data);

            foreach ($data as $line) {
                $line   = trim($line);
                $line   = mb_convert_encoding($line, 'UTF-8', 'UTF-8');
                $line   = explode("^", $line);
                $id     = array_shift($line);
                $values = array_values($line);

                foreach ($values as $k => $value)
                    if (!empty($value) && !in_array($value, self::PLACEHOLDERS))
                        static::$strings[$id][$k] = $value;
            }
        }

        private static function hasBeenLoaded($type) {
            return static::$loaded[$type] ?? false;
        }

        private static function loadStrings($type) {
            $file = static::FILE_MAP[$type];
            if ($file == null)
                throw new Exception($type);

            static::readFile($file);
            static::$loaded[$type] = true;
        }

        public function getEntries($type = null) {
            if ($type === null)
                return static::$strings[$type] ?? null;

            return static::$strings;
        }
    }