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
SacrificeRequest  7tWdn9zH  U80FYThX  /actionSymbol/QBiJEyUt.php  request
ExploreStartRequest  FR4ISN7P  0Xpfxg7U  /actionSymbol/0PIk8qdm.php  request
sgComebackQuestClaimRewardRequest  1IOdaWOW  4NO3PIyU  /actionSymbol/I7RzrQ3J.php  request
UltimaniaCodeIssueRequest  D1gXmV3L  4ouG6AjK  /actionSymbol/2D3yLWcW.php  request
TowerRetireRequest  MTuX7ai2  5dqrT8Mi  /actionSymbol/sn4mo4gN.php  request
GachaInfoRequest  UNP1GR5n  VA8QR57X  /actionSymbol/3nhWq25K.php  request
AllianceDeckEditRequest  P76LYXow  2E3UinsJ  /actionSymbol/7gAGFC4I.php  request
MedalExchangeRequest  LiM9Had2  dCja1E54  /actionSymbol/0X8Fpjhb.php  request
GetUserInfoRequest  X07iYtp5  rcsq2eG7  /actionSymbol/u7sHDCg4.php  request
VariableStoreCheckRequest  i0woEP4B  Hi0FJU3c  /actionSymbol/Nhn93ukW.php  request
GetTitleInfoRequest  ocP3A1FI  Mw56RNZ2  /actionSymbol/BbIeq31M.php  request
PartyRegisterSlotLoadRequest  Q7jV6pno  7zcaE16T  /actionSymbol/2y4BVhyj.php  request
UpdateUserInfoRequest  ey8mupb4  6v5ykfpr  /actionSymbol/v3RD1CUB.php  request
RbStartRequest  eHY7X8Nn  P1w8BKLI  /actionSymbol/dR20sWwE.php  request
PurchaseCancelRequest  L7K0ezU2  Z1mojg9a  /actionSymbol/y71uBCER.php  request
InitializeRequest  75fYdNxq  rVG09Xnt  /actionSymbol/fSG1eXI9.php  request
UpdateSwitchInfoRequest  mRPo5n2j  4Z5UNaIW  /actionSymbol/SqoB3a1T.php  request
ExploreEntryRequest  Tnkz60cb  a4X1Q2Hm  /actionSymbol/mr6DnTQV.php  request
DailyQuestUpdateRequest  6QYd5Hym  9QtGVCWg  /actionSymbol/QWDn5epF.php  request
UnitFavoriteRequest  tBDi10Ay  w9mWkGX0  /actionSymbol/sqeRg12M.php  request
PartyRegisterSlotUpdateRequest  RQ5BjDE0  CpZH8rY0  /actionSymbol/HgSTAzd2.php  request
PartyRegisterSlotSaveRequest  CE98eghN  0S9Uzr42  /actionSymbol/QjDQEMYc.php  request
sgExpdRecallRequest  0Fb87D0F  9J02K0lX  /actionSymbol/0Fb87D0F.php  request
TownOutRequest  sJcMPy04  Kc2PXd9D  /actionSymbol/0EF3JPjL.php  request
GetReinforcementInfoRequest  AJhnI37s  87khNMou  /actionSymbol/hXMoLwgE.php  request
sgHiddenSkillUpgrade  qxUUaS8s  rXDvnsv8  /actionSymbol/qxUUaS8s.php  request
RmStartRequest  7FyJS3Zn  iu67waph  /actionSymbol/8BJSL7g0.php  request
TransferRequest  oE5fmZN9  C6eHo3wU  /actionSymbol/v6Jba7pX.php  request
FriendAgreeRequest  kx13SLUY  9FjK0zM3  /actionSymbol/1DYp5Nqm.php  request
ItemBuyRequest  sxK2HG6T  InN5PUR0  /actionSymbol/oQrAys71.php  request
BeastBoardPieceOpenRequest  0gk3Tfbz  7uxYTm3k  /actionSymbol/Y2Zvnad9.php  request
MissionWaveReStartRequest  e9RP8Cto  M3bYZoU5  /actionSymbol/8m7KNezI.php  request
TowerEndRequest  fBN47X2b  kzFy73L9  /actionSymbol/TZXbYd9b.php  request
ArchiveUpdateRequest  cVTxW0K3  IFLW9H4M  /actionSymbol/2bCcKx0D.php  request
sgExpdQuestStartRequest  I8uq68c3  60Os29Mg  /actionSymbol/I8uq68c3.php  request
FacebookLogoutRequest  xHTo4BZp  wwHxtAy6  /actionSymbol/xHTo4BZp.php  request
ClsmEndRequest  3zgbapQ7  6aBHXGv4  /actionSymbol/7vHqNPF0.php  request
MissionSwitchUpdateRequest  Tvq54dx6  bZezA63a  /actionSymbol/1Xz8kJLr.php  request
GrandMissionEntryRequest  MTf2j9aK  Uey5jW2G  /actionSymbol/8DermCsY.php  request
BundlePurchaseRequest  w6Z9a6tD  NE3Pp4K8  /actionSymbol/tPc64qmn.php  request
CraftCancelRequest  79xDN1Mw  68zcUF3E  /actionSymbol/7WdDLIE4.php  request
RbRankingRequest  kcW85SfU  SR6PoLM3  /actionSymbol/3fd8y7W1.php  request
sgExpdQuestInfoRequest  hW0804Q9  4Bn7d973  /actionSymbol/hW0804Q9.php  request
PurchaseGiveUpRequest  BFf1nwh6  xoZ62QWy  /actionSymbol/C2w0f3go.php  request
FacebookAddFriendRequest  NAW9vJnm  532vAYUy  /actionSymbol/NAW9vJnm.php  request
FriendRefuseRequest  1nbWRV9w  RYdX9h2A  /actionSymbol/Vw0a4I3i.php  request
BeastMixRequest  C8X1KUpV  WfNSmy98  /actionSymbol/7vHqNPF0.php  request
PurchaseStartRequest  qAUzP3R6  9Kf4gYvm  /actionSymbol/tPc64qmn.php  request
MissionContinueRequest  LuCN4tU5  34n2iv7z  /actionSymbol/ZzCXI6E7.php  request
sgHomeMarqueeInfoRequest  PBSP9qn5  d3GDS9X8  /actionSymbol/PBSP9qn5.php  request
MissionBreakRequest  17LFJD0b  Z2oPiE6p  /actionSymbol/P4oIeVf0.php  request
TransferCodeCheckRequest  CY89mIdz  c5aNjK9J  /actionSymbol/C9LoeYJ8.php  request
PurchaseFailedRequest  jSe80Gx7  sW0vf3ZM  /actionSymbol/2TCis0R6.php  request
MyCardInitializeRequest  BruItbuW  q54ajlRb  /actionSymbol/d6f9LSI7.php  request
TrophyRewardRequest  wukWY4t2  2o7kErn1  /actionSymbol/05vJDxg9.php  request
PurchaseSettingRequest  QkwU4aD9  ePFcMX53  /actionSymbol/9hUtW0F8.php  request
DungeonLiberationRequest  nQMb2L4h  0xDA4Cr9  /actionSymbol/0vc6irBY.php  request
MailReceiptRequest  XK7efER9  P2YFr7N9  /actionSymbol/M2fHBe9d.php  request
RmRetireRequest  e0R3iDm1  T4Undsr6  /actionSymbol/fBn58ApV.php  request
GachaBoxNextRequest  xa9uR3pI  U2Lm8vcS  /actionSymbol/tULiKh5j.php  request
TowerRestartRequest  9Z3jCWfF  04HMuYV1  /actionSymbol/iqxK7alu.php  request
MissionStartRequest  29JRaDbd  i48eAVL6  /actionSymbol/63VqtzbQ.php  request
FacebookRewardClaimRequest  47R9pLGq  Rja82ZUK  /actionSymbol/47R9pLGq.php  request
ExchangeShopRequest  I7fmVX3R  qoRP87Fw  /actionSymbol/1bf0HF4w.php  request
ExploreSettingRequest  f8Q0BJVX  i6M7o0cg  /actionSymbol/O4JRsPZU.php  request
PlayerEmblemEntryRequest  Z7J9H6TK  F7A2MJoE  /actionSymbol/huKNdci6.php  request
OptionUpdateRequest  otgXV79T  B9mAa7rp  /actionSymbol/0Xh2ri5E.php  request
FriendDetailRequest  7kG0JAvE  aKvkU6Y4  /actionSymbol/QBiJEyUt.php  request
ShopExchangeItemRequest  xD5b6PqQ  vaDW85R2  /actionSymbol/qhP5wSXV.php  request
RmEndRequest  fyp10Rrc  FX5L3Sfv  /actionSymbol/I9p3n48A.php  request
MissionWaveStartRequest  BSq28mwY  d2mqJ6pT  /actionSymbol/Mn15zmDZ.php  request
PlaybackMissionWaveStartRequest  1BpXP3Fs  NdkX15vE  /actionSymbol/scyPYa81.php  request
EquipGrowAbilitySelectResumeRequest  80R6BXUw  7YxgkK1V  /actionSymbol/Ke7YG3xW.php  request
GachaExeRequest  9fVIioy1  oaEJ9y1Z  /actionSymbol/oC30VTFp.php  request
DailyQuestClaimAllRewardRequest  DCmya9WD  KHx6JdrT  /actionSymbol/Br9PwJ6A.php  request
RbEndRequest  os4k7C0b  MVA3Te2i  /actionSymbol/e8AHNiT7.php  request
UnitMixRequest  UiSC9y8R  4zCuj2hK  /actionSymbol/6aLHwhJ8.php  request
ClsmStartRequest  4uCSA3ko  wdSs23yW  /actionSymbol/rncR9js8.php  request
ExploreRewardGetRequest  XG3CwM1N  LaFmew85  /actionSymbol/RhznNjzo.php  request
PlayerEmblemSettingRequest  19YwqU2T  76kGLIgN  /actionSymbol/cswGxj8F.php  request
TownInRequest  8EYGrg76  JI8zU5rC  /actionSymbol/isHfQm09.php  request
FriendDeleteRequest  a2d6omAy  d0VP5ia6  /actionSymbol/8R4fQbYh.php  request
sgExpdQuestRefreshRequest  vTgYyHM6lC  vceNlSf3gn  /actionSymbol/vTgYyHM6lC.php  request
RoutineWorldUpdateRequest  6H1R9WID  XDIL4E7j  /actionSymbol/oR1psQ5B.php  request
TownUpdateRequest  G1hQM8Dr  37nH21zE  /actionSymbol/0ZJzH2qY.php  request
FriendFavoriteRequest  1oE3Fwn4  3EBXbj1d  /actionSymbol/8IYSJ5H1.php  request
PartyDeckEditRequest  TS5Dx9aZ  34qFNPf7  /actionSymbol/6xkK4eDG.php  request
GiftUpdateRequest  9KN5rcwj  xLEtf78b  /actionSymbol/noN8I0UK.php  request
GachaSelectExeRequest  xio14KrL  BuJqHc41  /actionSymbol/eB0VYGMt.php  request
PurchaseSettlementRequest  JsFd4b7j  jmh7xID8  /actionSymbol/yt82BRwk.php  request
SpChallengeRewardGetRequest  2G7ZVs4A  mG25PIUn  /actionSymbol/9inGHyqC.php  request
PlaybackMissionStartRequest  1YnQM4iB  YC20v1Uj  /actionSymbol/zm2ip59f.php  request
sgExpdAccelerateRequest  Ik142Ff6  d3D4l8b4  /actionSymbol/Ik142Ff6.php  request
MissionContinueRetireRequest  V3CiWT0r  F1QRxT5m  /actionSymbol/cQU1D9Nx.php  request
RoutineRaidMenuUpdateRequest  g0BjrU5D  z80swWd9  /actionSymbol/Sv85kcPQ.php  request
GameSettingRequest  OTX6Fmvu  4foXVwWd  /actionSymbol/OTX6Fmvu.php  request
ItemSellRequest  d9Si7TYm  E8H3UerF  /actionSymbol/hQRf8D6r.php  request
EquipGrowAbilityFixRequest  k8ew94DN  58dS0DZN  /actionSymbol/CnPyXkUV.php  request
DailyQuestClaimRewardRequest  Zy8fYJ5e  jwYGF3sY  /actionSymbol/Br9PwJ6A.php  request
ExploreRetireRequest  t8Yd2Pcy  3jTz0GIE  /actionSymbol/Gv0BZr4X.php  request
MissionUpdateRequest  j5JHKq6S  Nq9uKGP7  /actionSymbol/fRDUy3E2.php  request
FacebookRewardListRequest  8YZsGLED  85YBRzZg  /actionSymbol/8YZsGLED.php  request
EquipGrowEntryRequest  U8F0Q25i  6fTy3HRM  /actionSymbol/UiSOVXT2.php  request
sgOfferwallInfoRequest  uO1w9ggv  QgNR0HvE  /actionSymbol/NmNB96p8.php  request
sgGachaSelectPrismExeRequest  bgast6dR  7mDIdVEI  /actionSymbol/pXAIaMKW.php  request
AllianceEntryRequest  HtR8XF4e  zS4tPgi7  /actionSymbol/EzfT0wX6.php  request
ShopExchangeUnitRequest  Vgi7j68T  x6rSuK0J  /actionSymbol/lnXYChmF.php  request
ClsmEntryRequest  5g0vWZFq  8bmHF3Cz  /actionSymbol/UmLwv56W.php  request
RmDungeonEndRequest  WaPC2T6i  dEnsQ75t  /actionSymbol/CH9fWn8K.php  request
ShopExchangeItemListRequest  syKz34cE  h69WSu02  /actionSymbol/7KJjJiIh.php  request
UnitSellRequest  9itzg1jc  DJ43wmds  /actionSymbol/0qmzs2gA.php  request
RbReStartRequest  6ZNY3zAm  PRzAL3V2  /actionSymbol/DQ49vsGL.php  request
FriendRequest  j0A5vQd8  6WAkj0IH  /actionSymbol/8drhF2mG.php  request
RoutineHomeUpdateRequest  Daud71Hn  aw0syG7H  /actionSymbol/1YWTzU9h.php  request
GetUserInfo2Request  2eK5Vkr8  7VNRi6Dk  /actionSymbol/7KZ4Wvuw.php  request
MailListRequest  KQHpi0D7  7kgsrGQ1  /actionSymbol/u3E8hpad.php  request
RbMatchingRequest  DgG4Cy0F  4GSMn0qb  /actionSymbol/mn5cHaJ0.php  request
SearchGetItemInfoRequest  0D9mpGUR  vK2V8mZM  /actionSymbol/e4Gjkf0x.php  request
NoticeUpdateRequest  CQ4jTm2F  9t68YyjT  /actionSymbol/TqtzK84R.php  request
CraftStartRequest  Gr9zxXk5  K92H8wkY  /actionSymbol/w71MZ0Gg.php  request
TowerStartRequest  dZA90j5s  M3dCmDW8  /actionSymbol/1ch0bfGj.php  request
TowerEntryRequest  VfEh2wD0  L9d1gYAm  /actionSymbol/scI7HnwD.php  request
MissionReStartRequest  GfI4LaU3  Vw6bP0rN  /actionSymbol/r5vfM1Y3.php  request
CreateUserRequest  P6pTz4WA  73BUnZEr  /actionSymbol/0FK8NJRX.php  request
AllianceUndercoverStrengthenRequest  qj63QmHE  75pZA8tv  /actionSymbol/UR1OtKJS.php  request
ClsmLotteryRequest  Un16HuNI  pU62SkhJ  /actionSymbol/4uj3NhUQ.php  request
IsNeedValidateRequest  er5xMIj6  djhiU6x8  /actionSymbol/gk3Wtr8A.php  request
UnitEquipRequest  pB3st6Tg  45VZgFYv  /actionSymbol/nIk9z5pT.php  request
PurchaseCurrentStateRequest  9mM3eXgi  X9k5vFdu  /actionSymbol/bAR4k7Qd.php  request
SpChallengeEntryRequest  MTf2j9aK  Uey5jW2G  /actionSymbol/8DermCsY.php  request
GachaEntryRequest  rj6dxU9w  39cFjtId  /actionSymbol/tUJxSQz7.php  request
TransferCodeIssueRequest  crzI2bA5  T0y6ij47  /actionSymbol/hF0yCKc1.php  request
DungeonResourceLoadMstListRequest  jnw49dUq  3PVu6ReZ  /actionSymbol/Sl8UgmP4.php  request
sgAdsGachaMilestoneRequest  PkSzb2TM  Jp4Fz3qb  /actionSymbol/PkSzb2TM.php  request
DmgRankStartRequest  5P6ULvjg  1d5AP9p6  /actionSymbol/j37Vk5xe.php  request
RmRestartRequest  yh21MTaG  R1VjnNx0  /actionSymbol/NC8Ie07P.php  request
CampaignTieupRequest  mI0Q2YhW  72d5UTNC  /actionSymbol/2u30vqfY.php  request
UnitClassUpRequest  zf49XKg8  L2sTK0GM  /actionSymbol/8z4Z0DUY.php  request
sgExpdMileStoneClaimRequest  r4A791RF  t04N07LQ  /actionSymbol/r4A791RF.php  request
FriendSearchRequest  3siZRSU4  VCL5oj6u  /actionSymbol/6Y1jM3Wp.php  request
ReinforcementSettingRequest  ZSq2y7EX  jUreV31B  /actionSymbol/I1g4ezbP.php  request
RateAppRewardRequest  L0OsxMaT  m1pPBwC3  /actionSymbol/L0OsxMaT.php  request
ResourceAllDownloadRequest  i0d5n1Dp  fL07ojUc  /actionSymbol/Vtx9kFg0.php  request
MissionEndRequest  x5Unqg2d  1tg0Lsqj  /actionSymbol/0ydjM5sU.php  request
DmgRankRetireRequest  W3Z4VF1X  5fkWyeE6  /actionSymbol/8wdmR9yG.php  request
BundleStatusRequest  uLXAMvCT  PrSPuc8c  /actionSymbol/tPc64qmn.php  request
FriendSuggestRequest  iAs67PhJ  j2P3uqRC  /actionSymbol/6TCn0BFh.php  request
sgGachaRerollExeRequest  Dll4rncD  KJ13DlZz  /actionSymbol/QQVXslxB.php  request
ShopUseRequest  73SD2aMR  ZT0Ua4wL  /actionSymbol/w76ThDMm.php  request
RmDungeonStartRequest  R5mWbQ3M  A7V1zkyc  /actionSymbol/NC8Ie07P.php  request
DmgRankEndRequest  s98cw1WA  7pGj8hSW  /actionSymbol/zd5KJ3jn.php  request
RbEntryRequest  f8kXGWy0  EA5amS29  /actionSymbol/30inL7I6.php  request
RmEntryRequest  wx5sg9ye  p2tqP7Ng  /actionSymbol/fBn58ApV.php  request
CraftAddRequest  QkN1Sp64  qz0SG1Ay  /actionSymbol/iQ7R4CFB.php  request
NoticeReadUpdateRequest  pC3a2JWU  iLdaq6j2  /actionSymbol/j6kSWR3q.php  request
RoutineEventUpdateRequest  4kA1Ne05  V0TGwId5  /actionSymbol/WCK5tvr0.php  request
LoginBonusRequest  vw9RP3i4  Vi6vd9zG  /actionSymbol/iP9ogKy6.php  request
MissionRetireRequest  v51PM7wj  oUh1grm8  /actionSymbol/gbZ64SQ2.php  request
ItemCarryEditRequest  UM7hA0Zd  04opy1kf  /actionSymbol/8BE6tJbf.php  request
DailyDungeonSelectRequest  JyfxY2e0  ioC6zqG1  /actionSymbol/9LgmdR0v.php  request
PurchaseListRequest  BT28S96F  X3Csghu0  /actionSymbol/YqZ6Qc1z.php  request
sgMissionUnlockRequest  LJhqu0x6  ZcBV06K4  /actionSymbol/LJhqu0x6.php  request
sgUserExtraChallengeInfoRequest  dzU2t3HA  Qu6RHqbb  /actionSymbol/zgry2aNV.php  request
sgExpdEndRequest  2pe3Xa8bpG  cjHumZ2Jkt  /actionSymbol/2pe3Xa8bpG.php  request
CraftExeRequest  PKDhIN34  ZbHEB15J  /actionSymbol/UyHLjV60.php  request
RoutineGachaUpdateRequest  t60dQP49  Q6ZGJj0h  /actionSymbol/qS0YW57G.php  request
GetBackgroundDownloadInfoRequest  lEHBdOEf  Z1krd75o  /actionSymbol/action.php  request
FriendListRequest  u7Id4bMg  1iV2oN9r  /actionSymbol/p3hwqW5U.php  request
StrongBoxOpenRequest  PIv7u8jU  sgc30nRh  /actionSymbol/48ktHf13.php  request
ChallengeClearRequest  D9xphQ8X  UD5QCa2s  /actionSymbol/dEvLKchl.php  request
CraftEndRequest  WIuvh09n  yD97t8kB  /actionSymbol/9G7Vc8Ny.php  request
SublimationSkillRequest  s48Qzvhd  97Uvrdz3  /actionSymbol/xG3jBbw5.php  request
PurchaseHoldRequest  79EVRjeM  5Mwfq90Z  /actionSymbol/dCxtMZ27.php  request
RbBoardPieceOpenRequest  hqzU9Qc5  g68FW4k1  /actionSymbol/iXKfI4v1.php  request
SignInRequest  FckReppg  g8iv4P8I  /actionSymbol/8DRAiBXE.php  request
SignInCheckRequest  ufKRrNc7  F83wNKFt  /actionSymbol/Qfpa24mZ.php  request
SignOutRequest  o96pHAp3  UtE1qMv3  /actionSymbol/KwChAfkX.php  request
DailyQuestShareRequest  3sTwRcpq  PMyGMdUa  /actionSymbol/3sTwRcpq.php  request
slotgame::SgSlotInfoRequest  uVswysHK  gsRV5GaY  /actionSymbol/action.php  request
slotgame::SgSlotLotRequest  DW2yAFjB  vyLhq43j  /actionSymbol/action.php  request
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