<?php

    namespace Sol\FFBE;

    /**
     * User: aEnigma
     * Date: 24.01.2017
     * Time: 19:53
     */
    class Strings extends \Solaris\FFBE\Helper\Strings {
        public const LANGUAGES = 6;
        public const FILE_MAP  = [
            'BAD_STATE_DESC'                            => ['F_TEXT_TEXT_EN'],
            'BUFF_DESC'                                 => ['F_TEXT_TEXT_EN'],
            'CASH_BUNDLE_PACK_DESC'                     => ['F_TEXT_TEXT_EN'],
            'CASH_BUNDLE_PACK_TITLE'                    => ['F_TEXT_TEXT_EN'],
            'CHALLENGE_COND'                            => ['F_TEXT_TEXT_EN'],
            'EXPLAIN_REWARD'                            => ['F_TEXT_TEXT_EN'],
            'LOGIN_BONUS_SPECIAL_MAIL_MSG'              => ['F_TEXT_TEXT_EN'],
            'LOGIN_BONUS_SPECIAL_MAIL_TITLE'            => ['F_TEXT_TEXT_EN'],
            'LPS_SUMALLY_COMPLETE_REWARD_MST_MESSAGE'   => ['F_TEXT_TEXT_EN'],
            'LPS_SUMALLY_REWARD_MST_MESSAGE'            => ['F_TEXT_TEXT_EN'],
            'LPS_SUMALLY_REWARD_MST_MESSAGE_BA'         => ['F_TEXT_TEXT_EN'],
            'MST_ABILITY_LONGDESCRIPTION'               => ['F_TEXT_ABILITY_EXPLAIN_LONG'],
            'MST_ABILITY_NAME'                          => ['F_TEXT_ABILITY_NAME'],
            'MST_ABILITY_PARAM_MSG'                     => ['F_TEXT_ABILITY_PARAM_MSG'],
            'MST_ABILITY_SHORTDESCRIPTION'              => ['F_TEXT_ABILITY_EXPLAIN_SHORT'],
            'MST_AREA_NAME'                             => ['F_TEXT_AREA_NAME'],
            'MST_AWARD_DESCRIPTION'                     => ['F_TEXT_AWARD_EXPLAIN'],
            'MST_AWARD_NAME'                            => ['F_TEXT_AWARD_NAME'],
            'MST_AWARD_TYPE'                            => ['F_TEXT_AWARD_TYPE'],
            'MST_BEASTSKILL_DESCRIPTION'                => ['F_TEXT_BEAST_SKILL_DES'],
            'MST_BEASTSKILL_NAME'                       => ['F_TEXT_BEAST_SKILL_NAME'],
            'MST_BEAST_NAME'                            => ['F_TEXT_BEAST_NAME'],
            'MST_BUNDLE_DESC'                           => ['F_TEXT_BUNDLE'],
            'MST_BUNDLE_MESSAGE'                        => ['F_TEXT_BUNDLE'],
            'MST_BUNDLE_NAME'                           => ['F_TEXT_BUNDLE'],
            'MST_CAPTURE_INFO'                          => ['F_TEXT_CAPTURE_INFO'],
            'MST_CHALLENGE_NAME'                        => ['F_TEXT_CHALLENGE_NAME'],
            'MST_CHARACTER_NAME'                        => ['F_TEXT_CHARACTER_NAME'],
            'MST_CLSM_MONSTER_GROUP_NAME'               => ['F_TEXT_COLOSSEUM_MONSTER_GROUP_NAME'],
            'MST_DAILY_QUEST_DES'                       => ['F_TEXT_DAILY_QUEST_DES'],
            'MST_DAILY_QUEST_DETAIL'                    => ['F_TEXT_DAILY_QUEST_DETAIL'],
            'MST_DAILY_QUEST_NAME'                      => ['F_TEXT_DAILY_QUEST_NAME'],
            'MST_DIAMOND_DESC'                          => ['F_TEXT_DIAMOND_NAME'],
            'MST_DIAMOND_NAME'                          => ['F_TEXT_DIAMOND_NAME'],
            'MST_DUNGEONS_NAME'                         => ['F_TEXT_DUNGEON_NAME'],
            'MST_EQUIP_ITEM_LONGDESCRIPTION'            => ['F_TEXT_ITEM_EQUIP_LONG'],
            'MST_EQUIP_ITEM_NAME'                       => ['F_TEXT_ITEM_EQUIP_NAME'],
            'MST_EQUIP_ITEM_SHORTDESCRIPTION'           => ['F_TEXT_ITEM_EQUIP_SHORT'],
            'MST_EXCHANGESHOP_ITEM_NAME'                => ['F_TEXT_EXCHANGE_SHOP_ITEM'],
            'MST_EXPN_DESC'                             => ['F_TEXT_EXPN_STORY'],
            'MST_EXPN_NAME'                             => ['F_TEXT_EXPN_STORY'],
            'MST_GACHA_DETAIL_NAME'                     => ['F_TEXT_GACHA'],
            'MST_GACHA_EXPLAIN_LONG'                    => ['F_TEXT_GACHA'],
            'MST_GACHA_EXPLAIN_SHORT'                   => ['F_TEXT_GACHA'],
            'MST_GACHA_NAME'                            => ['F_TEXT_GACHA'],
            'MST_GAME_TITLE_NAME'                       => ['F_TEXT_GAME_TITLE_NAME'],
            'MST_IMPORTANT_ITEM_LONGDESCRIPTION'        => ['F_TEXT_IMPORTANT_ITEM_EXPLAIN_LONG'],
            'MST_IMPORTANT_ITEM_NAME'                   => ['F_TEXT_IMPORTANT_ITEM_NAME'],
            'MST_IMPORTANT_ITEM_SHOP'                   => ['F_TEXT_IMPORTANT_ITEM_SHOP'],
            'MST_IMPORTANT_ITEM_SHORTDESCRIPTION'       => ['F_TEXT_IMPORTANT_ITEM_EXPLAIN_SHORT'],
            'MST_ITEM_LONGDESCRIPTION'                  => ['F_TEXT_ITEM_EXPLAIN_LONG'],
            'MST_ITEM_NAME'                             => ['F_TEXT_ITEM_NAME'],
            'MST_ITEM_SHORTDESCRIPTION'                 => ['F_TEXT_ITEM_EXPLAIN_SHORT'],
            'MST_JOB_NAME'                              => ['F_TEXT_JOB_NAME'],
            'MST_LAND_NAME'                             => ['F_TEXT_LAND_NAME'],
            'MST_LIMITBURST_DESCRIPTION'                => ['F_TEXT_LIMIT_BURST_DES'],
            'MST_LIMITBURST_NAME'                       => ['F_TEXT_LIMIT_BURST_NAME'],
            'MST_MAGIC_LONGDESCRIPTION'                 => ['F_TEXT_MAGIC_EXPLAIN_LONG'],
            'MST_MAGIC_NAME'                            => ['F_TEXT_MAGIC_NAME'],
            'MST_MAGIC_SHORTDESCRIPTION'                => ['F_TEXT_MAGIC_EXPLAIN_SHORT'],
            'MST_MATERIA_LONGDESCRIPTION'               => ['F_TEXT_MATERIA_EXPLAIN_LONG'],
            'MST_MATERIA_NAME'                          => ['F_TEXT_MATERIA_NAME'],
            'MST_MATERIA_SHORTDESCRIPTION'              => ['F_TEXT_MATERIA_EXPLAIN_SHORT'],
            'MST_MEDAL_EXCHANGE_EXPLAIN'                => ['F_TEXT_TOWN_STORE'],
            'MST_MISSION_NAME'                          => ['F_TEXT_MISSION'],
            'MST_MONSTERDICTIONARY_LONGDESCRIPTION'     => ['F_TEXT_MONSTER_DIC_EXPLAIN_LONG'],
            'MST_MONSTERDICTIONARY_NAME'                => ['F_TEXT_MONSTER_DICTIONARY_NAME', 'F_TEXT_MONSTER_PART_DIC_NAME'],
            'MST_MONSTERDICTIONARY_SHORTDESCRIPTION'    => ['F_TEXT_MONSTER_DIC_EXPLAIN_SHORT'],
            'MST_MONSTERPARTDICTIONARY_NAME'            => ['F_TEXT_MONSTER_PART_DIC_NAME'],
            'MST_MONSTERSKILL_PARAM_MSG'                => ['F_TEXT_ABILITY_PARAM_MSG'],
            'MST_MONSTER_NAME'                          => ['F_TEXT_MONSTER_NAME', 'F_TEXT_TEXT_EN'],
            'MST_MONSTER_SKILL_NAME'                    => ['F_TEXT_MONSTER_SKILL_NAME'],
            'MST_NPC_NAME'                              => ['F_TEXT_NPC_NAME'],
            'MST_PICTURE_STORY_NAME'                    => ['F_TEXT_PICTURE_STORY_NAME'],
            'MST_PLAYBACK_CHAPTER_NAME'                 => ['F_TEXT_PLAYBACK'],
            'MST_PLAYBACK_EVENT_NAME'                   => ['F_TEXT_PLAYBACK'],
            'MST_PLAYBACK_MAP_NAME'                     => ['F_TEXT_PLAYBACK'],
            'MST_QUESTSUB_GOAL_DETAIL'                  => ['F_TEXT_QUEST_SUB_DETAIL'],
            'MST_QUESTSUB_NAME'                         => ['F_TEXT_QUEST_SUB_NAME'],
            'MST_QUESTSUB_STORY'                        => ['F_TEXT_QUEST_SUB_STORY'],
            'MST_QUESTSUB_TARGET_PARAM'                 => ['F_TEXT_QUEST_SUB_TARGET_PARAM'],
            'MST_QUEST_NAME'                            => ['F_TEXT_QUEST'],
            'MST_RB_ABILITY_GROUP_DES'                  => ['F_TEXT_RB_ABILITY_GROUP_DESCRIPTION'],
            'MST_RB_ABILITY_GROUP_NAME'                 => ['F_TEXT_RB_ABILITY_GROUP_NAME'],
            'MST_RB_FORBIDDEN_INFO_DES'                 => ['F_TEXT_RB_FORBIDDEN_INFO_DESCRIPTION'],
            'MST_RB_FORBIDDEN_INFO_NAME'                => ['F_TEXT_RB_FORBIDDEN_INFO_NAME'],
            'MST_RECIPEBOOK_NAME'                       => ['F_TEXT_RECIPE_BOOK_NAME'],
            'MST_RECIPE_EXPLAINLONG'                    => ['F_TEXT_RECIPE_EXPLAIN_LONG'],
            'MST_RULE_COND'                             => ['F_TEXT_RULE_DESCRIPTION'],
            'MST_RULE_DESC'                             => ['F_TEXT_RULE_DESCRIPTION'],
            'MST_SCENARIOBATTLE_NAME'                   => ['F_TEXT_SCENARIO_BATTLE'],
            'MST_SEASON_EVENT_ABILITY_TYPE_DESCRIPTION' => ['F_TEXT_SEASON_EVENT_ABILITY_NAME'],
            'MST_SEASON_EVENT_ABILITY_TYPE_NAME'        => ['F_TEXT_SEASON_EVENT_ABILITY_TYPE_NAME'],
            'MST_SHOP_NAME'                             => ['F_TEXT_SHOP'],
            'MST_SPCHALLENGE_DES'                       => ['F_TEXT_SPCHALLENGE'],
            'MST_SPCHALLENGE_NAME'                      => ['F_TEXT_SPCHALLENGE'],
            'MST_SPCHALLENGE_SYS_DES'                   => ['F_TEXT_SPCHALLENGE'],
            'MST_STORYSUB_NAME'                         => ['F_TEXT_STORY_SUB'],
            'MST_STORY_NAME'                            => ['F_TEXT_STORY_NAME'],
            'MST_SUBLIMATION_AFTER_EXPLAIN'             => ['F_TEXT_SUBLIMATION_EXPLAIN'],
            'MST_SUBLIMATION_BEFORE_EXPLAIN'            => ['F_TEXT_SUBLIMATION_EXPLAIN'],
            'MST_TELEPO_NAME'                           => ['F_TEXT_TELEPO_NAME'],
            'MST_TICKERLOG_MSGFORMAT'                   => ['F_TEXT_TICKER'],
            'MST_TOWNSTORE_COMMENT'                     => ['F_TEXT_TOWN_STORE_COMMENT'],
            'MST_TOWNSTORE_NAME'                        => ['F_TEXT_TOWN_STORE'],
            'MST_TOWN_DESCRIPTION'                      => ['F_TEXT_TOWN_EXPLAIN'],
            'MST_TOWN_NAME'                             => ['F_TEXT_TOWN_NAME'],
            'MST_TOWN_STORE_OWNER_NAME'                 => ['F_TEXT_TOWN_STORE_OWNER_NAME'],
            'MST_TRIBE_NAME'                            => ['F_TEXT_TRIBE'],
            'MST_TROPHY_EXPLAIN'                        => ['F_TEXT_TROPHY_EXPLAIN'],
            'MST_TROPHY_METER_SERIF'                    => ['F_TEXT_TROPHY_METER_SERIF'],
            'MST_UNIT_EXPLAIN_AFFINITY'                 => ['F_TEXT_UNIT_AFFINITY'],
            'MST_UNIT_EXPLAIN_DESCRIPTION'              => ['F_TEXT_UNIT_DESCRIPTION'],
            'MST_UNIT_EXPLAIN_EVOLUTION'                => ['F_TEXT_UNIT_EVO'],
            'MST_UNIT_EXPLAIN_FUSION'                   => ['F_TEXT_UNIT_FUSION'],
            'MST_UNIT_EXPLAIN_SHOP'                     => ['F_TEXT_UNIT_EXPLAIN_SHOP'],
            'MST_UNIT_EXPLAIN_SUMMON'                   => ['F_TEXT_UNIT_SUMMON'],
            'MST_UNIT_NAME'                             => ['F_TEXT_UNITS_NAME'],
            'SERVER_MSG'                                => ['F_TEXT_TEXT_EN'],
        ];

        protected static $loaded = [];

        /**
         * Read all files in the file map
         */
        public static function readAll(): void {
            foreach (static::FILE_MAP as $files)
                foreach ($files as $filename)
                    foreach (array_keys(static::LANGUAGE_ID) as $lang)
                        static::readFile(GameFile::getFilePath($filename, $lang), $lang);
        }

        /**
         * @param string $type
         *
         * @return bool
         */
        public static function hasBeenLoaded($type): bool {
            return static::$loaded[$type] ?? false;
        }


        /**
         * @param string $table
         * @param int    $id
         *
         * @return string[]
         */
        public static function getStrings($table, $id): array {
            if (static::hasBeenLoaded($table) !== true)
                static::readTable($table);

            return parent::getStrings($table, $id);
        }

        /**
         * @inheritDoc
         */
        public static function hasStrings($table, $id): bool {
            if (static::hasBeenLoaded($table) !== true)
                static::readTable($table);

            return parent::hasStrings($table, $id);
        }

        /**
         * @inheritDoc
         */
        public static function readFile($file, $language): void {
            static::$loaded[basename($file, '.txt')] = true;

            parent::readFile($file, $language);
        }

        /**
         * @param string $type
         *
         * @return bool
         */
        public static function readTable($type): bool {
            $files = static::FILE_MAP[$type] ?? [str_replace('MST_', 'F_TEXT_', $type)];
            if (empty($files))
                return false;

            foreach ($files as $file)
                if (static::$loaded[$file] ?? false)
                    // file already loaded
                    continue;

                else {
                    $loaded = false;
                    foreach (array_keys(static::LANGUAGE_ID) as $lang) {
                        try {
                            static::readFile($file, $lang);
                            $loaded = true;
                        }
                        catch (\LogicException $e) {
                            // File not found
                        }
                    }

                    // mark table name as loaded
                    static::$loaded[$type] = $loaded;
                }

            return true;
        }
    }