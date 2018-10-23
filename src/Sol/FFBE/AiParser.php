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
            1  => 'honey',
            'ramen',
            'sushi',
            'bacon',
            'steak',
            'salad',
            'fries',
            'sugar',
            'pizza',
            'pasta',

            // volatile flags
            11 => 'apple',
            'berry',
            'peach',
            'olive',
            'mango',
            'lemon',
            'grape',
            'melon',
            'guava',
            'gourd',

            // timers
            21 => 'manta',
            'whale',
            'squid',
            'shark',
            'guppy',

            // counters
            26 => 'green',
            'white',
            'black',
            'mauve',
            'azure',

            // global flags
            31 => 'otter',
            'tiger',
            'mouse',
            'goose',
            'horse',

            // unk
            36 => 'unk_1',
            'unk_2',
            'unk_3',
            'unk_4',
            'unk_5',
            'unk_6',
            'unk_7',
            'unk_8',
            'unk_9',
            'unk_0',
        ];

        /** @var string[] */
        const CONDITION_TARGET = [
            1 => '1:unit?', // Intangir2
            2 => '2:party?',  // Robo
            3 => '3:party',  // Moon
            4 => '4:party?',  // Orthros & Typhon
            5 => '5:player?', // Azure Knight (any)
        ];

        private static $skillset;
        private static $skills;

        /**
         * @param array $ai
         * @param int[] $skillset
         * @param array $skills
         *
         * @return string
         */
        public static function parseAI(array $ai, array $skillset, array $skills) {
            static::$skillset = $skillset;
            static::$skills   = $skills;

            // for each step / check
            $steps = [];
            foreach ($ai['AI']['actions'] as $step) {
                [$conditions, $skill_num] = self::parseConditions($step);
                [$action, $flags] = explode('@', $step['action_str'], 2);
                $target = static::parseTarget($step['target']);
                $flags  = self::parseSetFlags($flags);

                $steps[] = [$conditions, $action, $target, $skill_num, $flags];

                if (empty($conditions))
                    // always true
                    // -> ignore further steps
                    break;
            }

            return static::formatOutput($steps);
        }

        /**
         * @param array $step
         *
         * @return array
         */
        protected static function parseConditions($step): array {
            $conditions = [];

            // RNG
            if ($step['weight'] != 100)
                $conditions[] = sprintf("random() <= %.2f", $step['weight'] / 100);

            // states
            foreach ($step['conditions']['states'] as $val) {
                list($target_range, $num, $type, $value) = explode(':', $val);
                $target_type  = $num ?: 'any';
                $target_range = static::CONDITION_TARGET[$target_range] ?? $target_range;

                $conditions[] = self::parseCondition("{$target_range}_{$target_type}", $type, $value);
            }

            // flags
            $skill_num = -1;
            foreach ($step['conditions']['flags'] as $val) {
                list($key, $val) = explode(':', $val);

                if ($key == 'skill')
                    $skill_num = (int) $val;

                else
                    $conditions[] = self::parseCondition('self', $key, $val);
            }

            return [$conditions, $skill_num];
        }

        /**
         * @param int $var_num
         *
         * @return string
         */
        protected static function getVarName(int $var_num) {
            return static::VAR_NAMES[$var_num] ?? "var_{$var_num}";
        }

        /**
         * @param string $action
         * @param string $target
         * @param int    $skill_num
         *
         * @return array
         */
        protected static function parseAction($action, $target, $skill_num): array {
            $target = static::formatTarget($target);

            switch ($action) {
                case 'turn_end':
                    return ['endTurn()', ''];

                case 'skill':
                    if ($skill_num == 0)
                        return ["useRandomSkill('{$target}')", ''];

                    $action = "useSkill({$skill_num}, '{$target}')";

                    // get skill id from num
                    $skill_id = static::$skillset[$skill_num - 1] ?? null;
                    if ($skill_id == null)
                        return [$action, "# Unknown skill - wrong skillset?"];

                    // get skill from id
                    $skill   = static::$skills[$skill_id];
                    $effects = $skill['effects'];
                    $effects = str_replace("\n", ", ", $effects);

                    return [$action, "# {$skill['name']} ({$skill_id}): {$effects}"];

                case "attack":
                    return ["{$action}('{$target}')", ""];

                case "wait":
                    $target = $target == 'random'
                        ? ''
                        : "'{$target}'";

                    return ["wait({$target})", "# No action"];

                default:
                    return ["{$action}('{$target}')", "# Unknown action"];
            }
        }

        /**
         * @param string $string
         *
         * @return array
         */
        protected static function parseSetFlags($string) {
            $temp = GameHelper::readParameters($string, '@,');

            $arr = [];
            $off = [0, 30];
            foreach ($temp as $k => $flags) {
                array_pop($flags); // empty last
                $flags = array_chunk($flags, 2);

                foreach ($flags as [$flag, $val])
                    if ($flag != -1)
                        $arr[] = [$flag + $off[$k], $val];
            }

            return $arr;
        }

        /**
         * @param array[] $flags
         *
         * @return string
         */
        protected static function formatFlags(array $flags) {
            // sort by var num
            uasort($flags, function ($a, $b) { return $b[0] <=> $a[0]; });

            // format
            $code = '';
            foreach ($flags as list($var_num, $value)) {
                $note   = '';
                $action = '';
                $letter = static::getVarName($var_num);
                $type   = static::getVarType($var_num);

                switch ($type) {
                    default:
                        $action = "{$letter}  = $value";
                        break;

                    case 'counter':
                        if ($value == 1)
                            $action = "{$letter} += 1";

                        elseif ($value == -1)
                            $action = "{$letter} -= 1";

                        else
                            $action = "{$letter}  = $value";

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
                        if ($value) {
                            $note   = "# timer";
                            $action = "{$letter}  = Timer.create()";
                        } else {
                            $note   = "# timer";
                            $action = "{$letter}.reset()";
                        }

                        break;
                }

                $code .= sprintf("\t%-30s %s\n", $action, $note);
            }

            return $code;
        }

        /**
         * @param string $target
         * @param string $type
         * @param string $value
         *
         * @return string
         */
        protected static function parseCondition($target, $type, $value) {
            $target = static::formatTarget($target);
            $unit   = ($target == 'self')
                ? 'self'
                : "unit('{$target}')";


            switch ($type) {
                // hp
                case 'hp_pr_under':
                    return "{$unit}.HP < " . ($value / 100);

                case 'hp_pr_over':
                    return "{$unit}.HP > " . ($value / 100);

                // counters
                case 'flg_cntup_act':
                    list($var_num, $value) = explode(',', $value);

                    return static::getVarName($var_num + 25) . " == {$value}";

                case 'flg_cntup_over':
                    list($var_num, $value) = explode(',', $value);

                    return static::getVarName($var_num + 25) . " >= {$value}";

                case 'flg_cntup_under':
                    list($var_num, $value) = explode(',', $value);

                    return static::getVarName($var_num + 25) . " <= {$value}";

                // timers
                case 'flg_timer_act':
                    list($var_num, $value) = explode(',', $value);

                    return static::getVarName($var_num + 20) . " == {$value}";

                case 'flg_timer_over':
                    list($var_num, $value) = explode(',', $value);

                    return static::getVarName($var_num + 20) . " >= {$value}";

                case 'flg_timer_under':
                    list($var_num, $value) = explode(',', $value);

                    return static::getVarName($var_num + 20) . " <= {$value}";

                // turn conds
                case 'actbetween':
                    return "isTurnMod($value)";

                case 'act':
                    return "isTurn({$value})";

                // flags
                case 'limited_act':
                    if ($value == 1)
                        return "once()";

                    return "currentAction.timesExectuted() < {$value}";

                case 'flg_on':
                    return static::getVarName($value) . " == True";
                case 'flg2_on':
                    return static::getVarName($value + 30) . " == True";

                case 'flg_off':
                    return static::getVarName($value) . " == False";
                case 'flg2_off':
                    return static::getVarName($value + 30) . " == False";

                // states
                case 'abnormal_state':
                    $state = $value == 0
                        ? 'any'
                        : GameHelper::STATUS_TYPE[$value - 1] ?? $value;

                    return "{$unit}.hasStatus('{$state}')";

                case 'stdown_buff':
                    $state = $value == 0 ? 'any' : GameHelper::DEBUFF_TYPE[$value - 3] ?? $value;

                    return "{$unit}.hasDebuff('{$state}')";

                case 'stup_buff':
                    $state = $value == 0 ? 'any' : GameHelper::DEBUFF_TYPE[$value - 3] ?? $value;

                    return "{$unit}.hasBuff('{$state}')";

                case 'alive':
                    $state = $value == 0 ? 'Dead' : 'Alive';

                    return "{$unit}.is{$state}()";

                // actions
                case "before_turn_guard":
                    assert($value == 1);

                    return "{$unit}.usedGuardLastTurn()";

                case "before_turn_attack":
                    assert($value == 1);

                    return "{$unit}.usedNormalAttack()";

                case "before_turn_lb":
                    assert($value == 1);

                    return "{$unit}.usedLbLastTurn()";

                case "before_turn_sm":
                    assert($value == 1);

                    return "{$unit}.usedSummonLastTurn()";

                case "before_turn_mg":
                    assert($value == 1);

                    return "{$unit}.usedMagicLastTurn()";

                case "total_damage_over":
                    return "{$unit}.totalDamage() > {$value}";

                case "before_turn_item_attack":
                    assert($value == 1);

                    return "{$unit}.lastTurnHitBy('item')";

                case "before_turn_beast_attack":
                    assert($value == 1);

                    return "{$unit}.lastTurnHitBy('esper')";

                case "before_turn_hit_attack":
                    assert($value == 1);

                    return "{$unit}.lastTurnHitBy('attack')";

                case "before_turn_magic_attack":
                    assert($value == 1);

                    return "{$unit}.lastTurnHitBy('spell')";

                case "before_turn_special_attack":
                    assert($value == 1);

                    return "{$unit}.lastTurnHitBy('ability')";

                case "before_turn_item_heal":
                    assert($value == 1);

                    return "{$unit}.lastTurnHealedBy('item')";

                case "before_turn_magic_heal":
                    assert($value == 1);

                    return "{$unit}.lastTurnHealedBy('spell')";

                case "before_turn_special_heal":
                    assert($value == 1);

                    return "{$unit}.lastTurnHealedBy('ability')";

                case "special_user_id":
                    // king mog
                    $name = Strings::getString('MST_ABILITY_NAME', $value);

                    return "{$unit}.lastTurnUsedAbility($value, '{$name}')";

                case "rifrect_mode":
                    return ($value == 1 ? '' : 'not ') . "{$unit}.hasReflect()";

                default:
                    if (preg_match('~^physics_(.+)$~', $type, $match))
                        return ($value == 1 ? 'not ' : '') . "{$unit}.sufferedDamageLastTurn('{$match[1]}', 'physical')";

                    elseif (preg_match('~^magic_(.+?)$~', $type, $match))
                        return ($value == 1 ? 'not ' : '') . "{$unit}.sufferedDamageLastTurn('{$match[1]}', 'magical')";

                    return "{$unit}.is('{$type}:{$value}')";
            }
        }

        /**
         * @param mixed[] $steps
         *
         * @return string
         */
        private static function formatOutput(array $steps) {
            $first = true;
            $code  = "";
            foreach ($steps as [$conditions, $action, $target, $skill_num, $flags]) {
                /** @var string[] $conditions */
                // conditions
                if (empty($conditions))
                    $code .= $first
                        ? "if   True:\n"
                        : "else:\n";

                else {
                    $conditions = join(' and ', $conditions);
                    $code       .= $first
                        ? "if   {$conditions}:\n"
                        : "elif {$conditions}:\n";
                }

                // action + flags
                $code .= self::formatAction($action, $target, $skill_num);
                $code .= self::formatFlags($flags);
                $code .= "\n";

                $first = false;
            }

            return $code;
        }

        /**
         * @param string $action
         * @param string $target
         * @param int    $skill_num
         *
         * @return string
         */
        private static function formatAction($action, $target, $skill_num) {
            [$action, $note] = static::parseAction($action, $target, $skill_num);

            return sprintf("\t%-30s %s\n", $action, $note);
        }

        /**
         * @param string $str
         *
         * @return string
         */
        private static function parseTarget($str) {
            [$target, $i] = explode(':', $str, 2);

            return $i == 0
                ? $target
                : $str;
        }

        /**
         * @param int $num
         *
         * @return string
         */
        private static function getVarType($num) {
            if ($num < 11)
                return 'flag';

            if ($num < 21)
                return 'volatile';

            if ($num < 26)
                return 'timer';

            if ($num < 31)
                return 'counter';

            return 'unknown';
        }

        /**
         * @param string $target
         *
         * @return string
         */
        private static function formatTarget(string $target) {
            switch (strtolower($target)) {
                case "self":
                    return "self";


                case "mind_max":
                    return "highest SPR";

                case "int_max":
                    return "highest MAG";

                case "hp_max":
                    return "highest HP";

                case "mp_max":
                    return "highest MP";

                case "random":
                default:
                    return $target;
            }
        }
    }