<?php
    /**
     * User: aEnigma
     * Date: 17.02.2017
     * Time: 22:48
     */

    namespace Sol\FFBE;

    use Solaris\FFBE\AES;

    class GameFile {
        /** @var  GameFile[] */
        protected static $files;

        /** @var  GameFile[] */
        protected static $names;

        /** @var string */
        protected static $region = 'gl';
        /** @var string[] */
        protected static $keys = [
            // UnitMst
            '5A8SmwyC' => 'effect_frames',
            'a9DvZ70c' => 'attack_frames',
            'PR2HIc71' => 'lb_id',
            'T3Zcf40Q' => 'need_exp', // lb max?

            'G4L0YIB2' => 'name',
            'VgrT60cL' => null, // name dup
            '6uvHX2JG' => 'desc',

            'b42ICFMc' => 'StandData',
            // ''         => 'MoveOffset',
            'H4zD2IQ5' => 'wait',
            'rB70Ls63' => 'MoveSpeed',
            'rtw4F6hn' => 'MoveType',
            'LBye01Wi' => 'MoveOffset',
            '7KYufEd5' => 'TouchRect',

            // unit awakening
            '7UR4J2SE' => 'gil',
            'KCk8u0am' => 'mats', // "ElemInfo"

            // ability
            // AbilityMst
            'un32MdKk' => 'LearningId', // blue magic?
            '2QoE3PtB' => 'MultiUseFlg',
            'DQ87jGPz' => null, // unused
            'fwChg17k' => 'SublimationRank',
            'iUIxB65H' => 'FirstSkillId',
            '58bfqWsR' => 'AvoidDisable',
            'LNjKzg51' => 'AllImpactFlg',
            '5w1o9DPV' => 'MotioinType',

            // SkillMstBase
            'Af2Pc0Jz' => 'move_type', //
            'yjY4GK3X' => 'skill_type', // only used by sublimationskillrequest?
            'aR5i4ewA' => 'skill_kind', // ???  [NONE, Magic, Ability, Limit Burst, Esper, Monster]
            'J35nicFV' => 'execute_type', //
            'Isb1GDe2' => 'attack_type',

            'Jn6BbR2P' => 'element_inflict',

            '1Zsh27mS' => 'ability_ids', //
            '7irj9hAb' => 'flags',
            'i6kc1jN0' => 'magic_ids',   //
            'Ga5V42LZ' => 'ability_id',
            'QV1HE7Wq' => 'magic_id',
            '52KBR9qV' => 'rarity',
            '6X0fxsdw' => 'magic_type',
            '8cY9hejo' => 'mp_cost',
            'Q9uV5zqc' => 'skill_cost',
            'Ha9G5sCS' => 'effect_param', // ProcessParam
            '2kq0BpnD' => 'effect_type',  // ProcessType
            'PI18xnkD' => 'ProcessFrame',
            'z1q8AIXD' => 'PermitLap',
            '6p90IFnC' => 'unit_restriction',

            'a4hXTIm0' => 'target', // chances, modifiers
            'oPD4yL58' => 'target_range',
            //            '7irj9hAb' => 'activation_type',
            //            'oPD4yL58' => 'target1',
            //            'H4zD2IQ5' => 'target2',
            //            '6X0fxsdw' => 'target3',
            //            'AegvG91P' => 'target_party1',
            //            'oL6zdpD0' => 'target_party2',
            //            'a4hXTIm0' => 'target_condition',

            //            'PI18xnkD' => 'timing stuff?',
            // aR5i4ewA sound_id?
            // 7irj9hAb activation cost? dualcast seperate
            'KosY4a18' => 'is_active', // command type
            'oL6zdpD0' => 'state', // 1 skills from popoi and primm mostly 3 rest
            //            oPD4yL58

            // enhancement
            'SJ56uhz2' => 'enhancement_id',
            'hxu12ZFS' => 'skill_id_old',
            'Q1n9iYgz' => 'skill_id_new',
            'HS7V6Ww1' => 'mats',
            '25oxcKwN' => 'unit',

            // unit
            '2odVXyT8' => 'unit_type',
            'Fzmtn2Q5' => 'is_summonable',
            'rUnq35N9' => 'is_trust_moogle',

            'woghJa61' => 'unit_id',
            '3HriTp6B' => 'unit_evo_id',
            't74crqAL' => 'unit_next_evo_id',
            'a9qg8vnP' => 'tribe_id',
            'Z0EN6jSh' => 'order_index', // dex no for units
            'taQ69cIx' => 'game_id',
            'IWv3u1xX' => 'job_id',
            '0HUPxDf1' => 'icon_id',
            'qtkvrS60' => 'file_name',
            'tL6G9egd' => 'item_id',
            'UbSL8C7i' => 'sex',

            //
            '7wV3QZ80' => 'level',
            'aU3o2D4t' => 'max_level',
            'em5hx4FX' => 'hp',
            'L0MX7edB' => 'mp',
            'o7Ynu1XP' => 'atk',
            '6tyb58Kc' => 'def',
            'Y9H6TWnv' => 'mag',
            'sa8Ewx3H' => 'spr',
            //
            '9fW0TePj' => 'rarity',
            '2EusAP6T' => 'rarity_min',
            //
            'yY7U9TsP' => 'equip',
            '19ykfMgL' => 'ability_slots',
            'hn42BL3Q' => 'element_resist',
            'D9Nb7jgd' => 'status_resist',
            '5VtY0SIU' => 'magic_affinity',
            '9mu4boy7' => 'trustmaster',
            'C74EmZ1I' => 'supertrust',
            'DjNRx0d6' => 'lb_fillrate', // dropcheck
            'ENwn42DV' => 'lb_cost',

            //
            '0W7g6YXS' => 'exp_pattern',
            'uk6Wh8mw' => 'stat_pattern',

            //            //
            'u18nfD7p' => 'sell_price',
            'Y2X8d5R7' => 'merge_bonus_stats',
            'N85uJT1d' => 'merge_bonus_exp',
            '1Rf2VDoI' => 'merge_bonus_lb',

            // items
            'J1YX9kmM' => 'equip_id',
            'gJf3zp8a' => 'index', // tradable item index
            'QLfe23bu' => 'not_dex_no', // learning mst index
            'H27Vr9UD' => 'category',
            'm23vY65S' => 'is_unique', // always 0?

            'H5jBL0Vf' => 'materia_id',
            '6m9jvhMF' => 'materia_type',
            'cNSmWmn1' => 'limit', // materia limit

            '8DZGF4Nn' => 'equip_type',
            'znGsI4f8' => 'equip_slot_type',
            't5Q4wZ7v' => 'equip_requirements', // EquipCondition
            'fh31sk7B' => 'bonus_hp',   // equip & monsterparts
            'PC97pWQj' => 'bonus_mp',   // equip & monsterparts
            '9NBV1XCS' => 'bonus_atk',  // equip & monsterparts
            'Pn5w9dfh' => 'bonus_def',  // equip & monsterparts
            '84FJM3PD' => 'bonus_mag',  // equip & monsterparts
            'T8xMDAZ4' => 'bonus_spr',  // equip & monsterparts

            'fUnkv59o' => 'is_two_handed',
            'ECbv61DK' => 'accuracy',
            'u2I6pR9T' => 'atk_variance',

            'SxREt1M0' => 'status_inflict',

            'GiBvPK84' => 'equip_skill_magic',
            't42Xb3jV' => 'equip_skill_ability',

            'A86KwrGh' => 'DispDict', // 1 = Equip?
            '5Sx0haBw' => 'InvokeMagic', // new?
            '82PrpZU6' => 'InvokeAbility', // new?

            'E61qF5Lp' => 'price_buy',
            'bz6RS3pI' => 'price_sell',

            '9J6uRe8f' => 'stack_size', // setMaxNumInFrame, wtf
            'AegvG91P' => 'use_case',   // battle,explo
            'DnCP52Jk' => 'carry_limit',
            'pv9q8khF' => 'flags',

            // NoticeUpdate
            'gM1w8m4y' => 'NoticeUpdate',
            '8mD2v6IX' => 'kind_id',
            '9qh17ZUf' => 'SaveData::getHandleName',
            'JC61TPqS' => 'UserInfo::getContactID',
            'io30YcLA' => 'CommonUtils::getDeviceName',
            '6Nf5risL' => 'SaveData::getModelChangeCnt',
            'e8Si6TGh' => 'CommonUtils::getDeviceID',
            '1WKh6Xqe' => 'CommonUtils::getBuildVerSion',
            '64anJRhx' => 'CommonUtils::convertToDateTime(time())',
            'm3Wghr1j' => 'UserInfo::getFriendID',
            'NYVBQZG7' => 'UserInfo::getGumiLiveToken',
            'X6jT6zrQ' => 'UserInfo::getFacebookToken', //?
            'NggnPgQC' => 'System::getAdvertisingID', //?
            'ma6Ac53v' => 'getMacroToolRunningStatus',
            'D2I1Vtog' => 'CheckYmd',

            // News
            'tN8f0PSB' => 'NoticeMstResponse',
            'td06MKEX' => 'news_id', // NoticeID
            '1X65WPLU' => 'url',
            '15Y3fBmF' => 'title',
            'oIra47LK' => 'PageType',
            '8amsKpQ2' => 'PopupFlag',
            '5GNraZM0' => 'NoticeType', //

            // MonsterPartsMst
            'bV34YIJQ' => 'MonsterPartsMst',
            '6Yc4RkdM' => 'monster_parts_num',
            // <-- unit
            'V9j8wJcC' => 'monster_id',
            'xqartN26' => 'monster_unit_id',
            'A5x02C6a' => 'monster_skill_set_id',
            '7qxLC5hP' => 'monster_skill_ids',
            '6E5u1xrs' => 'monster_skill_id',
            '8a6gAwN9' => 'ai_id',
            'B6H34Mea' => 'exp',
            'tPh7ue1c' => 'DamageResists',
            'f67khsQZ' => 'physical_resist',
            'Q1qLbx93' => 'magical_resist',
            'zPu2iUK8' => 'special_resist',
            'Y9Vz8WNH' => 'special_resist2',
            'Rcqa1Zt7' => 'debuff_resist', // 1-4 breaks, 5 stop

            'h8V3AzqP' => 'drop_limit',
            '9BetM2Ds' => 'loot_table',
            "MZn8LC6H" => 'loot_table_rare',   // rare
            "g4voc2Aw" => 'loot_table_unique', // unique
            "Vf5DGw07" => 'loot_table_unit',

            '3frn4ILX' => 'steal_table',
            "IRf10dwx" => 'steal_table_rare',
            "e5xoiC84" => 'steal_table_unique',
            'ATM35pjf' => 'steal_gil',
            'Wm93vGVU' => 'steal_count_limit',

            '2U3Zbxpy' => 'num_act_max',
            '0ePkR9nZ' => 'num_act_min',

            '4Y2DGP7m' => 'num_actions',

            'n0UEb71s' => 'BadStateResistAdd',
            '0dQ4JPAp' => 'CoreFlg',

            'w04FtZqB' => 'OverHeadPos',
            '9rXm2fJP' => 'LibraOffset',
            'kSL0M69n' => 'FlipFlg',
            'c76s0ehT' => 'AttackFlushFlg',
            'cNzS27x6' => 'MpDecFlg',

            '8zH7CioM' => null, // unused

            // passive
            'Wp39QX52' => 'MonsterSkillMst', // jp
            'UY6CZ9AH' => 'MonsterSkillSetMst', // jp
            'MLj0dJa8' => 'MonsterPassiveSkillMst',
            '8CoiSdu4' => 'MonsterPassiveSkillSetMst',
            'jmKe42Yg' => 'monster_passive_skill_set_id',
            '4kVnNws6' => 'monster_passive_skill_set_skill_ids',
            '6oWt9bL3' => 'monster_passive_skill_id',

            // ai
            'w7VR6ypY' => 'priority',
            'ixDq7yJ8' => 'weight',
            'ry3J4UNG' => 'AI_COND_TARGET',
            '9Fe2mBtK' => 'AI_COND1',
            '0wEykBj5' => 'AI_PARAM1',
            'J38o4DRA' => 'AI_COND2',
            '8vTj4XRb' => 'AI_PARAM2',
            'VuUTL8v6' => 'AI_SEARCH_COND',
            'z5hB3P01' => 'action',
            'piK07TV4' => 'AI_ACT_TARGET',

            //
            'ib1l3NRs' => 'SignalKeyTag',
            '7XEgju6x' => 'getAppID',
            '1j5ZAMAO' => 'getAppVersion',
            'FBNfOCpA' => 'getAppType',
            'C1YE1cMb' => 'DeviceName',
            'tgg589XL' => 'DeviceVer',
            'TvBT6QFS' => 'getDeviceID',
            'AfgiCn0X' => 'GSInfo::getGSType',
            '0dAXnxoI' => 'GSInfo::getGSName',
            '9mL0CCHq' => 'GSInfo::getGSPlace',
            '9ttE9HJ1' => 'GSInfo::getGSLevel',
            'CB3Kt1eE' => 'GSInfo::getGSDone',
            'hZBT8NH1' => 'getCountryCode',

            // requests
            'TEAYk6R1' => 'header',
            'LhVz6aD2' => 'PlayerInfo',
            'c1qYg84Q' => 'VersionTag',
            't7n6cVWf' => 'body',
            'qrVcDe48' => 'data',
            '9Tbns0eI' => 'UserInfo::getUserID',
            'Wk1v6upb' => 'message',
            '3TzVX8nZ' => 'date_short',
            'eKZu2j0M' => 'UserSpDungeonInfo',
            'y35YvQjZ' => 'WaveBattleInfo',
            'wbet57nF' => 'MonsterMst',
            'sxe5C48P' => 'DailyQuestInfo',
            'Hqdgx83H' => 'DailyQuestMst',
            's4IVuXZ4' => 'ServerTimeInfo',
            'QCcFB3h9' => 'SignalKey',
            'QFUPjOcL' => 'Breadcrumb',
            '8PEB5o7G' => 'UserActualInfo',
            '1cBqy20J' => 'EventPointLog',

            '5RZe0rLH' => 'FriendUnitInfo', // FriendRestartResponse -> FriendUnitInfoResponse
            'V43AurR8' => 'LastAccessDiff',
            'G8mtXbf6' => 'LeaderUpdateDate',
            'csH5PWo1' => 'ReinYmd',
            '8zE6w9RP' => 'isFavorite',
            'K3BV17gk' => 'InfoType',
            'Th31onuv' => 'ForceUpdate?',
            'Ey50pR6I' => 'ReinforeceCnt',

            // requests debug
            'wM9AfX6I' => 'version',
            'dUX7Pt5i' => 'friend?',

            // MissionStartRequest
            'jQsE54Iz' => 'MissionStartRequest',
            'b6PwoB37' => 'rng_seed',

            // UserTeamInfo
            '3oU9Ktb7' => 'UserTeamInfo',
            '0XUs3Tv6' => 'friend_points',
            'qLke7K8f' => 'friend_unit_evo_id',
            'm0bD2zwU' => 'user_nrg_max',
            '5sEbN01y' => 'user_nrg_reset_timer',
            'xDm19iGS' => 'user_arena_orbs',
            'zo76TkaG' => 'user_arena_orbs_max',
            'T1J8Avne' => 'user_arena_orbs_reset_timer',
            'Ddsg1be4' => 'user_event_orbs',
            'Le8Z5cS0' => 'user_event_orbs_max',
            'zR0pYjb2' => 'user_event_orbs_reset_timer',
            'tsT4vP7w' => 'user_friends_add',
            'T1Lm7WdD' => 'user_units_add',
            'Xu7L1dNM' => 'user_equip_add',
            '4BI0WKjQ' => 'user_items_add',
            '92LoiU76' => 'user_materia_add',
            '9w3Pi6XJ' => 'user_mats_add',
            '68Vn2FrK' => 'user_login_series',
            'Utx8P1Qo' => 'user_gift_id',

            // UserActualInfo
            'DYTx1Is3' => 'battle_bg_image',
            'r21y0YzS' => 'party_id',
            'Kgvo5JL2' => 'party_id_dupe', // duplicate
            'MBIYc89Q' => 'friend_party',
            'igrz05CY' => 'arena_party',
            'Isc1ga3G' => 'colosseum_party',
            'i1AWnF52' => 'date_str',

            // FriendUnitInfo
            '2vuhy0Ex' => 'FriendUnitInfo',
            '9Qt5cI0n' => 'FriendUnitInfoResponse', //?
            '10fNKueF' => 'unit_level',
            'a71oxzCH' => 'unit_lb_level',
            'kaR12y9r' => 'timestamp_last_played',
            '4p8h7i9g' => 'timestamp_lead_changed',
            'L8d0fqpT' => 'player_rank',

            '2K05IxtX' => 'FriendSearchResult',

            // DungeonMst
            'U9iHsau3' => 'dungeon_id',
            'rFd5CiV2' => 'visibility',
            'DutE7B3F' => 'route_id',
            'U9hr20s7' => 'effect_switch_info',
            'UpRi8v3Y' => 'map_flag',
            '7DTnc2Lt' => 'image_info',
            'amG29ZFs' => null,
            'K9mME2Ye' => null,

            // MissionMst
            'qo3PECw6' => 'mission_id',
            '8xi6Xvyk' => 'num_waves',
            'B6kyCQ9M' => 'cost', // cost
            'MxLFKZ13' => 'display_position',
            'q4f9IdMs' => 'file_info',
            '1x4bG2J8' => 'completion_reward',
            'Y4VoF8yu' => 'mission_type', // 1 = battle, 2 = explo?
            'bs60p4LC' => 'explo_bg_sound',

            'uv60hgDW' => 'subregion_id',
            'fV4Jn13q' => 'flags',                      // mission flags
            'dIPkNn61' => 'switch_open',
            '0VkYZx1a' => 'unique',
            'gQR24kST' => 'mission_subtype',
            'Kn03YQLk' => 'EntryCond',
            '0VCy3xHN' => 'ForbiddenCmd',
            '69fVGgcI' => 'ScheduleType',
            'zF6QIY2r' => 'GroupId',
            'CQN15Hgr' => 'CostType',
            '1C7GDEv4' => 'switch_non_info',

            'GB89p0JW' => 'ExpGetLimitLv',              // always 0
            'IpyB85ab' => 'setMissionTargetId',         // always 0
            'TKBIV0U1' => 'always_1_1',                 // unused, always 1
            'V1BwG8bE' => 'always_empty_1',             // unused, always empty

            // MissionPhaseMst
            'an6TB4WS' => 'MissionPhaseMst',
            '6v7y3QcJ' => 'wave_num',
            'gou39JeI' => 'wave_type',
            'pIHCrf40' => null,                         // repeat of wave_num
            'EAie4oZ9' => null,                         // is battle flag?
            'k4imoR6p' => null,                         // is battle flag?
            'T03qgLWA' => 'ContinuePermit',
            'Us0znif6' => 'RerunPermit',
            '4K8t5dVh' => 'UnawaresFlg',
            'V2mCH5UR' => 'battle_script_id',
            'wTMK0x5p' => 'cond_info_str',
            'J9gafF0t' => 'BackSceneType',
            'YS9kfCX7' => 'BgmResourceID',
            'i2ar9yXM' => 'BeforeEffectType',
            '5LZN8C9I' => 'AfterEffectType',

            // Challenge
            'V36ygRYs' => 'challenge_id',
            'Pzn5h0Ga' => 'condition',
            'dX6cor8j' => 'reward',

            // DailyQuestMst
            'XCD3VwHk' => 'quest_progress_current',
            '97CxqAgk' => 'quest_progress_objective',

            // ServerTimeInfo
            'cnopoBWZ' => 'server_time_unix',
            'vhjEjKXZ' => 'server_time_string',

            // FieldTreasureMst
            'pDFgB3i0' => 'FieldTreasureMst',
            '4tNM5fGC' => 'treasure_id',
            'NiG2wL3A' => 'treasure_content',
            'uJh46wFS' => 'treasure_open_switch',
            'juA0Z4m7' => 'switch_info',

            'JR7jZci2' => 'ScenarioBattleInfo',

            // (Scenario) BattleGroupMst
            'YjHhvN65' => 'ScenarioBattleGroupMst',
            'Bi6hVv34' => 'BattleGroupMst',
            '2fY1IomW' => 'battle_group_id',
            '3t7pZM5V' => 'death_effect',
            'WJdBp9f0' => 'initial_display',
            'm9BvFk2X' => 'call_max',

            //
            'vM21k0do' => 'HarvestDetailInfo',
            'N3hB0CwE' => 'harvest_node_id',
            '9GIvAQC2' => 'harvest_node_num',
            '1Fa7rL5R' => 'harvest_node_loot_table',

            // empty?
            'mk5fza4r' => 'UserQuestInfo',
            'JG96kr5C' => 'UserQuestSubInfo',
            '1IfDqR4n' => 'DungeonResourceLoadMst',

            // ScenarioBattleMst
            'rqxw0p9k' => 'ScenarioBattleMst',
            'ja07xgLF' => 'scenario_battle_id',
            '6w4xiIsS' => 'battle_bg_music',
            '14FMQseU' => 'EscapeFlg',

            // EncountInfo
            '6v0LPiRe' => 'EncountInfo',
            'Cw06mL1r' => 'EncountInfo?',
            'YzGa63QM' => 'EncountFieldID',
            'h4Sjf96p' => 'EncountNum',
            '6uSx3PIa' => null,

            // UserDiamondInfo
            'J3pAG0I5' => 'UserDiamondInfo',
            'D7FX3MYh' => 'diamond_id',
            'mTxF8a3D' => 'name',
            'cb3WmiD0' => 'lapis_free',
            'T7sah9rc' => 'lapis_paid',
            'K1G4fBjF' => 'platform_id',
            // 'TzvJwA60' => 'product_id', // already set by bundle
            'FBjo93gK' => 'price',
            '96ej0dxb' => 'disp_name',

            // UserPurchaseInfo
            '5Ip20EyX' => 'UserPurchaseInfo',

            // UserUnitInfo
            'B71MekS8' => 'UserUnitInfo',
            'og2GHy49' => 'unit_real_id',
            'Z06cK4Qi' => 'unit_evo_id_pulled',
            'X9ABM7En' => 'unit_total_exp',
            'EXf5G3Mk' => 'unit_lb_exp',
            'f17L8wuX' => 'unit_tm_progress',

            // UserEquipItemDictionary
            'VpWnB21X' => 'UserUnitDictionary',
            'm4Bk5ex3' => 'UserEquipItemDictionary',
            '6RQhbe8m' => 'UserItemDictionary',
            'wiTU2Hy1' => 'UserMateriaDictionary',

            'HrVT9C6h' => 'UserMonsterDictionary',
            'z41pMJvC' => 'LibraDictionary',
            'KJv6V7G9' => 'KnockdownInfo',
            '4yk1JQBs' => 'KnockdownCount',

            // UserUnitFavorite
            '1k3IefTc' => 'UserUnitFavorite',

            // UserPartyDeckInfo
            '5Eb0Rig6' => 'UserPartyDeckInfo',
            '2A6fYiEC' => 'party_loadout', // !lead : pos? : unit_real_id

            'Z3vhm29a' => 'UserLearningInfo', //??? tutorial popups?

            'gy84FjCA' => 'UserCraftInfo',
            'gak9Gb3N' => 'craft_item_type',
            '6ukvMSg9' => 'craft_slot_id',
            'MpNE6gB5' => 'recipe_id',

            'ZrDPm3j5' => 'UserRecipeBookInfo',
            'eAM5B1qt' => 'RecipeBookInfo',

            'r6I9k7WR' => 'UserClsmInfo',
            'p9ortU42' => 'UserClsmProgressInfo',
            'i5pd8xr3' => 'colosseum_tier_id',
            '4jbAtD7f' => 'colosseum_tier_points',
            'Bi5akb1o' => 'colosseum_tier_tries',
            'Wdr3T4pe' => 'colosseum_tier_wins',
            'qBYxcC92' => 'colosseum_tier_completions',
            'g35y6a4N' => 'monster_list',

            'gP9TW2Bf' => 'UserBeastInfo',
            '49rQB3fP' => 'UserBeastDeckInfo',
            'XZ4Kh7Ic' => 'beast_info',

            'XoPQ5Rs3' => 'UserQuestInfo',
            'hQKAwj18' => 'UserQuestSubInfo',
            'Z34jU7ue' => 'quest_id',
            '7Svg2kdT' => 'quest_sub_id',
            '4kcAD9fW' => 'quest_state',
            '0G7J2vfD' => 'quest_substate',
            'VjJQ51uG' => 'date_start',
            'm8ivD4NX' => 'date_end',

            '8J1R5PXG' => 'UserSwitchInfo',
            '5opcUT9t' => 'switch_id',

            'h5XGny0N' => 'UserGiftInfo',
            '2HqjK1F7' => 'gift_send_id',
            'QSf4NDq3' => 'gift_item_id',
            'AJGfV35v' => 'gift_handle_name_from',
            '9WsTg26F' => 'gift_unit_id_from',
            'wjX34YxG' => 'gift_unit_lvl_from',
            'm94IYp6a' => 'gift_friend_id_from',
            'bUfq8BJ3' => 'gift_friend_id_to',
            'A3j7Wxtw' => 'gift_date_str',
            'Igd28KXq' => 'gift_receive_state',

            '80nGg32w' => 'UserGachaInfo',
            'Lqenh7d2' => 'gacha_term_start',
            'u7dM3XwK' => 'gacha_term_end',
            '08MuyfqD' => 'gacha_term_over',
            'X1IuZnj2' => 'gacha_id',
            'nqzG3b2v' => 'gacha_detail_id',
            'L4QmV0Nz' => 'gacha_pull_count',

            'nSG9Jb1s' => 'UserArchiveInfo',
            'NYb0Cri6' => 'archive_id',
            '6gAX1BpC' => 'archive_value',

            ''         => '',
            'Jr6z91Tf' => 'UserAnnounceInfo',
            'X3bZ86C7' => 'announce_info',

            'Z53gL0RU' => 'UserNoticeInfo',
            'Gs6BT8n5' => 'target_year',
            'goAiX84L' => 'notice_info',

            'Bbc6pF9Q' => 'UserLoginBonusTotalInfo',
            '1ACjIe9S' => 'login_bonus_days_running',
            'cksL9bW8' => 'TotalLoginCompleteInfo',
            'fHSZ74Qm' => 'ProcessDay',

            'R5kozET8' => 'UserMedalExchangeInfo',
            'S21FT3L8' => 'medal_exchange_id',
            'ngQX7a3N' => 'medal_exchange_count',

            'wR18kvTe' => 'UserSpDungeonInfo',
            'n2zX3VpH' => 'dungeon_id',
            'Esxe71j3' => 'region_id',
            'Nf7IsB4A' => 'area_id',
            '9Pb24aSy' => 'world_id',
            '3rhPSiK1' => 'date_completion_str',
            'zI5a9LPo' => 'date_completion_unix',

            'nE7pV2wr' => 'UserCarryItemInfo',
            'jsvoa0I2' => 'item_carry_list',

            // UserEquipItemInfo
            'w83oV9uP' => 'UserEquipItemInfo',
            '4rC0aLkA' => 'UserItemInfo',
            'HpL3FM4V' => 'item_map',
            'Md0N5abE' => 'UserImportantItemInfo',
            '2dfixko3' => 'keyitem_id',
            'Qy5EvcK1' => 'count',

            // UserUnitDictionary
            'TJ9eL80N' => 'UnitEquip',
            '93Y6evuT' => 'EquipIdList',
            '80NUYFMJ' => 'MateriaIdList',
            'aS39Eshy' => 'UserMateriaInfo',

            // BeastMst
            'Iwfx42Wo' => 'beast_id',
            'cyzug01G' => 'beast_art',
            '79YMZxpD' => 'beast_icon',
            '7h6SJfAH' => 'beast_display_id',
            '3P5gCMaQ' => 'beast_board_id__UNUSED',
            'Mnm4UZT9' => 'beast_exp_bonus', // ItemExtBeast
            '5oQ2TBpX' => 'reward_type',
            'Hxy8VP56' => 'reward_param',
            'xFsTMd83' => 'index',
            '0A1BkNWb' => 'point',

            // beast board pieces mst
            '1S8P2u9f' => 'UserBeastPieceInfo',
            'E8WRi1bg' => 'piece_info',
            'wY0Wj9Bt' => 'beast_board_piece_id',
            'SVZ3fPh0' => 'beast_board_piece_childnodes', // next piece info
            '1XRtI2d9' => 'board_piece_pos',
            // 'Hxy8VP56' => 'beast_board_piece_is_root',

            // beast board pieces ext mst
            'FsRXaZ45' => 'beast_board_piece_ext_OpenCondInfo',

            // beast skill
            '76LymtEu' => 'beast_skill_id',

            '8gSkPD6b' => 'UserUnitInfo',
            '4pYnBMw7' => 'UserUnitInfo2', // item set total ? ? ?
            '0W93YZvw' => 'AffinityPoint',
            'xojJ2w0S' => 'SkillP',
            'L4i4418y' => 'ContinueCountTag',
            '6q35TL07' => 'ContinueCount',
            'ExQ7H1ok' => 'UserSwitchInfo',
            'TQ5AJd2K' => 'UserChallengeInfo',

            // GetUserInfo
            // +> MissionResumeInfo
            // +> RmResumeInfo
            '1Ke2wFgm' => 'MissionResumeInfo',
            'LJas52VE' => 'MissionState',
            '8prQv5nz' => 'MissionSuspend',
            'Gh92V3Tx' => 'TownId',
            'YJah04ud' => 'TownState',
            '6VA3jNPn' => 'ReinFriendId',
            'PMp0Af1d' => 'ContinueCnt',
            'Ur6CKS2e' => 'RmResumeInfo',
            'Q4kJKh8b' => 'RmEventInfo',
            'KMwJ3s6W' => 'RbResumeInfo', // ArenaResume
            'uC8E0opI' => 'BattleState',
            // -> SeasonEventGroupMst
            'ds1vP0GR' => 'SeasonEventGroupMst',
            '42B7MGIU' => 'SeasonEventGroupId',
            'pvS5A4kE' => 'SeasonEventId',
            '89EvGKHx' => 'TargetType',
            '6uIYE15X' => 'TargetId',
            // -> SeasonEventGroupAbilityMst (JP?)
            '6ePjU3Nw' => 'SeasonEventGroupAbilityMst',
            '6W7n8ryb' => 'TargetType1',
            '86JmXL41' => 'TargetId1',
            'b79qc2Uu' => 'TargetType2',
            '4z5vDrRK' => 'TargetId2',
            'peF96w8R' => 'TargetFlg2',
            '1G6xjYvf' => 'SeasonEventAbilityIdList',

            // -> BannerScheduleMst
            'jBL5XNf6' => 'BannerScheduleMst',
            'fSxqkF10' => 'banner_id',
            // -> UserFriendLvInfoResponse
            'UaPBS5H0' => 'UserFriendLvInfoResponse',

            // RoutineHomeUpdate
            'ofp9t72b' => 'SeasonEventScheduleMst',
            'f5bA4wFx' => 'DailyLoginBonusInfo',

            // RoutineWorldUpdate
            // -> EventSetMst
            '8KPfoy2F' => 'EventSetMst',
            'evr3RH1b' => 'eventset_id',
            'qnUhs32P' => 'eventset_type',
            'xHZj7Va5' => 'param',

            // -> SpDungeonCondMst
            '0xkoNP8U' => 'SpDungeonCondMst',
            '0iWgxc1X' => 'condition_type',
            'B2FW5Qjq' => 'conditions', // also ai

            // -> RoutineEventUpdate
            '8SztQck9' => 'RoutineEventUpdate',
            '0Zpuzj7E' => 'ArchiveUpdateFlg',
            'bimh54Da' => 'NextHomeUpdateTime', // +s

            // GachaExe
            //-> GachaExeReq/Res / UserGachaResult
            '5yLD4dYI' => 'UserGachaResult',
            '2RtVPS3b' => 'GachaActCnt',
            'zJ1A6HXm' => 'GachaTicketId',
            'UJz5QED2' => 'result',
            'zoU5J3Kq' => 'effect_pattern_id',
            'uR70J6Lr' => 'new_entries',
            'bp8yx3z6' => null, // jp only?
            'oY0vAuE9' => null, // jp only?
            // -> GachaScheduleMst
            '4D5k8dpI' => 'GachaScheduleMst',
            '4yT7FUqj' => 'banner_info',
            'T7aEx3Mj' => 'explain_short',
            'VC9F3eJn' => 'explain_long',
            'ShTbN3q9' => 'explain_shop', // ImportantItemExplainMst
            '5vFjunq1' => null, // jp only?
            'u3bysZ8S' => null, // jp only?
            '2twsMyD6' => null, // jp only?
            'NTXoA6e2' => null, // jp only?
            '7Gvw6J82' => null, // jp only?
            // -> GachaMst (Reponse)
            'PcUrGH13' => 'GachaMst',
            'XFLm4Mx6' => 'image_button',
            'g38Pjk10' => 'anime_file',
            'JDFMe3A7' => 'anime_name',
            'GBZbgp18' => null,// jp only?
            '8s9wNSpd' => null,// jp only?
            // -> GachaDetailMst
            'IC0ueV5s' => 'GachaDetailMst',
            'ijEey5f7' => 'exe_type',
            '0GgK6ytT' => 'cost_info',
            'RZ6z12Dt' => 'TicketGroupId',
            '1ovyP04j' => 'ExeLimit',
            'rnks3B0q' => null, // jp only?
            '38r1hkcR' => null, // jp only?
            '3v9z2p1N' => null, // jp only?

            'fMohhKK8' => 'BundleMst',
            'EzXXHtTY' => 'bundle_id',
            '3GtkuMJ1' => 'bundle_name_var',
            '70oO1OaR' => 'bundle_desc_var',
            'pRYHbNN5' => 'currency_type',
            'AOHRcCC2' => 'price',
            "cSUSxGZA" => 'bundle_icon',
            'MfynwFte' => 'bundle_bg',
            'hJs2B3X5' => 'banner',
            'yZxOduZT' => 'bundle_type',
            'QvqubbO7' => 'bundle_order',
            'frHK6VA9' => 'sale_price',
            'ss30FUrW' => 'sale_date_start',
            'qM65cmnX' => 'sale_date_end',
            'gzWIBU0I' => 'purchase_limit',
            'lxXnQUZT' => 'daily_refresh',
            'JSogz3jg' => 'daily_refresh_time',
            'fW193id7' => 'bundle_state_dup',
            'TzvJwA60' => 'product_id',
            '6tSP4s8J' => 'bundle_state',

            'pcjqhOI8' => 'BundleItemMst',
            '4FF0gz8P' => 'bundle_item_id',
            'YWF08vZS' => 'bundle_item_type',
            '5GNDIwPX' => 'bundle_item_quality',
            '7Y9SVTyB' => 'bundle_item_rarity',
            'ddCbuITy' => 'bundle_item_order',
            '74wLIT8O' => 'bundle_item_state',

            // MissionResult
            '09HRWXDf' => 'MissionResult',
            'gq0jowm2' => 'isRaidMission', // if < 1
            // MissionStateInfo
            'S4u09svh' => 'getItemNum(0,0,0,99)',
            'Zgsr7t06' => 'getItemNum(0,18,0,99)',
            'Wdi3mas2' => 'reward_gil',
            'p1K5NecV' => 'FirstClearBonus',
            "S4U09svH" => 'add_item_num_exp_0_0_0',  // kill exp
            "ZGSr7T06" => 'add_item_num_exp_0_18_0',
            "Wdi3MAs2" => 'add_item_num_gil_0_0_0',  // kill gold
            "Syar71nw" => 'add_item_num_gil_0_2_0',
            "8CfoLQv5" => 'add_item_num_gil_0_4_0',
            'wQhu9G7n' => 'add_item_num_gil_0_18_0',
            'xF9Sr1a6' => 'add_item_csv_20_0',
            'JHMit30L' => 'add_item_csv_20_1',
            'Sp7fF6I9' => 'add_item_csv_20_2',
            '02aARqrv' => 'add_item_csv_20_3',
            'Z7G0yxXe' => 'add_item_csv_20_5',
            'EWmQy52k' => 'add_item_csv_20_6',
            '81fnjKLw' => 'add_item_csv_21_0',
            '3imo7rf4' => 'add_item_csv_21_1',
            'Iyx9Bos7' => 'add_item_csv_21_2',
            'VZ8DSco1' => 'add_item_csv_21_3',
            'Mh1BA4uv' => 'add_item_csv_21_5',
            'XMgu7qE0' => 'add_item_csv_21_6',
            '34pqHBIC' => 'add_item_csv_22_0',
            '0w6yHTEz' => 'add_item_csv_22_1',
            'E6kmo39Q' => 'add_item_csv_22_2',
            'ypU1T0kR' => 'add_item_csv_22_3',
            '30u6XYiB' => 'add_item_csv_22_5',
            '1Z2TD5ai' => 'add_item_csv_22_6',
            'Z6yB9eYd' => 'add_item_csv_23_0', // key item
            'aqH0nm3Y' => 'add_item_csv_23_1', // key item
            '3ueDCf8Y' => 'add_item_csv_23_2', // key item
            '2LqvowY8' => 'add_item_csv_23_3', // key item
            '95JxZzuG' => 'add_item_csv_23_5', // key item
            '7rA9WGQe' => 'add_item_csv_23_6', // key item
            'R1kg3EBA' => 'add_item_csv_60_5',

            '2U05aV9Z' => 'MissionResultInfo::getUnitCsv(v1, &v26)',
            '7a1Ugx4e' => 'getItemNum(v1, 80, 0, 0, 0)',
            't4v2o0zM' => 'getItemNum(v1, 80, 0, 15, 0)',
            'PB3vLE2r' => 'getItemNum(v1, 80, 0, 12, 0)',
            'aK4k1PvY' => 'getItemNum(v1, 80, 0, 14, 0)',
            'NCFk6Zv1' => 'getItemNum(v1, 80, 0, 13, 0)',

            'nar74pDu' => 'seen_monster_ids',
            'Dq4V7FtU' => 'libra_monster_unit_ids',
            'f6M1cJgk' => 'knockdown_monster_unit_count',
            'uU21m4ry' => 'knockdown_monster_parts_count',
            'V59rxm82' => 'getEncountFieldInfoCsv',
            'dvM0tR42' => 'getLearningIdCsv(v1, &v26)',

            'REB5tAL9' => 'add_reward_item_1', // Trust Master bonus
            'Ym8F0ndw' => 'add_reward_item_2', // Trust Master bonus
            'ERJa3SL5' => 'add_reward_item_3', // Trust Master bonus
            '9ibA0nq4' => 'add_reward_item_4', // Trust Master bonus
            'rw10CLoG' => 'add_reward_item_5', // Trust Master bonus
            'tzCNMJ87' => 'TeamLvInfoUpdate',
            'Qic38SuC' => 'setUnitLvInfo',
            'A90DrNfp' => 'setUseLimitBurstCnt',
            // 'eAM5B1qt' => 'addItemList',
            'GfQmi4h5' => 'setLevelupBonusDiaNum',
            'rxz6nXY3' => 'setNewUnitIdList',
            'Vy9YXBE7' => 'addEventBonusItem',
            // 'qdhWF7z9' => 'UserChallengeInfoList',
            '42dKqYHe' => 'setTotalDamage',

            //
            '2urzfX6d' => 'MissionChallengeLog',
            'qdhWF7z9' => 'cleared',
            'wf5Toh4k' => 'cleared_new',
            //
            'Pqi5r1TZ' => 'ability_log',
            'i1yJ3hmT' => 'magic_log',
            's2rX7g0o' => 'lb_log',
            'qr5PoZ1W' => 'elem_log',
            '4p6CrcGt' => 'item_log',
            '9JBtk2LE' => 'beast_log',
            '73CfbLEx' => 'ko_log',
            'Z1p0j9uF' => 'log_dead_count',
            '69ieJGhD' => 'battle_clear_log',
            ''         => '',

            // Arena stuff
            ''         => '',

            'g4Pu8oUt' => 'UserRbInfo',
            // 'UbQ26oH4'         => '',
            // 'mRY52Tib'         => '',
            // '5svT7qLR'         => '',
            // 'egayI1s4'         => '',
            // 'p6H4CNw8'         => '',
            // 'Ka0Pbz5N'         => '',
            // 'qH5h4GX2'         => '',
            // 'AU4qRzL9'         => '',
            // '52ibRBqw'         => '',

            'Tr9m32i1' => 'RbMatchingListInfo',
            'TI6deE8P' => 'content', // RbMatchingInfo

            'h5zN9MvP' => 'RbRankingStateInfo',
            'm9A4tK2r' => 'SeasonState',
            '3HbFrqo5' => 'TermType', // mined
            'BGFw7qi4' => 'SumStartDate',
            '2oL4dNFy' => 'SumEndDate',
            '0dh7yRIN' => 'InAggregateMessage',

            'yT12twnc' => 'OpeRbInfo',
            'S3rek7hC' => 'season_id',
            'o8RDmSI9' => 'long_season_id',

            'H54XvPis' => 'RbTradeBoardGroupMst',
            'CIE1rky2' => 'arena_board_group_id',
            '35iR6h0V' => 'BoardInfo', // mined
            '6m4Rn5jd' => 'arena_board_id',

            '2pJ8F0SK' => 'UserRbTradePieceInfo',
            '3qJw1vUG' => 'arena_board_piece_info',

            'ki9hJ5vq' => 'UserRbTradeCompleteInfoResponse',
            'N9Aw4kxC' => 'CompState',
            'KA68CYFo' => 'RoundCount',

            // GetReinforcementResponse
            'I53AVzSo' => 'OpeFriendInfoResponse',
            '56gJBnVh' => 'FriendUnitInfoResponse',
            'pzf5se6V' => 'ReinforcementResponse',
            'b21IQLY0' => 'GuestMstResponse',

            // UserMailInfo
            'qja26wHb' => 'UserMailInfo',
            'ynP26rjD' => 'mail_id',
            'fbcj5Aq9' => 'title',
            '9m5yIkCK' => 'icon_type',
            'u7Z1DYz4' => 'attach_type',
            '7iJpH5zZ' => 'target_info',
            '14Y7jihw' => 'receive_type',
            'f5LHNT4v' => 'send_date',
            'k0qX5yQd' => 'open_date',
            '0Sba4mZ9' => 'receive_date',

            // UserUnitSkillSublimationInfoResponse
            'Duz1v8x9' => 'UserUnitSkillSublimationInfo',
            '1MH7Na23' => 'skill_id_old',
            '6bHxDEL0' => 'skill_id_new',

            ''         => '',

            // sgExpeditionMst
            'Ko86047K' => 'ExpdId',
            'Up7229W2' => 'DifficultyId',
            'sZ4964o7' => 'NextExpdId',
            'Tr827450' => 'RewardGroupId',
            'sx4kyW0i' => 'type',
            '7LIoPpG9' => 'cost',
            'X34v87r7' => 'TimeEventFlags',
            'aP17413E' => 'weight_mag',
            'Xs627g95' => 'weight_spr',
            '9Fg19502' => 'MaxContributionPerChar',
            '8s85O72e' => 'UnitCount',
            'Y7fa7225' => 'RequiredUnitSeriesList',
            'fH306N14' => 'RecommendedType',
            'sO308L0M' => 'ConsumableItemList',
            '4Ya160s7' => 'DisplayReward',
            '5Yn160s8' => 'RelicReward',
            'kN017582' => 'ChallengeValue',

            // 'i8lR1228' => 'unused1',
            // 'f810OI8s' => 'unused2',
            // 'HVEqbROQ' => 'unused3',
            '2x2W5439' => 'TimeEventStart',
            'N2PkD9sy' => 'date',
            '0HJs6nFe' => null,

            '57oY53S5' => 'DifficultyGroupId',
            '7Ac7722s' => 'Duration',
            '2A07z50y' => 'Points',

            // requests
            '67l03PW2' => 'sgExpdQuestRefreshRequest',

            // JP explos
            'w46Zt1MT' => 'ExploreScheduleMst',
            'fK3W9qYA' => 'ScheduleId',
            "x5huPsU6" => 'AreaId',
            "wh6U0f1Z" => 'AreaId',

            "2J1wCyhQ" => 'UserExploreInfo',
            "FHk74LUd" => 'FrameNo',
            "9sE0nJPi" => 'TimeId',
            "RBcpE27S" => 'state',
            "TIpgd4Q0" => 'ResultState',
            "P7sEZ0vN" => 'RewardInfo',
            "9yEe4dt6" => 'RewardLimitDate',
            "uX54AqwV" => 'IsNew',
            "4P7BaEed" => 'date_end',

            // SEs
            'jSC4Tx9H' => 'UserRmActualInfo',
            'h0XJ2nPj' => 'PtActualInfo',
            '1w6Ju7Ie' => 'PtBeastInfo',
            '0VBhN1UC' => 'FriendInfo',
            'Zh73m5Nq' => 'FriendActualInfo',
            ''         => '',
            ''         => '',
        ];

        /**
         *
         */
        public static function init() {
            $file = ROOT_DIR . "/files.tsv";

            self::$files = [];
            self::$names = [];

            foreach (file($file) as $k => $row) {
                if ($k === 0)
                    continue;

                $row = trim($row);
                if (empty($row))
                    continue;

                $row = explode("\t", $row);

                list($name, $class, $file, $key, $type, $notes) = $row;
                if (isset(static::$names[$name]))
                    continue;

                $notes = explode(' ', $notes);
                $entry = new GameFile($name, $file, $key, $class, $type, $notes);
                static::addEntry($entry);
            }
        }

        public static function addEntry(GameFile $entry) {
            self::$files[$entry->getFile()] = $entry;

            if ($entry->getName() != '-' && $entry->getName() != null)
                self::$names[$entry->getName()] = $entry;

            if ($entry->getClass() != '-' && $entry->getClass() != null)
                self::$names[$entry->getClass()] = $entry;
        }

        public static function save() {
            $file  = ROOT_DIR . "/files2.tsv";
            $lines = [
                'NAME	CLASS	FILE	KEY	TYPE	NOTE',
            ];

            uasort(static::$files, function (GameFile $a, GameFile $b) { return $a->getName() <=> $b->getName(); });

            foreach (static::$files as $entry)
                $lines[] = implode("\t", [
                    $entry->getName(),
                    $entry->getClass(),
                    $entry->getFile(),
                    $entry->getKey(),
                    $entry->getType(),
                    join(' ', $entry->getNotes()),
                ]);

            file_put_contents($file, implode("\n", $lines));
        }

        /**
         * @param string $input File name or identifier
         *
         * @throws \Exception
         *
         * @return array
         */
        public static function loadRaw($input) {
            $file = self::getFilePath($input);

            // read file
            $data = file($file);
            $data = array_map(function ($row) { return json_decode($row, true); }, $data);

            return $data;
        }

        /**
         * @param string $input File name or identifier
         *
         * @return array
         */
        public static function loadMst($input) {
            $file = self::getFilePath($input);

            // read file

            $file = new \SplFileObject($file);
            $file->setFlags(\SplFileObject::DROP_NEW_LINE | \SplFileObject::SKIP_EMPTY);

            $entries = [];
            foreach ($file as $line) {
                if ($line === false)
                    continue;

                $row = trim($line);
                $row = mb_convert_encoding($row, 'UTF-8');
                $row = json_decode($row, true);

                if ($row == null)
                    continue;

                $row       = self::replaceKeys($row);
                $entries[] = $row;
            }

            return $entries;
        }

        public static function decodeAll($region = 'gl') {
            if (self::$files == null)
                self::init();

            // foreach (['gl', 'jp'] as $region)
            foreach (self::$files as $file => $entry) {
                try {
                    $versions = self::getFileVersions($entry, $region);
                    if (empty($versions))
                        throw new \Exception("No files found for {$entry->name}: {$entry->file}.");

                    $version = max($versions);

                    echo "{$entry->name}\n";
                    self::decodeFile(
                        DATA_ENCODED_DIR . "{$region}/{$entry->name}_v{$version}.txt",
                        DATA_DECODED_DIR . "{$region}/{$entry->name}.txt",
                        $entry->key
                    );

                    if (filesize(DATA_DECODED_DIR . "{$region}/{$entry->name}.txt") == 0)
                        throw new \Exception('Empty output');

                } catch (\Exception $e) {
                    print "File {$entry->name}: {$entry->file}.{$region} could not be decoded: {$e->getMessage()}\n";
                }
            }
        }

        public static function decodeFile($in_path, $out_path, $key) {
            if (!file_exists($in_path))
                throw new \LogicException("File does not exist: {$in_path}");

            if (strlen($key) != 8)
                throw new \LogicException("Invalid key: {$key}");

            $data = file_get_contents($in_path);
            $data = AES::decode($data, $key);

            file_put_contents($out_path, $data);
        }

        public static function getVersions($region) {
            $files = glob(DATA_ENCODED_DIR . "{$region}/Ver*_*.dat");
            if (empty($files))
                return [];

            // trim files
            $versions = [];
            foreach ($files as $file) {
                $filename = basename($file, '.dat');
                $filename = substr($filename, 3);
                var_dump($filename);
                die();
            }

            natsort($versions);

            return $versions;
        }

        public static function getFileVersions(GameFile $entry, $region = 'gl') {
            $versions = glob(DATA_ENCODED_DIR . "/{$region}/{$entry->getName()}_v*.txt");
            if (empty($versions))
                return [];

            // trim files
            $versions = array_map(function ($file) {
                $file = basename($file, '.txt');
                $file = substr($file, strrpos($file, "_v") + 2);

                assert(is_numeric($file));

                return (int) $file;
            }, $versions);

            natsort($versions);

            return $versions;
        }

        public static function replaceKeysRecursive(array $data) {
            foreach ($data as $key => $value) {
                unset($data[$key]);

                $key = static::$keys[$key] ?? $key;

                if ($key === null)
                    continue;

                if (isset($data[$key]))
                    if ($data[$key] != $value)
                        $key = "{$key}_2";
                    else
                        print "Warning: key {$key} already exists and data will be overwritten.\n";

                if (is_array($value))
                    $data[$key] = self::replaceKeysRecursive($value);

                else
                    $data[$key] = $value;
            }

            return $data;
        }

        public static function getFileKey($filename) {
            $entry = static::getEntry($filename);

            return $entry == null
                ? null
                : $entry->key;
        }

        public static function getRegion() {
            return static::$region;
        }

        public static function setRegion($string) {
            static::$region = $string;
        }

        public static function getEntry($file) {
            if (self::$files == null)
                self::init();

            return self::$files[$file] ?? self::$names[$file] ?? null;
        }

        /**
         * @param string $input
         *
         * @throws \LogicException
         *
         * @return string
         */
        public static function getFilePath($input): string {
            if (self::$files == null)
                self::init();

            // find file
            $input = static::$files[$input] ?? static::$names[$input] ?? $input;
            if (!$input instanceof GameFile)
                throw new \LogicException("Invalid file name or id '{$input}'.");

            // override region for localization data
            if (substr($input->getName(), 0, 6) == 'F_TEXT')
                $region = 'gl';
            else
                $region = static::$region;

            $file = DATA_DECODED_DIR . "/" . $region . "/{$input->getName()}.txt";

            if (!file_exists($file))
                throw new \LogicException("File {$file} for {$input->name} missing!");

            return $file;
        }

        #region Keys

        /**
         * @return GameFile[]
         */
        public static function getEntries() {
            if (self::$files == null)
                self::init();

            return static::$files;
        }

        /**
         * @param array $row
         *
         * @return array
         */
        private static function replaceKeys(array $row) {
            foreach ($row as $key => $val) {
                unset($row[$key]);

                if (!array_key_exists($key, static::$keys)) {
                    // unknown key
                    $row[$key] = $val;
                    continue;
                }

                $newKey = static::$keys[$key];

                if ($newKey == null)
                    // unset field
                    continue;

                if (isset($row[$newKey]))
                    print "Warning: key {$newKey} already exists and data will be overwritten.\n";

                $row[$newKey] = $val;

                //            else
                //                $row['unknown'][$key] = $val;
            }

            return $row;
        }

        /** @var string */
        protected $name;
        /** @var string */
        protected $file;
        /** @var string */
        protected $key;
        /** @var string */
        protected $class;
        /** @var string */
        protected $type;
        /** @var string[] */
        protected $notes;

        /**
         * GameFile constructor.
         *
         * @param string   $name
         * @param string   $file
         * @param string   $key
         * @param string   $class
         * @param string   $type
         * @param string[] $notes
         */
        public function __construct($name, $file, $key, $class = null, $type = null, $notes = []) {
            $this->name  = $name;
            $this->file  = $file;
            $this->key   = $key;
            $this->class = $class;
            $this->type  = $type;
            $this->notes = $notes;
        }

        /**
         * @return string
         */
        public function getName(): string {
            return $this->name;
        }

        /**
         * @param string $name
         */
        public function setName(string $name) {
            $this->name = $name;
        }

        /**
         * Returns the hashed file name
         *
         * @return string
         */
        public function getFile(): string {
            return $this->file;
        }

        /**
         * @param string $file
         */
        public function setFile(string $file) {
            $this->file = $file;
        }

        /**
         * @return string
         */
        public function getKey(): string {
            return $this->key;
        }

        /**
         * @param string $key
         */
        public function setKey(string $key) {
            $this->key = $key;
        }

        /**
         * @return string
         */
        public function getClass(): string {
            return $this->class;
        }

        /**
         * @param string $class
         */
        public function setClass(string $class) {
            $this->class = $class;
        }

        /**
         * @return string
         */
        public function getType(): string {
            return $this->type;
        }

        /**
         * @param string $type
         */
        public function setType(string $type) {
            $this->type = $type;
        }

        /**
         * @return null
         */
        public function getNotes() {
            return $this->notes;
        }

        /**
         * @param string[] $notes
         */
        public function setNote($notes) {
            $this->notes = $notes;
        }

        public function getDlType() {
            if (substr($this->name, 0, 7) == "F_TEXT_")
                return 'localized_texts';

            return 'mst';

        }
        #endregion
    }