<?php
    /**
     * User: aEnigma
     * Date: 05.12.2017
     * Time: 15:42
     */

    use Sol\FFBE\ChallengeParser;
    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\GameHelper;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    function parseRewards($string) {
        if (empty($string))
            return [];

        $rewards = [];

        foreach (explode(',', $string) as $reward) {
            $reward    = parseReward($reward);
            $rewards[] = [
                $reward[0], // item type
                (int) $reward[1], // item id
                (int) $reward[3], // amount
                // $reward[4][0], // max
            ];
        }

        return $rewards;
    }

    const COST_TYPE     = ['NRG', 'Raid', 'Upgrade'];
    const MISSION_TYPE  = ['UNKNOWN', 'BATTLE', 'EXPLORATION'];
    const MISSION_FLAGS = ['continue_allowed', 'escape_allowed', 'unknown_1', 'unknown_2', 'unknown_3', 'unknown_4', 'unknown_5'];

    $entries = [];
    foreach (GameFile::loadMst('F_MISSION_MST') as $row) {
        $mission_id = (int) $row['mission_id'];
        $dungeon_id = (int) $row['dungeon_id'];


        $name = (GameFile::getRegion() == 'gl')
            ? Strings::getString('MST_MISSION_NAME', $mission_id, 0) ?? $row['name']
            : $row['name'];

        $flags = readIntArray($row['flags'], ':');
        $flags = array_map('boolval', $flags);
        $flags = array_slice($flags, 1, 2); // todo all flags?
        $flags = GameHelper::array_use_keys(MISSION_FLAGS, $flags);

        $entry = [
            'dungeon_id' => $dungeon_id,
            'name'       => $name,
            'type'       => MISSION_TYPE[$row['mission_type']],
            'wave_count' => (int) $row['num_waves'],
            //
            'cost_type'  => COST_TYPE[$row['CostType']] ?? $row['CostType'],
            'cost'       => (int) $row['cost'],
            'difficulty' => (int) $row['rarity'],
            //
            'flags'      => $flags,

            // temp
            // 'temp' => [
            //   'switch_open' => $row['switch_open'],
            //   'switch_info' => $row['switch_info'],
            //   'switch_non_info' => $row['switch_non_info'],
            //   'effect_switch_info' => $row['effect_switch_info'] ?? null,
            // ],

            // rewards
            'rewards'    => parseRewards($row['completion_reward']),
            'gil'        => (int) $row['gil'], // base vals w/o monsters -> useless
            'exp'        => (int) $row['exp'], // base vals w/o monsters -> useless

            'challenges' => [],
        ];

        $entries[$mission_id] = $entry;
    }

    foreach (GameFile::loadMst('F_CHALLENGE_MST') as $row) {
        $mission_id   = (int) $row['mission_id'];
        $challenge_id = (int) $row['challenge_id'];

        $parsed = ChallengeParser::parse($row['condition'], true);
        $parsed = array_map('utf8_encode', $parsed);

        $reward = parseReward($row['reward']);
        $reward = [
            $reward[0], // item type
            (int) $reward[1], // item id
            (int) $reward[3], // amount
        ];

        $name = (GameFile::getRegion() == 'gl')
            ? Strings::getString('MST_CHALLENGE_NAME', $challenge_id) ?? $row['name']
            : $row['name'];

        $entries[$mission_id]['challenges'][] = [
            'string' => $name,
            'parsed' => $parsed,
            'reward' => $reward,
        ];
    }


    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/missions.json", toJSON($entries, true));

    /*
    // debug
    if (true)
        return;

    $entries = GameFile::loadMst('MissionMstList');
    $entries = array_combine(array_map("current", $entries), $entries);

    file_put_contents(DATA_OUTPUT_DIR . "/analyze.json", toJSON(arrayGroupValues($entries, [], false)));
*/