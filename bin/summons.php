<?php
    /**
     * User: aEnigma
     * Date: 25.02.2017
     * Time: 15:14
     */

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\GameHelper;
    use Solaris\Formatter\SkillFormatter;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    $entries = [];
    foreach (GameFile::loadMst('BeastMstList') as $row) {
        $beast_id = (int) $row['beast_id'];

        $names = $region == 'gl'
            ? Strings::getStrings('MST_BEAST_NAME', $beast_id)
            : [$row['name']];

        $entries[$beast_id] = [
            'names'   => $names,
            'image'   => $row['beast_art'],
            'icon'    => $row['beast_icon'],
            'skill'   => [],
            'color'   => null,
            'entries' => [],
        ];
    }

    // stats
    foreach (GameFile::loadMst('F_BEAST_STATUS_MST') as $row) {
        $beast_id                                          = (int) $row['beast_id'];
        $entry                                             = [
            'stats'          => formatStats($row),
            'element_resist' => readIntArray($row['element_resist']),
            'status_resist'  => readIntArray($row['status_resist']),
            'exp_pattern'    => (int) $row['exp_pattern'],
            'stat_pattern'   => (int) $row['stat_pattern'],
        ];
        $entries[$beast_id]['entries'][$row['rarity'] - 1] = $entry;

        assert($row['magical_resist'] == 0);
        assert($row['physical_resist'] == 0);
        assert($row['special_resist'] == '');
    }
    // Skill
    foreach (GameFile::loadMst('BeastSkillMstList') as $row) {
        $skill_id = (int) $row['beast_skill_id'];
        $beast_id = (int) substr($skill_id, 1, 2);

        $name = $region == 'gl'
            ? Strings::getStrings('MST_BEASTSKILL_NAME', $skill_id)
            : $row['name'];

        $desc = $region == 'gl'
            ? Strings::getStrings('MST_BEASTSKILL_DESCRIPTION', $skill_id)
            : $row['desc'];

        $entry = [
            // effect
            'effects'       => null,
            'effects_raw'   => null,

            // frames
            'attack_count'  => 0,
            'attack_damage' => [],
            'attack_frames' => [],
            'effect_frames' => [],

            'strings' => [
                'name' => $name,
                'desc' => $desc,
            ],
        ];

        $_row = [
            \Solaris\FFBE\MstKey::TARGET_RANGE => $row['target_range'],
            \Solaris\FFBE\MstKey::TARGET       => $row['target'],
            \Solaris\FFBE\MstKey::EFFECT_TYPE  => $row['effect_type'],
            \Solaris\FFBE\MstKey::EFFECT_PARAM => $row['effect_param'],
        ];

        $skill              = new \Solaris\FFBE\Mst\LimitBurstMst();
        $skill->id          = $skill_id;
        $skill->attack_type = $row['attack_type'];
        $skill->elements    = GameHelper::readElement($row['element_inflict'], true);
        $skill->effects     = \Solaris\FFBE\Mst\SkillMstList::parseEffects($_row, true);

        $entry['effects']     = SkillFormatter::formatEffects($skill, $container[\Solaris\FFBE\Mst\SkillMstList::class]);
        $entry['effects_raw'] = SkillFormatter::formatEffectsRaw($skill);

        $entry = \Sol\FFBE\Reader\SkillReader::parseFrames($row, $entry);

        $entries[$beast_id]['skill'][$skill_id] = $entry;
    }
    // bonus color
    foreach (GameFile::loadMst('ItemExtBeastMstList') as $row) {
        if (!isset($row['item_id']))
            continue;

        $bonus = $row['beast_exp_bonus'];
        if ($bonus == '')
            continue;

        $name  = Strings::getString('MST_ITEM_NAME', $row['item_id'], 0);
        $color = explode(" ", $name, 2)[0];
        $bonus = readParameters($bonus, ":,");

        foreach ($bonus as list($beast_id, $factor)) {
            $entries[$beast_id]['color'][$color] = $factor / 100;
        }
        // $entries[] = $row;
    }

    // foreach ($entries as $beast_id => $entry)
    //     var_dump($entry) || die();
    //     $entries[$beast_id]['color'] = $entry['color'] == null
    //         ? null
    //         : array_unique($entry['color']);

    // Board
    $boards = [];
    // build parent map
    $parent_map = [];
    foreach (GameFile::loadMst('BeastBoardPieceMstList') as $row) {
        $node_id = (int) $row['beast_board_piece_id'];

        $child_nodes = readIntArray($row['beast_board_piece_childnodes']);
        foreach ($child_nodes as $child_node_id)
            $parent_map[$child_node_id] = $node_id;
    }

    // fill data
    foreach (GameFile::loadMst('BeastBoardPieceMstList') as $row) {
        $node_id  = (int) $row['beast_board_piece_id'];
        $beast_id = (int) $row['beast_id'];

        if (!isset($entries[$beast_id]))
            continue;

        $reward = null;
        if ($row['reward_type'] != 0)
            $reward = [
                \Solaris\FFBE\Mst\BeastBoardPieceMst::REWARD_TYPE[$row['reward_type']],// ?? "Unknown {$row['reward_type']}",
                (int) $row['reward_param'],
            ];

        $entry = [
            'parent_node_id' => $parent_map[$node_id] ?? null,
            'reward'         => $reward,
            'position'       => readIntArray(str_replace(':', ',', $row['board_piece_pos'])),
            'cost'           => (int) $row['point'],
        ];

        assert(count($boards[$beast_id]) == $row['index']);
        $boards[$beast_id][$node_id] = $entry;
    }

    assert(count(GameFile::loadMst('BeastBoardPieceExtMstList')) == 1);

    $data = toJSON($entries, false);

    foreach (['effect_frames', 'attack_damage', 'attack_frames', 'effects_raw'] as $x)
        $data = preg_replace_callback('/(\"(?:' . $x . ')":\s+)([^:]+)(,\s+"[^"]+":)/sm', function ($match) {
            $trimmed = preg_replace('~\r?\n\s+~', '', $match[2]);
            $trimmed = str_replace(',', ', ', $trimmed);

            return $match[1] . $trimmed . $match[3];
        }, $data);


    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/summons.json", $data);
    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/summons_boards.json", toJSON($boards));