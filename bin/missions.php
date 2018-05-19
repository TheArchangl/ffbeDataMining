<?php
    /**
     * User: aEnigma
     * Date: 05.12.2017
     * Time: 15:42
     */

    use Sol\FFBE\ChallengeParser;
    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";


    $entries = [];
    foreach (GameFile::loadMst('MissionMstList') as $row) {
        $mission_id = (int)$row['mission_id'];
        $dungeon_id = (int)$row['dungeon_id'];

        if ($row['completion_reward'] == '')
            $reward = null;

        else {
            $reward = parseReward($row['completion_reward']);
            $reward = [
                $reward[0], // item type
                (int)$reward[1], // item id
                (int)$reward[3], // amount
                // $reward[4][0], // max
            ];
        }

        $name = (GameFile::getRegion() == 'gl')
            ? Strings::getString('MST_MISSION_NAME', $mission_id, 0) ?? $row['name']
            : $row['name'];

        $flags = readIntArray($row['flags'], ':');
        $entry = [
            'dungeon_id' => $dungeon_id,
            'name'       => $name,
            'type'       => ['UNKNOWN', 'BATTLE', 'EXPLORATION'][$row['mission_type']],
            'wave_count' => (int)$row['num_waves'],
            //
            'cost_type'  => ['NRG', 'STA'][$row['CostType']] ?? $row['CostType'],
            'cost'       => (int)$row['cost'],
            //
            'flags'      => [
                'continue_allowed' => $flags[1] == 1,
                'escape_allowed'   => $flags[2] == 1,
            ],

            // temp
            // 'temp' => [
            //   'switch_open' => $row['switch_open'],
            //   'switch_info' => $row['switch_info'],
            //   'switch_non_info' => $row['switch_non_info'],
            //   'effect_switch_info' => $row['effect_switch_info'] ?? null,
            // ],

            // rewards
            'reward'     => $reward,
            'gil'        => (int)$row['gil'], // base vals w/o monsters -> useless
            'exp'        => (int)$row['exp'], // base vals w/o monsters -> useless

            'challenges' => [],
        ];

        $entries[$mission_id] = $entry;
    }

    foreach (GameFile::loadMst('ChallengeMstList') as $row) {
        $mission_id   = (int)$row['mission_id'];
        $challenge_id = (int)$row['challenge_id'];

        $parsed = ChallengeParser::parse($row['condition'], true);
        $parsed = array_map('utf8_encode', $parsed);

        $reward = parseReward($row['reward']);
        $reward = [
            $reward[0], // item type
            (int)$reward[1], // item id
            (int)$reward[3], // amount
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