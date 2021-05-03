<?php
    /**
     * User: aEnigma
     * Date: 27.01.2017
     * Time: 16:13
     */

    use Pimple\Container;
    use Sol\FFBE\GameFile;
    use Sol\FFBE\MstList\IconMstList;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\GameHelper;
    use Solaris\FFBE\Mst\AbilitySkillMst;
    use Solaris\FFBE\Mst\EquipItemMstList;
    use Solaris\FFBE\Mst\LimitBurstMst;
    use Solaris\FFBE\Mst\MagicSkillMst;
    use Solaris\FFBE\Mst\MateriaMstList;
    use Solaris\FFBE\Mst\SkillMst;
    use Solaris\FFBE\Mst\SkillMstList;
    use Solaris\FFBE\MstKey;
    use Solaris\Formatter\SkillFormatter;

    require_once dirname(__DIR__) . '/bootstrap.php';
    require_once dirname(__DIR__) . '/helpers.php';
    require_once __DIR__ . '/read_strings.php';

    /** @var Container $container */
    /** @var string $region */

    IconMstList::init();

    function readSkills(array &$entry, array $row) {
        global $container;

        if ($row['equip_skill_magic'] === '' && $row['equip_skill_ability'] === '')
            return;

        $skills  = [];
        $effects = [];

        $skill_ids = array_merge(
            readIntArray($row['equip_skill_magic']),
            readIntArray($row['equip_skill_ability'])
        );

        foreach ($skill_ids as $skill_id) {
            $skills[] = $skill_id;

            /** @var SkillMst $skill */
            $skill = $container[SkillMstList::class]->getEntry($skill_id);
            if ($skill === null) {
                $effects[] = "Unknown skill ({$skill_id})";
                continue;
            }

            $type = match ($skill::class) {
                MagicSkillMst::class => 'magic',
                AbilitySkillMst::class => $skill->isActive() ? 'ability' : 'passive',
                default => 'skill'
            };

            $effects[] = "Grant '{$skill}' {$type}";
        }

        $entry['skills']  = $skills;
        $entry['effects'] = $effects;
    }

    echo "Reading Equipment\n";
    $entries = [];
    foreach (GameFile::loadMst('F_EQUIP_ITEM_MST') as $row) {
        $id = (int) $row['equip_id'];
        assert($row['is_unique'] === '0');

        $names = $region === 'jp'
            ? [$row['name']]
            : Strings::getStrings('MST_EQUIP_ITEM_NAME', $id);

        $entry = [
            'name'             => $names[0] ?? $row['name'],
            'compendium_id'    => (int) $row['not_dex_no'],
            // 'compendium_id2' => (int)$row['order_index'],
            'compendium_shown' => (bool) $row['DispDict'],
            //
            'rarity'           => (int) $row['rarity'],
            'type_id'          => (int) $row['equip_type'],
            'type'             => GameHelper::EQUIPMENT_TYPE[$row['equip_type']],
            'slot_id'          => (int) $row['equip_slot_type'],
            'slot'             => GameHelper::EQUIPMENT_SLOT_ID[$row['equip_slot_type']],
            // 'unique'        => $row['is_unique'] == 1, // none

            // weapon
            'is_twohanded'     => $row['is_two_handed'] === '1',
            'dmg_variance'     => null,
            'accuracy'         => (int) ($row['accuracy'] ?? 0),

            'requirements' => null,
            'skills'       => null,
            'effects'      => null,

            'stats' => [
                'HP'  => (int) $row['bonus_hp'],
                'MP'  => (int) $row['bonus_mp'],
                'ATK' => (int) $row['bonus_atk'],
                'DEF' => (int) $row['bonus_def'],
                'MAG' => (int) $row['bonus_mag'],
                'SPR' => (int) $row['bonus_spr'],

                'element_resist'  => GameHelper::readElement($row['element_resist'], false) ?: null,
                'element_inflict' => GameHelper::readElement($row['element_inflict']) ?: null,

                'status_resist'  => GameHelper::readStatus($row['status_resist']) ?: null,
                'status_inflict' => GameHelper::readStatus($row['status_inflict']) ?: null,
            ],

            'price_buy'  => (int) $row['price_buy'],
            'price_sell' => (int) $row['price_sell'],

            'icon'    => IconMstList::getFilename($row['icon_id']),
            'strings' => [
                'name'       => $names,
                'desc_short' => Strings::getStrings('MST_EQUIP_ITEM_SHORTDESCRIPTION', $id),
                'desc_long'  => Strings::getStrings('MST_EQUIP_ITEM_LONGDESCRIPTION', $id),
            ],
        ];

        $special = EquipItemMstList::parseSpecialResists($row['special_resist']);
        if ($special)
            $entry['stats']['status_resist'] = array_merge($entry['stats']['status_resist'] ?? [], $special);

        if ($row['special_resist2'])
            die('Special Resist2: ' . json_encode($row['special_resist2'], JSON_THROW_ON_ERROR));

        if ($row['equip_slot_type'] === '1' && $row['atk_variance'] !== GameHelper::WEAPON_DAMAGE_VARIANCE_1H[$row['equip_type']]) {
            printf("\t%24s %9s instead of %9s\n", $names[0], json_encode($row['atk_variance'], JSON_THROW_ON_ERROR), json_encode(GameHelper::WEAPON_DAMAGE_VARIANCE_1H[$row['equip_type']], JSON_THROW_ON_ERROR));

            $entry['dmg_variance'] = explode(',', $row['atk_variance']);
            $entry['dmg_variance'] = array_map(static fn($val) => is_numeric($val) ? $val / 100 : $val, $entry['dmg_variance']);
        }

        // requirements
        $reqs = $row['equip_requirements'];
        if (! empty($reqs)) {
            static $req_types = [1 => 'SEX', 3 => 'UNIT_ID'];

            $reqs = GameHelper::readParameters($reqs, '@');

            if (! isset($req_types[$reqs[0]]))
                throw new \LogicException('no type');

            $reqs[0]               = $req_types[$reqs[0]];
            $entry['requirements'] = $reqs;
        }

        // skills
        readSkills($entry, $row);

        // local
        if ($region === 'jp')
            unset($entry['strings']);

        $entries[$id] = $entry;
    }

    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/equipment.json", toJSON($entries));

    #region Materia
    echo "Reading Materia\n";
    Strings::readTable('MST_ABILITY_SHORTDESCRIPTION');

    $entries = [];
    foreach (GameFile::loadMst(MateriaMstList::getName()) as $row) {
        $id = $row['materia_id'];

        assert($row['is_unique'] === '0');

        if ($region === 'jp')
            $names = [$row['name']];
        else
            $names = Strings::getStrings('MST_MATERIA_NAME', $id);

        $entry = [
            'name'             => $names[0] ?? $row['name'],
            'compendium_id'    => (int) $row['not_dex_no'],
            // 'compendium_id2'   => (int)$row['order_index'],
            'compendium_shown' => (bool) $row['DispDict'],
            // 'item_id'       => (int)$row['inventory_id'],


            'unit_restriction' => null,
            'skills'           => null,
            'effects'          => null,

            'unique'     => false,

            //
            'price_buy'  => (int) $row['price_buy'],
            'price_sell' => (int) $row['price_sell'],

            'icon'    => IconMstList::getFilename($row['icon_id']),
            //
            'strings' => [
                'names'      => $names,
                'desc_short' => Strings::getStrings('MST_MATERIA_SHORTDESCRIPTION', $id),
                'desc_long'  => Strings::getStrings('MST_MATERIA_LONGDESCRIPTION', $id),
            ],
        ];

        // skills
        readSkills($entry, $row);

        // restriction
        $firstSkill = $entry['skills'][count($entry['skills']) - 1] ?? null;
        if ($firstSkill !== null) {
            /** @var SkillMst $skill */
            $skill = $container[SkillMstList::class]->getEntry($firstSkill);
            if (! empty($skill->requirements['unit']))
                $entry['unit_restriction'] = GameHelper::readIntArray($skill->requirements['unit']);
        }

        // local
        if ($region === 'jp')
            unset($entry['strings']);

        $entries[$id] = $entry;
    }

    // read materia limits
    if ($region === 'jp')
        foreach ($entries as $id => $val)
            unset($entries[$id]['unique']);

    else {
        foreach (GameFile::loadMst('F_MATERIA_LIMIT_MST') as $row) {
            $ids   = explode(',', $row['materia_id']);
            $limit = $row['limit'];

            assert($limit === 1);

            foreach ($ids as $id)
                $entries[$id]['unique'] = true;
        }
        /*
         * todo
        $exclusive = [];
        foreach (GameFile::loadMst('F_MATERIA_LIMIT_MST') as $row) {
            $ids   = GameHelper::readIntArray($row['materia_id']);
            $limit = $row['limit'];
            assert($limit == '1');

            if (count($ids) === 1)
                $entries[$ids[0]]['unique'] = true;

            else
                foreach ($ids as $id)
                    $entries[$id]['unique'] = $ids;
        }
        */
    }

    //    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/materia.json", toJSON($entries));
    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/materia.json", toJSON($entries));
    #endregion

    #region Consumables
    echo "Reading Consumables\n";

    $entries = [];
    foreach (GameFile::loadMst('F_ITEM_MST') as $k => $row) {
        $id = (int) $row['item_id'];

        // var_dump($row);

        assert($row['is_unique'] === '0');

        if ($region === 'jp')
            $names = [$row['name']];
        else
            $names = Strings::getStrings('MST_ITEM_NAME', $id);

        $entry = [
            'name'                  => $names[0] ?? $row['name'],
            'type'                  => ['Unknown', 'Consumable', 'Item', 'Awakening'][$row['category']] ?? $row['category'],
            'compendium_id'         => (int) $row['not_dex_no'],
            'compendium_shown'      => (bool) $row['DispDict'],

            //
            'usable_in_combat'      => false,
            'usable_in_exploration' => false,
            'flags'                 => array_map('boolval', readIntArray($row['flags'])),

            //
            'carry_limit'           => (int) $row['carry_limit'],
            'stack_size'            => (int) $row['stack_size'],
            'price_buy'             => (int) $row['price_buy'],
            'price_sell'            => (int) $row['price_sell'],

            //
            'effects'               => null,
            'effects_raw'           => '',

            'icon'    => IconMstList::getFilename($row['icon_id']),
            //
            'strings' => [
                'names'      => $names,
                'desc_short' => Strings::getStrings('MST_ITEM_SHORTDESCRIPTION', $id),
                'desc_long'  => Strings::getStrings('MST_ITEM_LONGDESCRIPTION', $id),
            ],
        ];

        // usability
        $use_case = readIntArray($row['use_case']);
        $flags    = readIntArray($row['flags']);

        $entry['usable_in_combat']      = (bool) $use_case[0];
        $entry['usable_in_exploration'] = (bool) $use_case[1];

        // $entry['flags'][] = (bool)($flags[0]); // 'battle'
        // $entry['flags'][] = (bool)($flags[1]); // 'field'
        // $entry['flags'][] = (bool)($flags[2]); // 'throw'
        // $entry['flags'][] = (bool)($flags[3]); // 'medicine'
        // $entry['flags'][] = (bool)($flags[4]); // 'item'
        // $entry['flags'][] = (bool)($flags[5]); // 'drink'

        // assert(($flags[0] == 1) == ($use_case[0] == 0)) or var_dump('battle', $names[0], [$use_case[0], $flags[0]]);
        // assert(($flags[1] == 1) == ($use_case[1] == 1)) or var_dump('field', $names[0], [$use_case[1], $flags[1]]);

        // if ($entry['flag_throw'])
        //     var_dump([$names[0], 'flag_throw', (bool)$flags[2]]);
        //
        // if ($entry['flag_medicine_knowledge'])
        //     var_dump([$names[0], 'flag_medicine_knowledge', (bool)$flags [3]]);
        // if ($entry['flag_item_knowledge'])
        //     var_dump([$names[0], 'flag_item_knowledge', (bool)$flags[4]]);
        // if ($entry['flag_drink'])
        //     var_dump([$names[0], 'flag_drink', (bool)$flags[5]]);

        if (strlen($row['effect_type']) > 0) {
            $_row = [
                MstKey::TARGET_RANGE => $row['target_range'],
                MstKey::TARGET       => $row['target'],
                MstKey::EFFECT_TYPE  => $row['effect_type'],
                MstKey::EFFECT_PARAM => $row['effect_param'],
            ];

            $skill              = new LimitBurstMst();
            $skill->id          = $id;
            $skill->attack_type = 0;
            $skill->elements    = [];
            $skill->effects     = SkillMstList::parseEffects($_row, true);

            $entry['effects']     = SkillFormatter::formatEffects($skill, $container[SkillMstList::class]);
            $entry['effects_raw'] = SkillFormatter::formatEffectsRaw($skill);
        }
        // tL6G9egd item id? icon id?
        // T9HbA3g6 use animation ?
        // H27Vr9UD item type ? ['Consumable', 'Crafting Material', 'Awakening Material', 'Magicite']

        // local
        if ($region === 'jp')
            unset($entry['strings']);

        $entries[$id] = $entry;
    }

    // trim effects_raw
    $data = toJSON($entries);
    $data = preg_replace_callback('/("effects_raw":\s++)([^:]+)(,\s++"[^"]++":)/m', static function ($match) {
        $trimmed = preg_replace('~\r?\n\s+~', '', $match[2]);
        $trimmed = str_replace(',', ', ', $trimmed);

        return "{$match[1]}{$trimmed}{$match[3]}";
    }, $data);

    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/items.json", $data);
    // file_put_contents(DATA_OUTPUT_DIR . "/{$region}/items_analyze.json", toJSON(arrayGroupValues(GameFile::loadMst('ItemMstList')), false));
    #endregion