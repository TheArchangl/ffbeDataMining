<?php
    /**
     * User: aEnigma
     * Date: 09.03.2017
     * Time: 11:57
     */

    namespace Sol\FFBE;

    use Solaris\FFBE\GameHelper;

    class AiParser {
        /** @var string[] */
        const VAR_NAMES = [
            // persistant flags
            1  => 'honey', 'ramen', 'sushi', 'bacon', 'steak', 'salad', 'fries', 'sugar', 'pizza', 'pasta',

            // volatile flags
            11 => 'apple', 'berry', 'peach', 'olive', 'mango', 'lemon', 'grape', 'melon', 'guava', 'gourd',

            // timers
            21 => 'manta', 'whale', 'squid', 'shark', 'guppy',

            // counters
            26 => 'green', 'white', 'black', 'mauve', 'azure',

            // global flags
            31 => 'otter', 'tiger', 'mouse', 'goose', 'horse',

            // unk
            36 => 'unk_1', 'unk_2', 'unk_3', 'unk_4', 'unk_5', 'unk_6', 'unk_7', 'unk_8', 'unk_9', 'unk_0',
        ];

        /** @var string[] */
        const CONDITION_TARGET = [
            1 => '1:player?',// Intangir2
            2 => '2:party?', // Robo
            3 => '3:party?', // Moon
            4 => '4:party?', // Orthros & Typhon
            5 => '5:player?', // Azure Knight (any)
        ];

        public static function readVarTypes($ai) {
            $var_types = [];
            $conds     = [];

            // echo json_encode($ai, JSON_PRETTY_PRINT);

            foreach ($ai['AI']['actions'] as $step) {
                foreach ($step['conditions']['flags'] as $val) {
                    list($key, $val) = explode(':', $val);

                    if ($key == 'skill')
                        continue;
                    $conds[] = [$key, $val];
                }

                foreach ($conds as list($cond_type, $cond_value))
                    switch ($cond_type) {
                        case 'flg_cntup_act':
                        case 'flg_cntup_over':
                        case 'flg_cntup_under':
                            $var_num             = explode(',', $cond_value)[0] + 25;
                            $var_types[$var_num] = 'count';
                            break;

                        case 'flg_timer_act':
                        case 'flg_timer_over':
                        case 'flg_timer_under':
                            $var_num             = explode(',', $cond_value)[0] + 20;
                            $var_types[$var_num] = 'timer';
                            break;

                        case 'flg_on':
                        case 'flg_off':
                            $var_types[$cond_value] = ($cond_value > 10)
                                ? 'volatile'
                                : 'flag';
                            break;

                        case 'flg2_on':
                        case 'flg2_off':
                            $var_types[$cond_value] = ($cond_value > 10)
                                ? 'volatile'
                                : 'flag';
                            break;
                    }
            }

            return $var_types;
        }

        /**
         * @param array $ai
         * @param int[] $skillset
         * @param array $skills
         *
         * @return string
         */
        public static function parseAI(array $ai, array $skillset, array $skills) {
            $code      = '';
            $letters   = static::VAR_NAMES; //array_merge(range('a', 'z'), range('A', 'Z'));
            $var_types = AiParser::readVarTypes($ai);

            $first = true;

            foreach ($ai['AI']['actions'] as $step) {
                $conditions = [];

                if ($step['weight'] != 100)
                    $conditions[] = sprintf("random() <= %.2f", $step['weight'] / 100);

                // foreach ($step['conditions']['flags'] as $flags) {
                //     list($cond_type, $cond_value) = explode(':', $flags, 2);

                $skill_num = 0;
                $conds     = [];

                foreach ($step['conditions']['states'] as $val) {
                    list($target_range, $num, $type, $value) = explode(':', $val);
                    $target_type  = $num ?: 'any';
                    $target_range = static::CONDITION_TARGET[$target_range] ?? $target_range;

                    $conditions[] = self::parseCondition("{$target_range}_{$target_type}", $type, $value);
                }

                foreach ($step['conditions']['flags'] as $val) {
                    list($key, $val) = explode(':', $val);

                    if ($key == 'skill')
                        $skill_num = $val;

                    else
                        $conds[] = [$key, $val];
                }

                list($a, $flags) = explode('@', $step['action_str'], 2);
                $actions   = [];
                $actions[] = $a;

                $flags = str_replace('@', '', $flags); // merge both flag types
                $flags = rtrim($flags, ',');
                $flags = explode(',', $flags);
                $flags = array_chunk($flags, 2);
                $flags = array_filter($flags, function ($flag) { return $flag[0] != -1; });

                foreach ($conds as list($cond_type, $cond_value))
                    $conditions[] = self::parseCondition('self', $cond_type, $cond_value);

                if (empty($conditions)) {
                    $code .= $first
                        ? "if   True:\n"
                        : "else:\n";

                    $break = true;
                }
                else {
                    $conditions = implode(' and ', $conditions);
                    $code       .= $first ? "if   {$conditions}:\n"
                        : "elif {$conditions}:\n";

                    $break = false;
                }

                $first = false;

                // todo target?
                $target = static::formatTarget($step['target']);

                // unit action
                foreach ($actions as $action) {
                    $note = '';
                    switch ($action) {
                        case 'turn_end':
                            $action = 'end_turn()';
                            break;

                        case 'skill':
                            if ($skill_num == 0) {
                                $action = "useRandomSkill('{$target}')";
                            }
                            else {
                                $action = "useSkill({$skill_num}, '{$target}')";

                                $skill_id = $skillset[$skill_num - 1] ?? null;
                                if ($skill_id == null)
                                    $note = "# Unknown skill - wrong skillset?";

                                else {
                                    $skill   = $skills[$skill_id];
                                    $effects = $skill['effects'];
                                    $effects = str_replace("\n", ", ", $effects);

                                    $note = "# {$skill['name']} ({$skill_id}): {$effects}";
                                }
                            }
                            break;

                        default:
                            $action = "{$action}('{$target}')";
                    }

                    $code .= sprintf(
                        "\t%-30s %s\n",
                        $action,
                        $note
                    );
                }

                // $code .= "\n";

                // set flags
                $code .= self::formatFlags($flags, $var_types, $letters);

                $code .= "\n";

                if ($break === true)
                    break;
            }

            return $code;
        }

        /**
         * @param $target
         * @param $type
         * @param $value
         *
         * @return string
         */
        protected static function parseCondition($target, $type, $value) {
            $letters = static::VAR_NAMES;
            $unit    = ($target == 'self')
                ? 'self.'
                : "unit('{$target}').";

            switch ($type) {
                case 'hp_pr_under':
                    return "{$unit}HP < " . ($value / 100);

                case 'hp_pr_over':
                    return "{$unit}HP > " . ($value / 100);

                case 'flg_cntup_act':
                    list($var_num, $value) = explode(',', $value);
                    $var_num += 25;

                    return "{$letters[$var_num]} == " . ($value);

                case 'flg_cntup_over':
                    list($var_num, $value) = explode(',', $value);
                    $var_num += 25;

                    return "{$letters[$var_num]} >= " . ($value);

                case 'flg_cntup_under':
                    list($var_num, $value) = explode(',', $value);
                    $var_num += 25;

                    return "{$letters[$var_num]} <= " . ($value);

                //
                case 'flg_timer_act':
                    list($var_num, $value) = explode(',', $value);
                    $var_num += 20;

                    return "{$letters[$var_num]} == " . ($value);

                case 'flg_timer_over':
                    list($var_num, $value) = explode(',', $value);
                    $var_num += 20;

                    return "{$letters[$var_num]} >= " . ($value);

                case 'flg_timer_under':
                    list($var_num, $value) = explode(',', $value);
                    $var_num += 20;

                    return "{$letters[$var_num]} <= " . ($value);

                case 'actbetween':
                    return "isTurnMod($value)";

                case 'act':
                    // if ($value == 1)
                    //     return "isFirstTurn()";

                    return "isTurn({$value})";

                case 'limited_act':
                    if ($value == 1)
                        return "once()";

                    return "uses() < {$value}";

                case 'flg_on':
                    return "{$letters[$value]} == True";
                case 'flg2_on':
                    return "{$letters[$value]}* == True";

                case 'flg_off':
                    return "{$letters[$value]} == False";
                case 'flg2_off':
                    return "{$letters[$value]}* == False";

                case 'abnormal_state':
                    $state = $value == 0
                        ? 'any'
                        : GameHelper::STATUS_TYPE[$value - 1] ?? $value;

                    return "{$unit}hasStatus('{$state}')";

                case 'stdown_buff':
                    $state = $value == 0 ? 'any' : GameHelper::DEBUFF_TYPE[$value - 3] ?? $value;
                    return "{$unit}hasDebuff('{$state}')";

                case 'stup_buff':
                    $state = $value == 0 ? 'any' : GameHelper::DEBUFF_TYPE[$value - 3] ?? $value;
                    return "{$unit}hasBuff('{$state}')";

                case 'alive':
                    $state = $value == 0 ? 'Dead' : 'Alive';
                    return "{$unit}is{$state}()";

                case "before_turn_guard":
                    assert($value == 1);

                    return "{$unit}usedGuardLastTurn()";

                case "before_turn_attack":
                    assert($value == 1);

                    return "{$unit}usedNormalAttack()";

                case "before_turn_lb":
                    assert($value == 1);

                    return "{$unit}usedLbLastTurn()";

                case "before_turn_sm":
                    assert($value == 1);

                    return "{$unit}usedSummonLastTurn()";

                case "before_turn_mg":
                    assert($value == 1);

                    return "{$unit}usedMagicLastTurn()";

                case "total_damage_over":
                    return "{$unit}totalDamage() > {$value}";

                case "before_turn_item_attack":
                    assert($value == 1);
                    return "{$unit}lastTurnHitBy('item')";

                case "before_turn_beast_attack":
                    assert($value == 1);
                    return "{$unit}lastTurnHitBy('esper')";

                case "before_turn_hit_attack":
                    assert($value == 1);
                    return "{$unit}lastTurnHitBy('attack')";

                case "before_turn_magic_attack":
                    assert($value == 1);
                    return "{$unit}lastTurnHitBy('spell')";

                case "before_turn_special_attack":
                    assert($value == 1);
                    return "{$unit}lastTurnHitBy('ability')";

                case "before_turn_item_heal":
                    assert($value == 1);
                    return "{$unit}lastTurnHealedBy('item')";

                case "before_turn_magic_heal":
                    assert($value == 1);
                    return "{$unit}lastTurnHealedBy('spell')";

                case "before_turn_special_heal":
                    assert($value == 1);
                    return "{$unit}lastTurnHealedBy('ability')";

                case "special_user_id":
                    // king mog
                    $name = Strings::getString('MST_ABILITY_NAME', $value);

                    return "{$unit}lastTurnUsedAbility($value, '{$name}')";

                default:
                    if (preg_match('~^physics_(.+)$~', $type, $match))
                        return ($value == 1 ? 'not ' : '') . "{$unit}sufferedDamageLastTurn('{$match[1]}', 'phys')";

                    elseif (preg_match('~^magic_(.+?)$~', $type, $match))
                        return ($value == 1 ? 'not ' : '') . "{$unit}sufferedDamageLastTurn('{$match[1]}', 'mag')";

                    return "{$unit}is('{$type}:{$value}')";
            }
        }

        /**
         * @param array[]  $flags
         * @param array[]  $var_types
         * @param string[] $letters
         *
         * @return string
         */
        protected static function formatFlags($flags, $var_types, $letters) {
            $code = '';

            uasort($flags, function ($a, $b) use ($var_types) {
                return ($var_types[$b[0]] ?? 0) <=> ($var_types[$a[0]] ?? 0);
            });

            foreach ($flags as list($var_num, $value)) {
                $note   = '';
                $action = '';
                $letter = $letters[$var_num];

                switch ($var_types[$var_num] ?? null) {
                    default:
                        $action = "{$letter}  = $value";
                        break;

                    case 'count':
                        if ($value == 0)
                            $action = "{$letter}  = $value";

                        elseif ($value == 1)
                            $action = "{$letter} += 1";

                        elseif ($value == -1)
                            $action = "{$letter} -= 1";

                        break;

                    case 'volatile':
                        $action = "{$letter}  = " . ($value ? 'True' : 'False');
                        $note   = "# reset next turn";
                        break;

                    case 'global':
                        $action = "{$letter}  = " . ($value ? 'True' : 'False');
                        $note   = "# global";
                        break;

                    case 'flag':
                        $note   = "# persistent";
                        $action = "{$letter}  = " . ($value ? 'True' : 'False');
                        break;

                    case 'timer':
                        $note   = "# timer";
                        $action = $value
                            ? "{$letter}  = Timer.create()"
                            : "{$letter}.stop()";

                        break;
                }

                $code .= sprintf(
                    "\t%-30s %s\n",
                    $action,
                    $note
                );
            }

            return $code;
        }

        private static function formatTarget($str) {
            [$target, $i] = explode(':', $str, 2);

            return $i == 0
                ? $target
                : $str;
        }
    }