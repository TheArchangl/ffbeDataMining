<?php
    /**
     * User: aEnigma
     * Date: 12.02.2018
     * Time: 23:14
     */

    use Sol\FFBE\GameFile;

    require_once dirname(__DIR__) . "/bootstrap.php";

    $blubb = [
        'jp' => <<<EOF
EOF
        ,
        'gl' => <<<EOF
F_AI_MST	AIMstList	PCm9K3no	yFr6Kj3P	mst	
F_LEARNING_MST	LearningMstList	DLVF0cN1	4HCdYk80	mst	
F_BATTLE_BG_MST	BattleBgMstList	bqR4p8SN	39mEiDNB	mst	
F_LIMITBURST_LV_MST	LimitBurstLvMstList	0EvyjKh8	g2hZpVW7	mst	
F_TEXT_BATTLE_SCRIPT	BattleScriptMstList	dhR7sSxt	QuxKHQQT	mst	
F_MAGIC_MST	MagicMstList	6J8jwSDW	2zyP4WQY	mst	
F_TRIBE_MST	TribeMstList	3Lc1DWQV	adj41LQC	mst	
F_ABILITY_MST	AbilityMstList	TK61DudV	4sPQ8aXo	mst	
F_LIMITBURST_MST	LimitBurstMstList	c5T4PIyL	6q3eIR9k	mst	
F_LIMITED_SKILL_MST	sgLimitedSkillMstList	V6Qke9wf	UyQxAWBZ	mst	
F_UNIT_CLASS_UP_MST	UnitClassUpMstList	60FtuAhp	pjW5TI0K	mst	
F_NV_EX_CLASS_UP_MST	NvExClassUpMstList	0vf1STU2	Uoqk1TX2	mst	
F_UNIT_EXP_PATTERN_MST	UnitExpPatternMstList	kMnY10ry	B38YWDtF	mst	
F_NV_SHIFT_MST	NvShiftMstList	sT9k7wLE	70LnQ6UA	mst	
F_UNIT_ROLE_MST	UnitRoleMstList	HgULPb69	eAz6X15L	mst	
F_UNIT_MST	UnitMstList	SsidX62G	UW0D8ouL	mst	
F_JOB_MST	JobMstList	F2mQ87Wt	3CVoZu7s	mst	
F_UNIT_SERIES_LV_ACQUIRE_MST	UnitSeriesLvAcquireMstList	Eb5ewi87	t8QV2WvE	mst	
F_GAME_TITLE_MST	GameTitleMstList	91D3CfxA	yFZs58K7	mst	
F_UNIT_GROW_MST	UnitGrowMst	i3YrEM1t	6cUguh10	mst	
F_SEASON_EVENT_ABILITY_MST	SeasonEventAbilityMstList	VMIe9c6U	Na71Z6kg	mst	
F_SEASON_EVENT_ABILITY_TYPE_MST	SeasonEventAbilityTypeMstList	I0SUr3WY	XY2MA1x3	mst	
F_SEASON_EVENT_GROUP_FRIEND_LV_MST	SeasonEventGroupFriendLvMstList	v5aCt9ne	g7VmK5a9	mst	
F_BEAST_BOARD_PIECE_MST	BeastBoardPieceMstList	E9z2e4UZ	XvkVU34H	mst	
F_BEAST_EXP_PATTERN_MST	BeastExpPatternMstList	42JaImDh	UY7D3dpn	mst	
F_BEAST_GROW_MST	BeastGrowMstList	tSb4Y8QR	7YUDsew3	mst	
F_BEAST_SKILL_MST	BeastSkillMstList	zE1hx53P	Y4Fds1Jr	mst	
F_BEAST_STATUS_MST	BeastStatusMstList	tWp7f5Ma	XuWn97Hf	mst	
F_BEAST_CP_MST	BeastCpMstList	dzA5MC6f	Syax34vR	mst	
F_BEAST_BOARD_PIECE_EXT_MST	BeastBoardPieceExtMstList	5o0Z6Gwn	Knh15FzM	mst	
F_BEAST_MST	BeastMstList	WF57bvfG	hdFeT14k	mst	
F_CHAIN_FAMILY_DETAIL_MST	ChainGroupDetailMstList	wBnzcySj	AxShHtr3	mst	
F_CHAIN_FAMILY_MST	ChainGroupMstList	d2mmSNEP	6swDqsej	mst	
F_RECIPE_MST	RecipeMstList	27pGMZDm	JveAwh98	mst	
F_AWARD_TYPE_MST	AwardTypeMstList	jsX49HtE	t3IEWke8	mst	
F_AWARD_MST	AwardMstList	xH4WGNk8	HZgmc2u9	mst	
F_MAGIC_EXPLAIN_MST	MagicExplainMstList	8qbVQUx5	8LE2hk3r	mst	
F_UNIT_EXPLAIN_MST	UnitExplainMstList	Da62x85q	zU6L4Gng	mst	
F_IMPORTANT_ITEM_EXPLAIN_MST	ImportantItemExplainMstList	Nny6xD90	89fcSX4v	mst	
F_ITEM_EXPLAIN_MST	ItemExplainMstList	E7YMdK3P	TERM4PD7	mst	
F_ABILITY_EXPLAIN_MST	AbilityExplainMstList	7FGj2bCh	B2Ka3kft	mst	
F_MONSTER_DICTIONARY_EXPLAIN_MST	MonsterExplainMstList	Svq1K0rh	wA4Dxp1m	mst	
F_MATERIA_EXPLAIN_MST	MateriaExplainMstList	bW2GHT1N	7mof2RVP	mst	
F_EQUIP_ITEM_EXPLAIN_MST	EquipItemExplainMstList	psuJ5VE2	r6PSK8QW	mst	
F_ARCHIVE_MST	ArchiveMstList	Wmd5K32b	t3T1hELp	mst	
F_SP_CHALLENGE_MST	SpChallengeMstList	4b6NcpLo	wd1t3MPf	mst	
F_SUBLIMATION_RECIPE_MST	SublimationMstList	M9K0SJgR	vnkfer76	mst	
F_SACRIFICE_MST	SacrificeMstList	w5JFGPh3	J2u0YBPr	mst	
F_CHARACTER_MST	CharacterMstList	cf29diuR	ru39Q4YK	mst	
F_TROPHY_MST	TrophyMstList	9ZN4Eo1m	5qRZs6Kz	mst	
F_TROPHY_REWARD_MST	TrophyRewardMstList	GN1bMQm3	KyAC0q3D	mst	
F_TROPHY_METER_SERIF_MST	TrophyMeterSerifMstList	4Qz7qK51	SGjk89Tr	mst	
F_GALLERY_MST	GalleryMstList	C3PUoJ4T	3ZKzx4uV	mst	
F_UNIT_TRANSLATE_RATE_MST	UnitTranslateRateMstList	87p6gkuV	skp9EyT5	mst	
F_VISION_CARD_ABILITY_GROUP_MST	VisionCardAbilityGroupMstList	2wHP4tXr	PI8sH9nC	mst	
F_VISION_CARD_ABILITY_ACT_COND_MST	VisionCardAbilityActCondMstList	jTGeKc87	S0ATBUP1	mst	
F_VISION_CARD_CLASS_UP_MST	VisionCardClassUpMstList	61Bheyqc	ojEX7fz9	mst	
F_VISION_CARD_GROW_MST	VisionCardGrowMst	utBeD01i	U6iuscS1	mst	
F_VISION_CARD_EXPLAIN_MST	VisionCardExplainMstList	y8G2PKDk	U0jS43vH	mst	
F_VISION_CARD_EXP_PATTERN_MST	VisionCardExpPatternMstList	8i9R2qUY	pk73b1XU	mst	
F_VISION_CARD_MST	VisionCardMstList	G9K8kLXz	6MS9LGhA	mst	
F_VISION_CARD_LV_ACQUIRE_MST	VisionCardLvAcquireMstList	9uqRjwS6	MI28ZNHo	mst	
F_EFFECT_MST	EffectMstList	HfRPdg65	d4KR8YSq	mst	
F_EFFECT_GROUP_MST	EffectGroupMstList	ZM06fUem	4WipzuH2	mst	
F_ANIMATION_MST	AnimationMstList	N87mXCEp	6BmdUqg2	mst	
F_TEAM_LV_MST	UserLevelMstList	NCPTw2p0	qP7TGZE8	mst	
F_LOGIN_BONUS_MST	LoginBonusMstList	P3raZX89	8xQP3fUZ	mst	
F_COMEBACK_LOGIN_MST	ComebackLoginBonusMstList	ElNwW49V	kR71T42e	mst	
F_LOGIN_BONUS_TOTAL_REWARD_MST	LoginBonusTotalRewardMstList	J6dVmNv5	y57ZhtLI	mst	
F_LOGIN_BONUS_SP_MST	SpecialLoginBonusMstList	gjkPJ95T	o64t9Qmd	mst	
F_LOGIN_BONUS_SP_REWARD_MST	SpecialLoginBonusRewardMstList	JQ1c6DCd	Ym2rpIA7	mst	
F_EXVIUS_POINT_REWARD_MST	ExviusPointRewardMstList	jiSF1p9I	h8x1tiwz	mst	
F_CLSM_ROUND_MST	ClsmRoundMstList	7zEc85n1	V4wZA7Hn	mst	
F_CLSM_GRADE_MST	ClsmGradeMstList	q14CSpIb	sn8QPJ24	mst	
F_CLSM_PROGRESS_MST	ClsmProgressMstList	pF3JiA6L	h8Za6TiL	mst	
F_CLSM_RANK_MST	ClsmRankMstList	W2f5GThw	jtbur0h5	mst	
F_CHALLENGE_MST	ChallengeMstList	GL9t6cMF	8Dx2QUZS	mst	
F_UNIT_SERIES_CHALLENGE_MST	sgUnitSeriesChallengeMstList	UjyC72ud	TNaqqXrS	mst	
F_STORY_EVENT_MST	StoryEventMstList	YW9MUm2H	LyjM2nU9	mst	
F_CAPTURE_MST	CaptureMstList	65fHqgnT	w1A9S5sr	mst	
F_COMMUNITY_DUNGEON_MST	sgCommunityDungeonMstList	6DDX7Kbk	TPWnfEAt	mst	
F_ENCOUNT_FIELD_MST	EncountFieldMstList	2Jtkc4Ar	mhok0p7B	mst	
F_SG_TIME_DUNGEON_MST	TimeMissionMstList	pnjSbo89	UhUsWVhw	mst	
F_EXPLORE_AREA_MST	ExploreAreaMstList	b2VdS9Pv	6wm49Yur	mst	
F_EXPLORE_TIME_MST	ExploreTimeMstList	1jVfJ6CB	0WaTe2ZH	mst	
F_SUMALLY_REWARD_MST	SumallyRewardMstList	RU2MH9o5	8HXr5ZMb	mst	
F_RULE_MST	RuleMstList	7s4egUBN	sf2o1jWL	mst	
F_DEFINE_MST	DefineMst	zg5P1AxM	Zib6m5ed	mst	
F_LOCALIZED_TEXT_MST	sgSplitLocalizedTextMstList	i5QEJpua	R8PYPnJ9	mst	
F_EXCHANGE_SHOP_ITEM_MST	ExchangeShopItemMstList	5hYf3xv1	sg3fXED8	mst	
F_MYCARD_MST	MyCardMstList	gJTPrUcS	eEEER1cd	mst	
F_ICON_MST	IconMstList	LM1APs6u	8XT23CYy	mst	
F_POPUP_MST	sgGeneralPopupMstList	DCgG5vBr	zjRdCncr	mst	
F_URL_MST	UrlMst	9wmd4iua	UREmi85S	mst	
F_DIAMOND_MST	DiamondMstList	mo47BaST	p9sk1MjH	mst	
F_FUNCTION_MST	FunctionMstList	6wibj9m8	sy6GRuY2	mst	
F_SHOP_MST	ShopMstList	1ks9q4Pj	X0FA6Ewh	mst	
F_SWITCH_TYPE_MST	SwitchTypeMstList	PJw28YRg	5NUkhS4Q	mst	
F_BANNER_MST	BannerMstList	oB0YVw67	C6ci5pfG	mst	
F_SWITCH_MST	SwitchMstList	T46RpNZH	AUx51pni	mst	
F_PICTURE_STORY_MST	PictureStoryMstList	PSDIR8b1	SDV4mZL0	mst	
F_RESOURCE_CHECKSUM_MST	ResourceSizeMstList	9Hwur83G	92SkMNAx	mst	
F_RESOURCE_VERSION_MST_LOCALIZE	ResourceVersionMstLocalizeList	A1L1GlaQ	JchlFPWK	mst	
F_RESOURCE_MAP_VERSION_MST_LOCALIZE	ResourceMapVersionMstLocalizeList	YIgCpDKW	geHlpoos	mst	
F_RESOURCE_MAP_CHECKSUM_MST	ResourceMapSizeMstList	2RZ4uDoK	29Rd7ra0	mst	
F_PRODUCT_MST	productMstList	DEC8Jmy2	OEl4ZmDY	mst	
F_FIXED_UNIT_MST	FixedPartyUnitMstList	22Yd29Ft	LjXDtZEQ	mst	
F_FIXED_BEAST_MST	FixedPartyBeastMstList	VmvPBaqv	tEVAFPmf	mst	
F_FIXED_PARTY_MISSION_MST	FixedPartyMissionMstList	zyG7uDTr	Tjtz9UPY	mst	
F_MAP_EVENT_MST	MapEventMstList	91vNbdxg	1YAk2fcr	mst	
F_STRONGBOX_MST	StrongBoxMstList	X1xsdRk7	qPpXT0Z2	mst	
F_MAP_OBJECT_MST	MapObjectMstList	7d25gta1	1zYSh5ps	mst	
F_AREA_MST	AreaMstList	Fvix6V0n	pR2DHS6i	mst	
F_MISSION_MST	MissionMstList	24Eka6wL	naJ3P84b	mst	
F_TOWN_MST	TownMstList	6P9XZ7ts	IavY38oB	mst	
F_IMAGE_SWITCHING_MST	MapImageReplaceMstList	K4rvk96u	Ne92GPyR	mst	
F_MISSION_NOTICE_MST	MissionNoticeMstList	MTd4qG09	05PtnLXf	mst	
F_LAND_MST	LandMstList	23yVcGpH	Y9wKxzI0	mst	
F_FOOTPRINT_MST	FootPrintMstList	q3jTCo8m	iI7aS2oq	mst	
F_WORLD_MST	WorldMstList	JLxQW68j	t8bWUq9Z	mst	
F_MAP_ROUTE_MST	MapRouteMstList	F1a5PZYq	rASW8N5L	mst	
F_MAP_EXT_RESOURCE_MST	MapExtResourceMstList	En90A5BC	9dA6bguS	mst	
F_DUNGEON_MST	DungeonMstList	1Bj4oy5Q	9bEA2SYP	mst	
F_TOWN_STORE_COMMENT_MST	StoreCommentMstList	ASh8IH0b	1L7oneM4	mst	
F_TOWN_STORE_MST	TownStoreMstList	v34C8PdG	Si9bxe86	mst	
F_GACHA_SELECT_PRISM_MST	sgGachaSelectPrismMstList	9i6xZoRK	p2jfDqJD	mst	
F_GACHA_EFFECT_PATTERN_MST	GachaEffectPatternMstList	uw07SXpU	48HhnaKP	mst	
F_GACHA_WHEEL_EFFECT_MST	sgAdsGachaEffectMstList	6dlDoBxM	Tgwqv2Tp	mst	
F_GACHA_EFFECT_BLOCK_MST	GachaEffectBlockMstList	fDI1T97q	7H3P6zF4	mst	
F_GACHA_SELECT_UNIT_MST	GachaSelectUnitMstList	qRb73JSY	H9Jye1j3	mst	
F_GACHA_DETAIL_EXTRA_MST	sgGachaDetailExtraMstList	p3NngNXw	7jxqirMk	mst	
F_DESCRIPTION_FORMAT_MST	DescriptionFormatMstList	HL3rXQh7	WvJI5AB8	mst	
F_PLAYBACK_SEASON_MST	PlaybackSeasonMstList	D2zaT7Ru	UgLId19h	mst	
F_PLAYBACK_CHAPTER_MST	PlaybackChapterMstList	Nm36woZx	gHLE4DM0	mst	
F_PLAYBACK_EVENT_MST	PlaybackEventMstList	IL78hX09	qWr8ZE2D	mst	
F_PLAYBACK_MAP_MST	PlaybackMapMstList	9Zdr5ap0	ZdM9VQb0	mst	
F_MONSTER_DICTIONARY_MST	MonsterDictionaryMstList	1r6jb9wB	wL4N16V3	mst	
F_TICKET_MST	TicketMstList	95KFiNTM	0sDdWku8	mst	
F_IMPORTANT_ITEM_MST	ImportantItemMstList	vdeSoq61	tnNGLk45	mst	
F_EMBLEM_ITEM_MST	EmblemMstList	SuM2XA05	UyE6HP3h	mst	
F_ITEM_MST	ItemMstList	1CUX0Qwr	L3f8nko1	mst	
F_MATERIA_MST	MateriaMstList	Af46DrVW	4MbdRZI6	mst	
F_EQUIP_ITEM_MST	EquipItemMstList	S67QEJsz	T1kP80NU	mst	
F_RECIPE_BOOK_MST	RecipeBookMstList	yu5rvEI3	3t8DMRQE	mst	
F_ITEM_EXT_BEAST_MST	ItemExtBeastMstList	hc8Ham29	wZ5IkW2f	mst	
F_MATERIA_LIMIT_MST	MateriaLimitMstList	BX0pRc8A	CVERk9KN	mst	
F_QUEST_SUB_MST	QuestSubMstList	myGc0U5v	oWcL37sK	mst	
F_QUEST_MST	QuestMstList	2Px75LpY	20mEeKo3	mst	
F_TICKER_DEFINE_MESSAGE_MST	TickerDefineMessageMstList	tUQ2Lkc1	6TQR0hSX	mst	
F_TICKER_MST	TickerMstList	5WJ9MQ3n	h92Fk0Qw	mst	
F_TICKER_LOG_CATEGORY_MST	TickerLogCategoryMstList	QmnZ48Fs	hJ91Gkz5	mst	
F_PURCHASE_AGE_LIMIT_MST	PurchaseLimitMstList	okUH6yV7	ZDvrb16x	mst	
F_SG_COMEBACK_QUEST_GROUP_MST	sgComebackQuestGroupMstList	J0zWp5jp	Ipr3c8SN	mst	
F_SG_COMEBACK_QUEST_MST	sgComebackQuestMstList	a2U6BqrH	TlIjUjGM	mst	
F_RB_BONUS_RULE_MST	RbBonusRuleMstList	09u3Nzk2	7Hm6jxe3	mst	
F_RB_AI_PATTERN_MST	RbAiPatternMstList	A6DJx0Qj	2phgAJt3	mst	
F_RB_TRADE_BOARD_MST	RbTradeBoardMstList	yiuv0Fb9	cbqYR3s7	mst	
F_RB_FORBIDDEN_INFO_MST	RbForbiddenInfoMstList	x6iQrD2e	8yWH5IGC	mst	
F_RB_SS_REWARD_MST	RbShortSeasonRewardMstList	cg5k2Mxn	KNWf37Bm	mst	
F_RB_DEFINE_MST	RbDefineMst	qzTG4ba0	2y1oT5gq	mst	
F_RB_SS_MST	RbShortSeasonMstList	gd74jWQn	gY7NiJL8	mst	
F_RB_TRADE_BOARD_PIECE_MST	RbTradeBoardPieceMstList	7Si5wPLj	fXZse89L	mst	
F_RANKUP_REWARD_MST	RankUpRewardMstList	BemPcH90	yn2Wwme5	mst	
F_NPC_MST	NpcMstList	94diC1bt	fvQ37cE0	mst	
F_MISSION_SKILL_POINT_MST	sgMissionSkillPointMstList	SeWZnBgT	dSCtehxp	mst	
F_UNIT_SERIES_HIDDEN_SKILL_MST	sgUnitSeriesHiddenSkillMstList	A5qV5ssR	htXe8Tdp	mst	
F_HIDDEN_SKILL_MST	sgHiddenSkillMstList	FWUA4NVN	bECcUBPa	mst	
F_STATIC_PARTY_DECK_MST	StaticPartyDeckMstList	DQ71KBSd	uJTgZ67c	mst	
F_STATIC_UNIT_EQUIP_MST	StaticUnitEquipMstList	XaH20cRS	2pD9ivX1	mst	
F_STATIC_UNIT_SKILL_SUBLIMATION_MST	StaticUnitSkillSublimationMstList	g2R1yYzn	0o6wkbyv	mst	
F_STATIC_UNIT_MST	StaticUnitMstList	wQ1LVGx6	nJA5e96H	mst	
F_STORY_MST	StoryMstList	IiVw7H6k	Cf2WZ8qA	mst	
F_STORY_SUB_MST	StorySubMstList	8vneWT7G	8ci0TamY	mst	
F_EXPEDITION_MST	sgExpdMstList	TdE7Oasq	fFx92pYD	mst	
F_EXPEDITION_DIFFICULTY_MST	sgExpdDifficultyMstList	4Xy9386a	0vkM7772	mst	
DailyQuestShareRequest	3sTwRcpq	PMyGMdUa	/actionSymbol/3sTwRcpq.php	request
SignOutRequest	o96pHAp3	UtE1qMv3	/actionSymbol/KwChAfkX.php	request
GrandMissionEntryRequest	MTf2j9aK	Uey5jW2G	/actionSymbol/8DermCsY.php	request
GetBackgroundDownloadInfoRequest	lEHBdOEf	Z1krd75o	/actionSymbol/action.php	request
RoutineHomeUpdateRequest	Daud71Hn	aw0syG7H	/actionSymbol/1YWTzU9h.php	request
RateAppRewardRequest	L0OsxMaT	m1pPBwC3	/actionSymbol/L0OsxMaT.php	request
RmDungeonStartRequest	R5mWbQ3M	A7V1zkyc	/actionSymbol/NC8Ie07P.php	request
RmEntryRequest	wx5sg9ye	p2tqP7Ng	/actionSymbol/fBn58ApV.php	request
RmStartRequest	7FyJS3Zn	iu67waph	/actionSymbol/8BJSL7g0.php	request
RmEndRequest	fyp10Rrc	FX5L3Sfv	/actionSymbol/I9p3n48A.php	request
RmRestartRequest	yh21MTaG	R1VjnNx0	/actionSymbol/NC8Ie07P.php	request
RmRetireRequest	e0R3iDm1	T4Undsr6	/actionSymbol/fBn58ApV.php	request
RmDungeonEndRequest	WaPC2T6i	dEnsQ75t	/actionSymbol/CH9fWn8K.php	request
IsNeedValidateRequest	er5xMIj6	djhiU6x8	/actionSymbol/gk3Wtr8A.php	request
PartyRegisterSlotSaveRequest	CE98eghN	0S9Uzr42	/actionSymbol/QjDQEMYc.php	request
PartyRegisterSlotUpdateRequest	RQ5BjDE0	CpZH8rY0	/actionSymbol/HgSTAzd2.php	request
PartyRegisterSlotLoadRequest	Q7jV6pno	7zcaE16T	/actionSymbol/2y4BVhyj.php	request
UnitNvClassUpRequest	Pf5C8xjJ	2tJE0Ijo	/actionSymbol/ukNsKIEo.php	request
UnitClassUpRequest	zf49XKg8	L2sTK0GM	/actionSymbol/8z4Z0DUY.php	request
UnitMixRequest	UiSC9y8R	4zCuj2hK	/actionSymbol/6aLHwhJ8.php	request
UnitSublimationRequest	a54L9CKY	B7iQjmz5	/actionSymbol/PiQObNT2.php	request
UnitEquipRequest	pB3st6Tg	45VZgFYv	/actionSymbol/nIk9z5pT.php	request
UnitFavoriteRequest	tBDi10Ay	w9mWkGX0	/actionSymbol/sqeRg12M.php	request
UnitExClassUpRequest	k6NI9Tm1	3xNEuAJ0	/actionSymbol/P19pJnPf.php	request
UnitSellRequest	9itzg1jc	DJ43wmds	/actionSymbol/0qmzs2gA.php	request
BeastMixRequest	C8X1KUpV	WfNSmy98	/actionSymbol/7vHqNPF0.php	request
BeastBoardPieceOpenRequest	0gk3Tfbz	7uxYTm3k	/actionSymbol/Y2Zvnad9.php	request
EquipGrowAbilitySelectResumeRequest	80R6BXUw	7YxgkK1V	/actionSymbol/Ke7YG3xW.php	request
EquipGrowAbilityFixRequest	k8ew94DN	58dS0DZN	/actionSymbol/CnPyXkUV.php	request
EquipGrowEntryRequest	U8F0Q25i	6fTy3HRM	/actionSymbol/UiSOVXT2.php	request
CraftStartRequest	Gr9zxXk5	K92H8wkY	/actionSymbol/w71MZ0Gg.php	request
CraftAddRequest	QkN1Sp64	qz0SG1Ay	/actionSymbol/iQ7R4CFB.php	request
CraftExeRequest	PKDhIN34	ZbHEB15J	/actionSymbol/UyHLjV60.php	request
CraftEndRequest	WIuvh09n	yD97t8kB	/actionSymbol/9G7Vc8Ny.php	request
CraftCancelRequest	79xDN1Mw	68zcUF3E	/actionSymbol/7WdDLIE4.php	request
ReinforcementSettingRequest	ZSq2y7EX	jUreV31B	/actionSymbol/I1g4ezbP.php	request
FriendDeleteRequest	a2d6omAy	d0VP5ia6	/actionSymbol/8R4fQbYh.php	request
FriendRequest	j0A5vQd8	6WAkj0IH	/actionSymbol/8drhF2mG.php	request
FriendSuggestRequest	iAs67PhJ	j2P3uqRC	/actionSymbol/6TCn0BFh.php	request
FriendFavoriteRequest	1oE3Fwn4	3EBXbj1d	/actionSymbol/8IYSJ5H1.php	request
FriendSearchRequest	3siZRSU4	VCL5oj6u	/actionSymbol/6Y1jM3Wp.php	request
FriendRefuseRequest	1nbWRV9w	RYdX9h2A	/actionSymbol/Vw0a4I3i.php	request
FriendDetailRequest	7kG0JAvE	aKvkU6Y4	/actionSymbol/QBiJEyUt.php	request
FriendListRequest	u7Id4bMg	1iV2oN9r	/actionSymbol/p3hwqW5U.php	request
GetReinforcementInfoRequest	AJhnI37s	87khNMou	/actionSymbol/hXMoLwgE.php	request
FriendAgreeRequest	kx13SLUY	9FjK0zM3	/actionSymbol/1DYp5Nqm.php	request
SignInRequest	FckReppg	g8iv4P8I	/actionSymbol/8DRAiBXE.php	request
SignInCheckRequest	ufKRrNc7	F83wNKFt	/actionSymbol/Qfpa24mZ.php	request
ParadeEntryRequest	Q46YTgWo	kF7Z5vCK	/actionSymbol/9i4b61rU.php	request
ParadeMissionEndRequest	4cACW7y3	Pv08w12y	/actionSymbol/hC8r9p81.php	request
DailyQuestClaimAllRewardRequest	DCmya9WD	KHx6JdrT	/actionSymbol/Br9PwJ6A.php	request
DailyQuestUpdateRequest	6QYd5Hym	9QtGVCWg	/actionSymbol/QWDn5epF.php	request
DailyQuestClaimRewardRequest	Zy8fYJ5e	jwYGF3sY	/actionSymbol/Br9PwJ6A.php	request
ArchiveUpdateRequest	cVTxW0K3	IFLW9H4M	/actionSymbol/2bCcKx0D.php	request
TowerRetireRequest	MTuX7ai2	5dqrT8Mi	/actionSymbol/sn4mo4gN.php	request
TowerEndRequest	fBN47X2b	kzFy73L9	/actionSymbol/TZXbYd9b.php	request
TowerEntryRequest	VfEh2wD0	L9d1gYAm	/actionSymbol/scI7HnwD.php	request
TowerRestartRequest	9Z3jCWfF	04HMuYV1	/actionSymbol/iqxK7alu.php	request
TowerStartRequest	dZA90j5s	M3dCmDW8	/actionSymbol/1ch0bfGj.php	request
PartyDeckEditRequest	TS5Dx9aZ	34qFNPf7	/actionSymbol/6xkK4eDG.php	request
SpChallengeRewardGetRequest	2G7ZVs4A	mG25PIUn	/actionSymbol/9inGHyqC.php	request
SpChallengeEntryRequest	MTf2j9aK	Uey5jW2G	/actionSymbol/8DermCsY.php	request
SublimationSkillRequest	s48Qzvhd	97Uvrdz3	/actionSymbol/xG3jBbw5.php	request
GetTitleInfoRequest	ocP3A1FI	Mw56RNZ2	/actionSymbol/BbIeq31M.php	request
InitializeRequest	75fYdNxq	rVG09Xnt	/actionSymbol/fSG1eXI9.php	request
GameSettingRequest	OTX6Fmvu	4foXVwWd	/actionSymbol/OTX6Fmvu.php	request
UltimaniaCodeIssueRequest	D1gXmV3L	4ouG6AjK	/actionSymbol/2D3yLWcW.php	request
NvSacrificeRequest	x94MnujU	KXcC8A1o	/actionSymbol/VliYfNhr.php	request
SacrificeRequest	7tWdn9zH	U80FYThX	/actionSymbol/QBiJEyUt.php	request
MailReceiptRequest	XK7efER9	P2YFr7N9	/actionSymbol/M2fHBe9d.php	request
MailListRequest	KQHpi0D7	7kgsrGQ1	/actionSymbol/u3E8hpad.php	request
DmgRankRetireRequest	W3Z4VF1X	5fkWyeE6	/actionSymbol/8wdmR9yG.php	request
DmgRankEndRequest	s98cw1WA	7pGj8hSW	/actionSymbol/zd5KJ3jn.php	request
DmgRankStartRequest	5P6ULvjg	1d5AP9p6	/actionSymbol/j37Vk5xe.php	request
TrophyRewardRequest	wukWY4t2	2o7kErn1	/actionSymbol/05vJDxg9.php	request
ShopExchangeUnitRequest	Vgi7j68T	x6rSuK0J	/actionSymbol/lnXYChmF.php	request
sgOfferwallInfoRequest	uO1w9ggv	QgNR0HvE	/actionSymbol/NmNB96p8.php	request
ShopExchangeItemListRequest	syKz34cE	h69WSu02	/actionSymbol/7KJjJiIh.php	request
ShopUseRequest	73SD2aMR	ZT0Ua4wL	/actionSymbol/w76ThDMm.php	request
ExchangeShopRequest	I7fmVX3R	qoRP87Fw	/actionSymbol/1bf0HF4w.php	request
ShopExchangeItemRequest	xD5b6PqQ	vaDW85R2	/actionSymbol/qhP5wSXV.php	request
AllianceUndercoverStrengthenRequest	qj63QmHE	75pZA8tv	/actionSymbol/UR1OtKJS.php	request
AllianceEntryRequest	HtR8XF4e	zS4tPgi7	/actionSymbol/EzfT0wX6.php	request
AllianceDeckEditRequest	P76LYXow	2E3UinsJ	/actionSymbol/7gAGFC4I.php	request
PlayerEmblemSettingRequest	19YwqU2T	76kGLIgN	/actionSymbol/cswGxj8F.php	request
PlayerEmblemEntryRequest	Z7J9H6TK	F7A2MJoE	/actionSymbol/huKNdci6.php	request
TownUpdateRequest	G1hQM8Dr	37nH21zE	/actionSymbol/0ZJzH2qY.php	request
TownOutRequest	sJcMPy04	Kc2PXd9D	/actionSymbol/0EF3JPjL.php	request
TownInRequest	8EYGrg76	JI8zU5rC	/actionSymbol/isHfQm09.php	request
LibraryVisionCardEntryRequest	Lx8F2iU0	63ucryZ7	/actionSymbol/lIUfgWYj.php	request
VisionCardClassUpRequest	zf49XKg8	L2sTK0GM	/actionSymbol/8z4Z0DUY.php	request
VisionCardMixRequest	5J14x3Qz	NVMk7Qr1	/actionSymbol/aeBv64rU.php	request
TransferRequest	oE5fmZN9	C6eHo3wU	/actionSymbol/v6Jba7pX.php	request
TransferCodeIssueRequest	crzI2bA5	T0y6ij47	/actionSymbol/hF0yCKc1.php	request
TransferCodeCheckRequest	CY89mIdz	c5aNjK9J	/actionSymbol/C9LoeYJ8.php	request
RoutineWorldUpdateRequest	6H1R9WID	XDIL4E7j	/actionSymbol/oR1psQ5B.php	request
BundleStatusRequest	uLXAMvCT	PrSPuc8c	/actionSymbol/tPc64qmn.php	request
BundlePurchaseRequest	w6Z9a6tD	NE3Pp4K8	/actionSymbol/tPc64qmn.php	request
GetUserInfoRequest	X07iYtp5	rcsq2eG7	/actionSymbol/u7sHDCg4.php	request
GetUserInfo2Request	2eK5Vkr8	7VNRi6Dk	/actionSymbol/7KZ4Wvuw.php	request
UpdateUserInfoRequest	ey8mupb4	6v5ykfpr	/actionSymbol/v3RD1CUB.php	request
CreateUserRequest	P6pTz4WA	73BUnZEr	/actionSymbol/0FK8NJRX.php	request
sgUserExtraChallengeInfoRequest	dzU2t3HA	Qu6RHqbb	/actionSymbol/zgry2aNV.php	request
OptionUpdateRequest	otgXV79T	B9mAa7rp	/actionSymbol/0Xh2ri5E.php	request
LoginBonusRequest	vw9RP3i4	Vi6vd9zG	/actionSymbol/iP9ogKy6.php	request
sgHomeMarqueeInfoRequest	PBSP9qn5	d3GDS9X8	/actionSymbol/PBSP9qn5.php	request
ClsmLotteryRequest	Un16HuNI	pU62SkhJ	/actionSymbol/4uj3NhUQ.php	request
ClsmStartRequest	4uCSA3ko	wdSs23yW	/actionSymbol/rncR9js8.php	request
ClsmEndRequest	3zgbapQ7	6aBHXGv4	/actionSymbol/7vHqNPF0.php	request
ClsmEntryRequest	5g0vWZFq	8bmHF3Cz	/actionSymbol/UmLwv56W.php	request
MissionRetireRequest	v51PM7wj	oUh1grm8	/actionSymbol/gbZ64SQ2.php	request
MissionContinueRequest	LuCN4tU5	34n2iv7z	/actionSymbol/ZzCXI6E7.php	request
MissionEndRequest	x5Unqg2d	1tg0Lsqj	/actionSymbol/0ydjM5sU.php	request
MissionWaveStartRequest	BSq28mwY	d2mqJ6pT	/actionSymbol/Mn15zmDZ.php	request
MissionContinueRetireRequest	V3CiWT0r	F1QRxT5m	/actionSymbol/cQU1D9Nx.php	request
MissionBreakRequest	17LFJD0b	Z2oPiE6p	/actionSymbol/P4oIeVf0.php	request
MissionStartRequest	29JRaDbd	i48eAVL6	/actionSymbol/63VqtzbQ.php	request
MissionWaveReStartRequest	e9RP8Cto	M3bYZoU5	/actionSymbol/8m7KNezI.php	request
MissionReStartRequest	GfI4LaU3	Vw6bP0rN	/actionSymbol/r5vfM1Y3.php	request
MissionUpdateRequest	j5JHKq6S	Nq9uKGP7	/actionSymbol/fRDUy3E2.php	request
sgMissionUnlockRequest	LJhqu0x6	ZcBV06K4	/actionSymbol/LJhqu0x6.php	request
MissionSwitchUpdateRequest	Tvq54dx6	bZezA63a	/actionSymbol/1Xz8kJLr.php	request
ExploreSettingRequest	f8Q0BJVX	i6M7o0cg	/actionSymbol/O4JRsPZU.php	request
ExploreStartRequest	FR4ISN7P	0Xpfxg7U	/actionSymbol/0PIk8qdm.php	request
ExploreRetireRequest	t8Yd2Pcy	3jTz0GIE	/actionSymbol/Gv0BZr4X.php	request
ExploreEntryRequest	Tnkz60cb	a4X1Q2Hm	/actionSymbol/mr6DnTQV.php	request
ExploreRewardGetRequest	XG3CwM1N	LaFmew85	/actionSymbol/RhznNjzo.php	request
NoticeReadUpdateRequest	pC3a2JWU	iLdaq6j2	/actionSymbol/j6kSWR3q.php	request
NoticeUpdateRequest	CQ4jTm2F	9t68YyjT	/actionSymbol/TqtzK84R.php	request
GiftUpdateRequest	9KN5rcwj	xLEtf78b	/actionSymbol/noN8I0UK.php	request
DungeonResourceLoadMstListRequest	jnw49dUq	3PVu6ReZ	/actionSymbol/Sl8UgmP4.php	request
ResourceAllDownloadRequest	i0d5n1Dp	fL07ojUc	/actionSymbol/Vtx9kFg0.php	request
sgAdsGachaMilestoneRequest	PkSzb2TM	Jp4Fz3qb	/actionSymbol/PkSzb2TM.php	request
GachaSelectExeRequest	xio14KrL	BuJqHc41	/actionSymbol/eB0VYGMt.php	request
GachaInfoRequest	UNP1GR5n	VA8QR57X	/actionSymbol/3nhWq25K.php	request
sgGachaSelectPrismExeRequest	bgast6dR	7mDIdVEI	/actionSymbol/pXAIaMKW.php	request
GachaExeRequest	9fVIioy1	oaEJ9y1Z	/actionSymbol/oC30VTFp.php	request
GachaPanelNextRequest	32YLaEmn	qbHn0E2L	/actionSymbol/7WGJtJwo.php	request
GachaSelectExchangeExeRequest	W58vo4Hp	98yAYp5c	/actionSymbol/ROLuCfi2.php	request
RoutineGachaUpdateRequest	t60dQP49	Q6ZGJj0h	/actionSymbol/qS0YW57G.php	request
GachaBoxNextRequest	xa9uR3pI	U2Lm8vcS	/actionSymbol/tULiKh5j.php	request
sgGachaRerollExeRequest	Dll4rncD	KJ13DlZz	/actionSymbol/QQVXslxB.php	request
GachaSelectEntryRequest	6nTcSp4R	uG1jRky6	/actionSymbol/6RI813OP.php	request
GachaEntryRequest	rj6dxU9w	39cFjtId	/actionSymbol/tUJxSQz7.php	request
FacebookAddFriendRequest	NAW9vJnm	532vAYUy	/actionSymbol/NAW9vJnm.php	request
FacebookLogoutRequest	xHTo4BZp	wwHxtAy6	/actionSymbol/xHTo4BZp.php	request
FacebookRewardClaimRequest	47R9pLGq	Rja82ZUK	/actionSymbol/47R9pLGq.php	request
FacebookRewardListRequest	8YZsGLED	85YBRzZg	/actionSymbol/8YZsGLED.php	request
PlaybackMissionStartRequest	1YnQM4iB	YC20v1Uj	/actionSymbol/zm2ip59f.php	request
PlaybackMissionWaveStartRequest	1BpXP3Fs	NdkX15vE	/actionSymbol/scyPYa81.php	request
MyCardInitializeRequest	BruItbuW	q54ajlRb	/actionSymbol/d6f9LSI7.php	request
EquipItemFavoriteRequest	cVRHU84e	dRBYF8Q0	/actionSymbol/fZ4jONfh.php	request
VariableStoreCheckRequest	i0woEP4B	Hi0FJU3c	/actionSymbol/Nhn93ukW.php	request
SearchGetItemInfoRequest	0D9mpGUR	vK2V8mZM	/actionSymbol/e4Gjkf0x.php	request
ItemCarryEditRequest	UM7hA0Zd	04opy1kf	/actionSymbol/8BE6tJbf.php	request
MedalExchangeRequest	LiM9Had2	dCja1E54	/actionSymbol/0X8Fpjhb.php	request
StrongBoxOpenRequest	PIv7u8jU	sgc30nRh	/actionSymbol/48ktHf13.php	request
ItemBuyRequest	sxK2HG6T	InN5PUR0	/actionSymbol/oQrAys71.php	request
ItemSellRequest	d9Si7TYm	E8H3UerF	/actionSymbol/hQRf8D6r.php	request
MateriaFavoriteRequest	k5v0hMd8	La3si8kz	/actionSymbol/lW4xfpvS.php	request
RoutineRaidMenuUpdateRequest	g0BjrU5D	z80swWd9	/actionSymbol/Sv85kcPQ.php	request
CampaignTieupRequest	mI0Q2YhW	72d5UTNC	/actionSymbol/2u30vqfY.php	request
UpdateSwitchInfoRequest	mRPo5n2j	4Z5UNaIW	/actionSymbol/SqoB3a1T.php	request
DailyDungeonSelectRequest	JyfxY2e0	ioC6zqG1	/actionSymbol/9LgmdR0v.php	request
DungeonLiberationRequest	nQMb2L4h	0xDA4Cr9	/actionSymbol/0vc6irBY.php	request
PurchaseStartRequest	qAUzP3R6	9Kf4gYvm	/actionSymbol/tPc64qmn.php	request
PurchaseListRequest	BT28S96F	X3Csghu0	/actionSymbol/YqZ6Qc1z.php	request
PurchaseSettlementRequest	JsFd4b7j	jmh7xID8	/actionSymbol/yt82BRwk.php	request
PurchaseGiveUpRequest	BFf1nwh6	xoZ62QWy	/actionSymbol/C2w0f3go.php	request
PurchaseHoldRequest	79EVRjeM	5Mwfq90Z	/actionSymbol/dCxtMZ27.php	request
PurchaseSettingRequest	QkwU4aD9	ePFcMX53	/actionSymbol/9hUtW0F8.php	request
PurchaseCurrentStateRequest	9mM3eXgi	X9k5vFdu	/actionSymbol/bAR4k7Qd.php	request
PurchaseCancelRequest	L7K0ezU2	Z1mojg9a	/actionSymbol/y71uBCER.php	request
PurchaseFailedRequest	jSe80Gx7	sW0vf3ZM	/actionSymbol/2TCis0R6.php	request
sgComebackQuestClaimRewardRequest	1IOdaWOW	4NO3PIyU	/actionSymbol/I7RzrQ3J.php	request
RbEntryRequest	f8kXGWy0	EA5amS29	/actionSymbol/30inL7I6.php	request
RbReStartRequest	6ZNY3zAm	PRzAL3V2	/actionSymbol/DQ49vsGL.php	request
RbStartRequest	eHY7X8Nn	P1w8BKLI	/actionSymbol/dR20sWwE.php	request
RbMatchingRequest	DgG4Cy0F	4GSMn0qb	/actionSymbol/mn5cHaJ0.php	request
RbBoardPieceOpenRequest	hqzU9Qc5	g68FW4k1	/actionSymbol/iXKfI4v1.php	request
RbEndRequest	os4k7C0b	MVA3Te2i	/actionSymbol/e8AHNiT7.php	request
RbRankingRequest	kcW85SfU	SR6PoLM3	/actionSymbol/3fd8y7W1.php	request
ChallengeClearRequest	D9xphQ8X	UD5QCa2s	/actionSymbol/dEvLKchl.php	request
RoutineEventUpdateRequest	4kA1Ne05	V0TGwId5	/actionSymbol/WCK5tvr0.php	request
sgHiddenSkillUpgrade	qxUUaS8s	rXDvnsv8	/actionSymbol/qxUUaS8s.php	request
sgStoreLogReportRequest	6611dc2f	af7a56a0	/actionSymbol/6611dc2f.php	request
slotgame::SgSlotInfoRequest	uVswysHK	gsRV5GaY	/actionSymbol/action.php	request
slotgame::SgSlotLotRequest	DW2yAFjB	vyLhq43j	/actionSymbol/action.php	request
sgExpdMileStoneClaimRequest	r4A791RF	t04N07LQ	/actionSymbol/r4A791RF.php	request
sgExpdAccelerateRequest	Ik142Ff6	d3D4l8b4	/actionSymbol/Ik142Ff6.php	request
sgExpdQuestRefreshRequest	vTgYyHM6lC	vceNlSf3gn	/actionSymbol/vTgYyHM6lC.php	request
sgExpdRecallRequest	0Fb87D0F	9J02K0lX	/actionSymbol/0Fb87D0F.php	request
sgExpdQuestInfoRequest	hW0804Q9	4Bn7d973	/actionSymbol/hW0804Q9.php	request
sgExpdQuestStartRequest	I8uq68c3	60Os29Mg	/actionSymbol/I8uq68c3.php	request
sgExpdEndRequest	2pe3Xa8bpG	cjHumZ2Jkt	/actionSymbol/2pe3Xa8bpG.php	request
F_TEXT_ABILITY_EXPLAIN_SHORT	-	8GJBzdQV	D9UVupRQ	mst
F_TEXT_ABILITY_NAME	-	eSB5Ry3E	At4ghcWo	mst
F_TEXT_ABILITY_PARAM_MSG	-	3oM745kA	OTxnCDr7	mst
F_TEXT_ARCHIVE_NAME	-	QR3zeiJr	CrZmyr8k	mst
F_TEXT_AREA_NAME	-	4qiOcmtJ	FhWJxFlW	mst
F_TEXT_AWARD_NAME	-	mGPBqPdv	G9IOqfaz	mst
F_TEXT_AWARD_EXPLAIN	-	TlrlvFx5	XfXmnCNF	mst
F_TEXT_AWARD_TYPE	-	soohaWOo	b23aWS6d	mst
F_TEXT_BEAST_NAME	-	148O76GI	pzLuYoDO	mst
F_TEXT_BEAST_SKILL_DES	-	Yjq14lAB	g5SJviEC	mst
F_TEXT_BEAST_SKILL_NAME	-	k51drhZ6	JHJRAcEx	mst
F_TEXT_BUNDLE	-	4OBhnBKu	3VkESseA	mst
F_TEXT_CAPTURE_INFO	-	Ir2B1EBL	Ak7An4Ys	mst
F_TEXT_CHALLENGE_NAME	-	4bQbA2FH	SFaS7rXZ	mst
F_TEXT_CHARACTER_NAME	-	JIqrNzje	iizk8FjI	mst
F_TEXT_COLOSSEUM_GRADE	-	Y2rngIF3	7Vzk5Oq9	mst
F_TEXT_DAILY_QUEST_NAME	-	FBBD8vv6	BhDl8FWG	mst
F_TEXT_DAILY_QUEST_DETAIL	-	gCfFcx75	xpLyjA9A	mst
F_TEXT_DAILY_QUEST_DES	-	W4525rcx	1ihwWs7f	mst
F_TEXT_DEFINE	-	0Uu7pmsG	Yj7TOhPL	mst
F_TEXT_DESCRIPTION_FORMAT_1	-	gY4LGMMD	caEX7W6f	mst
F_TEXT_DESCRIPTION_FORMAT_2	-	JqBqNkGX	2creMDuf	mst
F_TEXT_DESCRIPTION_FORMAT_3	-	SkqNMnxV	ydvfBPV4	mst
F_TEXT_DESCRIPTION_FORMAT_4	-	RPUNrRkn	4AMpavqU	mst
F_TEXT_DESCRIPTION_FORMAT_5	-	p95CetGL	SdMBwAYu	mst
F_TEXT_DIAMOND_NAME	-	mcQeT7mq	tN7Gjdpv	mst
F_TEXT_DUNGEON_NAME	-	AyVqkz2B	0cqVvd61	mst
F_TEXT_EMBLEM	-	qnfEz2Ah	qolF92B6	mst
F_TEXT_EXCHANGE_SHOP_ITEM	-	r1ZLxyyg	fT1SbKUm	mst
F_TEXT_EXPN_STORY	-	N1FxjkHa	AC7L89p8	mst
F_TEXT_GACHA	-	ZJz5QwAy	Ab2Kb6yJ	mst
F_TEXT_GAME_TITLE_NAME	-	3CzC5zn7	SA6Bv7i1	mst
F_TEXT_IMPORTANT_ITEM_NAME	-	TIKwbf3D	lx4HCdrQ	mst
F_TEXT_IMPORTANT_ITEM_SHOP	-	JO7UqqJ6	EzACmD0Y	mst
F_TEXT_ITEM_NAME	-	VhkhtvDn	xDkegMbe	mst
F_TEXT_ITEM_EQUIP_NAME	-	E0NdslwL	SGMI0nIq	mst
F_TEXT_ITEM_EQUIP_LONG	-	CD4giPVu	jqe9yQm0	mst
F_TEXT_ITEM_EQUIP_SHORT	-	Nao9HYWk	I0l1uc2s	mst
F_TEXT_ITEM_EXPLAIN_LONG	-	9NCIDltW	s3fVSywt	mst
F_TEXT_ITEM_EXPLAIN_SHORT	-	IAPS1jOu	TZh30bbo	mst
F_TEXT_JOB_NAME	-	yUkwbFyc	R6mBb3T3	mst
F_TEXT_LAND_NAME	-	sKLZVYWQ	yYnTSUtm	mst
F_TEXT_LIMIT_BURST_DES	-	EUsG7rlQ	ZcXYp8BI	mst
F_TEXT_LIMIT_BURST_NAME	-	XBS8hLZD	zswbSb5U	mst
F_TEXT_MAGIC_EXPLAIN_SHORT	-	Hs9KVVnj	raokY9Xl	mst
F_TEXT_MAGIC_NAME	-	1ZqISaBp	9TAwFj0e	mst
F_TEXT_MATERIA_NAME	-	2Eg5s20D	E5jbLGyb	mst
F_TEXT_MATERIA_EXPLAIN_LONG	-	QEbXmDTD	3ZaDmbq1	mst
F_TEXT_MATERIA_EXPLAIN_SHORT	-	8g18k8jD	DvdwoXYQ	mst
F_TEXT_MISSION	-	pa6vblsG	GdAhtrNB	mst
F_TEXT_MONSTER_NAME	-	0xkPiwVI	UOz3hI2k	mst
F_TEXT_MONSTER_DIC_EXPLAIN_SHORT	-	zf7USTwU	OJir9FR5	mst
F_TEXT_MONSTER_DICTIONARY_NAME	-	F1VQsGpG	uEOGF11w	mst
F_TEXT_MONSTER_PART_DIC_NAME	-	P4c5fq2t	XhCacZZv	mst
F_TEXT_MONSTER_SKILL_NAME	-	F1z92dkt	B41kLp2C	mst
F_TEXT_NPC_NAME	-	A55coosK	xQj2cuc8	mst
F_TEXT_PICTURE_STORY_NAME	-	j525zYCH	Pe21ACFe	mst
F_TEXT_PLAYBACK	-	cvz0lj48	svmqgt6n	mst
F_TEXT_QUEST	-	NMwfx1lf	KVBowHC2	mst
F_TEXT_QUEST_SUB_NAME	-	uuU68I2u	cnW2w71S	mst
F_TEXT_QUEST_SUB_DETAIL	-	vb5Nom5d	sCFyRxng	mst
F_TEXT_QUEST_SUB_STORY	-	fULaqIeB	CueuZNTN	mst
F_TEXT_QUEST_SUB_TARGET_PARAM	-	Cw3B65ql	W4rz5Sas	mst
F_TEXT_RB_ABILITY_GROUP_NAME	-	69zUY4Zb	qEAmEfJU	mst
F_TEXT_RB_BONUS_RULE_NAME	-	6YYynT87	YC0psthA	mst
F_TEXT_RB_BONUS_RULE_DESCRIPTION	-	10Ew2Rth	AGItS6CC	mst
F_TEXT_RB_FORBIDDEN_INFO_NAME	-	DaNvFWp7	wxea5c0t	mst
F_TEXT_RECIPE_BOOK_NAME	-	tFnHkR8G	lEWsm9iI	mst
F_TEXT_RECIPE_EXPLAIN_LONG	-	DS21iNC5	9uTG75o7	mst
F_TEXT_RULE_DESCRIPTION	-	a6kiwI22	EahlebAb	mst
F_TEXT_SHOP	-	NYz5Oxm4	yBd5wOHp	mst
F_TEXT_SPCHALLENGE	-	hge62ssc	lklesd2w	mst
F_TEXT_STORY_NAME	-	fKGHnuPm	nJswCIXz	mst
F_TEXT_STORY_SUB	-	hiiVWxXJ	ucOQs1YO	mst
F_TEXT_SUBLIMATION_EXPLAIN	-	JF89DHPE	SkUNQP6F	mst
F_TEXT_TELEPO_NAME	-	ca5XNnWD	WfvQbAKG	mst
F_TEXT_TEXT_EN	-	0ThfQQWd	s9E34w78	mst
F_TEXT_TICKER	-	RUPcXt7J	tHaAyyqI	mst
F_TEXT_TOWN_NAME	-	N12vEZpN	11yq2cUt	mst
F_TEXT_TOWN_EXPLAIN	-	KLoYS0Tj	0BZOtiRB	mst
F_TEXT_TOWN_STORE	-	h23JuUGF	Jovaw62m	mst
F_TEXT_TOWN_STORE_OWNER_NAME	-	KFL34pbm	Q9HMWNZG	mst
F_TEXT_TOWN_STORE_COMMENT	-	SZXTrTgq	oVpBUtX2	mst
F_TEXT_TRIBE	-	Z6OfsPv9	FAfGhIMo	mst
F_TEXT_TROPHY_EXPLAIN	-	pNeHXqpJ	OJV9Jpm8	mst
F_TEXT_TROPHY_METER_SERIF	-	7BfBBf9E	S410iF8y	mst
F_TEXT_UNIT_AFFINITY	-	Zfw0jmyn	xCppLKwD	mst
F_TEXT_UNIT_DESCRIPTION	-	w6U2ntyZ	VNh3r92R	mst
F_TEXT_UNIT_EVO	-	7tfppWVS	OYQn68Hu	mst
F_TEXT_UNIT_EXPLAIN_SHOP	-	3uEWl5CV	QrOn67A8	mst
F_TEXT_UNIT_FUSION	-	TpbDECdR	v47OlIK4	mst
F_TEXT_UNIT_SUMMON	-	hWE8dJMC	GInxSlTN	mst
F_TEXT_UNITS_NAME	-	sZE3Lhgj	3IfWAnJ3	mst
F_TEXT_WORLD_NAME	-	GPNXLUJP	uDFuhlR6	mst
F_TEXT_EXTRA_CHALLENGE_NAME	-	9DPL7n3Y	FfL8gPmF	mst
F_TEXT_HIDDEN_SKILL_UPGRADE	-	C6YYHC6F	F2AMzWZU	mst
F_TEXT_SG_COMEBACK_QUEST_NAME	-	gOP1GEvQ	q8glTCiE	mst
F_TEXT_CHAIN_FAMILY_NAME	-	MaKssSZb	cfecerpW	mst
F_TEXT_QUEST_DISP_NAME	-	1F5ol7IV	dr02cMU8	mst
F_TEXT_QUEST_SUB_DISP_NAME	-	27RESBcF	H3msM3YC	mst
F_TEXT_GACHA_BOX_DETAIL_NAME	-	eQvFNZGe	HeLChsPw	mst
F_TEXT_NV_UNIT_CLASS_UP	-	eE9ecgGq	yhUaM7Nj	mst
F_TEXT_MONSTER_PARTS_BREAK_DES	-	f3uHXanH	KMKAH3nQ	mst
F_TEXT_VISION_CARD_BGM	-	2txH4bFV	bsx2wWDf	mst
F_TEXT_VISION_CARD_DICTIONARY	-	tYBPBWfL	EqFAGvvb	mst
F_TEXT_VISION_CARD_MIX	-	gd7PfAj3	zWZ9XN8W	mst
F_TEXT_VISION_CARD_NAME	-	jDYeUTZA	PJu2EYP8	mst
F_TEXT_VISION_CARD_EXPLAIN_SHORT	-	Shyx968X	U5bJng9G	mst
F_TEXT_VISION_CARD_SHOP	-	Q5amhU3b	5uQjxA85	mst
F_TEXT_VISION_CARD_SUMMON	-	hNXaXBXh	P7mJb2KR	mst
F_TEXT_NV_SHOP_PRODUCT_NAME	-	s5K4EzVy	spxNTzFX	mst
F_TEXT_NV_SHOP_PRODUCT_SHORT_DES	-	39tuQBKk	bLUjnqGk	mst
F_TEXT_MAP_OBJECT	-	15KaBQci	U56G5oiU	mst
F_TEXT_SCENARIO_BATTLE	-	TGxop4tW	ZCIcuxf3	mst
F_TEXT_SEASON_EVENT_ABILITY_NAME	-	9ligF2RJ	v11TneD5	mst
F_TEXT_SEASON_EVENT_NAME	-	FxaCYmHE	NwKi0CpP	mst
F_TEXT_ANALYTICS_LOCALIZE	-	3sI39BAT	6zAQarn1	mst
F_TEXT_ANALYTICS_ITEMS	-	8e6PGb3p	7nyO4pC9	mst
EOF
        ,
    ];

    GameFile::init();

    foreach ($blubb as $region => $data) {
        $region = strtoupper($region);
        $data   = explode("\n", $data);
        $data   = array_map('trim', $data);
        foreach ($data as $line) {
            if (empty($line))
                continue;

            $row  = preg_split("~\s+~", $line);
            $type = array_pop($row);


            switch ($type) {
                case 'mst':
                case 'txt':
                    [$name, $class, $hash, $pass] = $row;

                    if ($name == '-' && $hash == '-')
                        continue 2;

                    $entry = GameFile::getEntry($name)
                             ?? GameFile::getEntry($hash);

                    $type = strpos($name, 'F_TEXT') !== false ? 'txt' : 'mst';
                    if ($entry == null) {
                        $entry = new GameFile($name, $hash, $pass, $class, [$type]);
                        GameFile::addEntry($entry);
                    }
                    else {
                        if ($class != '-')
                            $entry->setClass($class);
                        if ($hash != '-')
                            $entry->setFile($hash);
                        if ($pass != '-')
                            $entry->setKey($pass);
                    }

                    if (empty($entry->getNotes()) || in_array($entry->getNotes(), [['-'], ['']]))
                        $entry->setNotes([$type]);

                    // if (! in_array($region, $entry->getNotes())) {
                    //     $notes   = $entry->getNotes();
                    //     $notes[] = $region;
                    //     sort($notes);
                    //
                    //     $entry->setNote($notes);
                    // }

                    break;

                case 'request':
                    [$class, $hash, $pass, $url] = $row;


                    $class = str_replace('::', '', $class);
                    if (class_exists("\\Solaris\\FFBE\\Request\\{$class}"))
                        continue 2;

                    $path = ROOT_DIR . "/../ffbe-discord/src/Solaris/FFBE/Request/{$class}.php";
                    $url = preg_replace('~/actionSymbol/(.*?).php~', '$1', $url);

                    file_put_contents($path, <<<EOF
<?php
    namespace Solaris\FFBE\Request;


    class {$class} extends BaseRequest {
        public static function getUrl(): string {
            return '{$url}';
        }
        
        public static function getRequestID(): string
        {
            return '{$hash}';
        }

        public static function getKey(): string {
            return '{$pass}';
        }
    }
EOF
                    );
                    break;

                default:
                    throw new LogicException($type);
            }
        }
    }

    // c# export
    GameFile::save();
    #die();
    foreach (GameFile::getEntries() as $entry)
        if (in_array('', [$entry->getKey(), $entry->getName(), $entry->getFile()], true) ||
            in_array('-', [$entry->getKey(), $entry->getName(), $entry->getFile()], true))
            continue;
        else
            echo "{\"{$entry->getName()}\", new GameFileInfo(\"{$entry->getFile()}\", \"{$entry->getKey()}\")},\n";