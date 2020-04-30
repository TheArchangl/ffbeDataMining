<?php
    /**
     * User: aEnigma
     * Date: 22.06.2017
     * Time: 12:53
     */

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\GameHelper;

    const MAGIC_TYPE_EXPED = [
        'UNKNOWN',
        'Black', // swapped
        'White', // swapped
        'Green',
        'Blue',
    ];

    require_once dirname(__DIR__) . '/bootstrap.php';
    require_once dirname(__DIR__) . '/helpers.php';
    require_once __DIR__ . '/read_strings.php';


    $difficulties = [];
    foreach (GameFile::loadMst('F_EXPEDITION_DIFFICULTY_MST') as $row) {
        $id = (int) $row['DifficultyId'];

        $difficulties[$id] = $row;
    }

    $entries = [];
    /**
     * @param array $row
     *
     * @return array
     */
    function parseUnitBonus($row) {
        if ($row['RecommendedType'] == null)
            return null;

        [$type, $value, $bonus] = explode(':', $row['RecommendedType']) + [null, null, null];

        if (in_array(null, [$type, $value, $bonus]))
            return null;

        switch ($type) {
            case 1:
            case 2:
            case 3:
            case 4:
                // magic level
                $type  = MAGIC_TYPE_EXPED[$type] . ' magic';
                $value = (int) $value;
                break;

            case 0:
                // equip
                $type  = 'equipable';
                $value = GameHelper::EQUIPMENT_TYPE[$value];
                break;

            case 5:
                // Game
                $type  = 'game';
                $value = Strings::getString('MST_GAME_TITLE_NAME', $value);
                break;

            default:
                break;
        }

        return [
            'type'  => $type,
            'value' => $value,
            'bonus' => (int) $bonus,
        ];
    }

    foreach (GameFile::loadMst('F_EXPEDITION_MST') as $row) {
        $id = (int) $row['ExpdId'];

        $diff = $difficulties[$row['DifficultyId']];

        $entry = [
            'name'       => $row['name'],
            'type'       => (int) $row['type'],
            'cost'       => (int) $row['cost'] ?: null,
            'rank'       => $diff['name'],
            'difficulty' => (int) $row['ChallengeValue'],
            'duration'   => (int) $diff['Duration'],
            'units'      => (int) $row['UnitCount'],
            'required'   => $row['RequiredUnitSeriesList'] == 0
                ? null
                : Strings::getString('MST_UNIT_NAME', $row['RequiredUnitSeriesList']),

            'next_id'        => (int) $row['NextExpdId'] ?: null,

            // rewards
            'reward'         => null,
            'relics'         => null,
            'exp_levels'     => array_map('toInt', explode(':', $row['TimeEventFlags'])),

            // diff?
            'unit_bonus'     => null,
            'unit_bonus_max' => (int) $row['MaxContributionPerChar'],
            'item_bonus'     => null,
            'stat_bonus'     => [
                'HP'  => (int) $row['bonus_hp'],
                'MP'  => (int) $row['bonus_mp'],
                'ATK' => (int) $row['bonus_atk'],
                'DEF' => (int) $row['bonus_def'],
                'MAG' => (int) $row['weight_mag'],
                'SPR' => (int) $row['weight_spr'],
            ],

            'strings' => [
                'name' => Strings::getStrings('MST_EXPN_NAME', $id),
                'desc' => Strings::getStrings('MST_EXPN_DESC', $id),
            ],
        ];

        $relics = GameHelper::parseMstItem($row['RelicReward']);
        assert($relics[1] == 1209000808);

        $entry['relics'] = (int) $relics[3];
        $entry['reward'] = array_combine(
            ['type', 'id', 'name', 'amount'],
            array_slice(GameHelper::parseMstItem($row['DisplayReward']), 0, 4)
        );

        if (in_array($row['ConsumableItemList'], ['NULL', '', null], true))
            $entry['item_bonus'] = null;

        else {
            $bonus               = GameHelper::parseMstItem($row['ConsumableItemList']);
            $entry['item_bonus'] = [[
                                        'id'     => (int) $bonus[1],
                                        'name'   => $bonus[2],
                                        'amount' => (int) $bonus[3],
                                        'bonus'  => (int) $bonus[4][0],
                                    ]];
        }

        $entry['unit_bonus'] = parseUnitBonus($row);

        assert(strpos($row['ConsumableItemList'], ',') === false);
        assert(strpos($row['RequiredUnitSeriesList'], ',') === false);
        assert(strpos($row['RelicReward'], ',') === false);
        assert(strpos($row['DisplayReward'], ',') === false);

        $entries[$id] = $entry;
    }

    $data = toJSON($entries, false);
    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/expeditions.json", $data);