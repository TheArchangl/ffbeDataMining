<?php
    /**
     * User: aEnigma
     * Date: 24.01.2017
     * Time: 17:39
     */

    // $region = 'jp';

    use Sol\FFBE\GameFile;
    use Solaris\FFBE\GameHelper;
    use Solaris\FFBE\Mst\EquipItemMstList;

    require_once dirname(__DIR__) . '/bootstrap.php';
    require_once dirname(__DIR__) . '/helpers.php';

    // patterns
    $entries = [];
    foreach (GameFile::loadMst('F_VISION_CARD_EXP_PATTERN_MST') as $row)
        $entries[$row['exp_pattern']][$row['level'] - 1] = (int) $row['93fnYUJG'];

    file_put_contents(join('/', [DATA_OUTPUT_DIR, $region, 'vision_cards_exp_patterns.json']),
                      toJSON($entries, false));

    $entries = [];
    foreach (GameFile::loadMst('F_VISION_CARD_GROW_MST') as $row)
        $entries[$row['stat_pattern']][$row['max_level']][$row['level'] - 1] = (int) $row['UjKF93ok'];

    file_put_contents(join('/', [DATA_OUTPUT_DIR, $region, 'vision_cards_stat_patterns.json']),
                      toJSON($entries, false));

    // cards
    $entries = [];
    foreach (GameFile::loadMst('F_VISION_CARD_MST') as $row) {
        $id    = $row['5giCMUd2'];
        $entry = [
            // id => $id,
            'name'             => $row['name'],
            'compendium_id'    => (int) $row['not_dex_no'],
            // 'compendium_id2' => (int)$row['order_index'],
            'compendium_shown' => (bool) $row['DispDict'],
            //
            'rarity'           => (int) $row['rarity'],
            'game_id'          => (int) $row['game_id'],
            'job_id'           => (int) $row['job_id'],
            'unit_type'        => (int) $row['unit_type'],

            'stats' => [
                'HP'  => GameHelper::readIntArray($row['hp']),
                'MP'  => GameHelper::readIntArray($row['mp']),
                'ATK' => GameHelper::readIntArray($row['atk']),
                'DEF' => GameHelper::readIntArray($row['def']),
                'MAG' => GameHelper::readIntArray($row['mag']),
                'SPR' => GameHelper::readIntArray($row['spr']),

                'element_resist' => GameHelper::readElement($row['element_resist'], false) ?: null,
                'status_resist'  => array_merge(GameHelper::readStatus($row['status_resist']) ?? [], EquipItemMstList::parseSpecialResists($row['special_resist'])) ?: null,
            ],

            'skills'       => [],
            'restriction'  => null,
            'max_level'    => (int) $row['max_level'],
            'exp_pattern'  => (int) $row['exp_pattern'],
            'stat_pattern' => (int) $row['stat_pattern'],
            'merge_exp'    => (int) $row['merge_bonus_exp'],
            'merge_stats'  => readIntArray($row['merge_bonus_stats']),
        ];

        if (trim($row['special_resist2'], '0,')) {
            var_dump(['special_resist2!', $row['special_resist2'], $id]);
            echo 'WEOH WOEH WARNING';
            die();
        }

        $entries[$id] = $entry;
    }

    // skills
    foreach (GameFile::loadMst('F_VISION_CARD_LV_ACQUIRE_MST') as $row) {
        if ($row['4JZgSr3A'] !== '0')
            throw new LogicException('4JZgSr3A');

        $id = $row['5giCMUd2'];
        if (! isset($entries[$id]))
            continue;

        $entries[$id]['skills'][$row['level']] = array_merge($entries[$row['5giCMUd2']][$row['level']] ?? [],
                                                             GameHelper::readIntArray($row['magic_ids']),
                                                             GameHelper::readIntArray($row['ability_ids']));
    }

    foreach (GameFile::loadMst('F_VISION_CARD_ABILITY_ACT_COND_MST') as $row) {
        [
            '5giCMUd2' => $id,
            '9qQg2zjH' => $rule_id,
            'desc'     => $desc,
            'target'   => $skill_id,
        ] = $row;

        if (! isset($entries[$id]))
            continue;

        $entries[$id]['restriction'][$skill_id] = [(int) $rule_id, $desc];
    }

    // strings
    foreach (GameFile::loadMst('F_VISION_CARD_EXPLAIN_MST') as $row) {
        ['5giCMUd2' => $id,] = $row;

        if (! isset($entries[$id]))
            continue;

        if ($row['explain_long'] !== $row['explain_short'])
            throw new LogicException('explain_short');

        if ($row['explain_long'] !== $row['fYhnz5N9'])
            throw new LogicException('fYhnz5N9');

        foreach (['MkrpdP61', 'LwS0a8xn', 'SAh4cnW2'] as $k)
            if (! empty($row[$k]))
                throw new LogicException($k);

        $entries[$id]['details'] = [
            'desc'     => $row['explain_short'],
            'info'     => $row['J5QKLzW7'],
            'shop'     => $row['explain_shop'],
            'music'    => $row['1ux5sP4L'],
            'music_id' => $row['Y4PER0gB'],
        ];
    }


    $file    = join('/', [DATA_OUTPUT_DIR, $region, 'vision_cards.json']);
    $entries = toJSON($entries);
    file_put_contents($file, $entries);
