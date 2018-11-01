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
            1 => '1:ally',  // Intangir2
            2 => '2:ally',  // Robo
            3 => '3:ally',  // Moon
            4 => '4:ally',  // Orthros & Typhon, Echidna
            5 => '5:enemy', // Azure Knight (any)
            6 => '6:enemy',
            7 => '7:enemy',
            8 => '8:ally', // Monster part
        ];

        private static $skillset;
        private static $skills;
        private static $monsters;
        private static $isFake;

        /**
         * @param array $ai
         * @param int[] $skillset
         * @param array $skills
         * @param array $monsters
         * @param bool  $isFake
         *
         * @return string
         */
        public static function parseAI(array $ai, array $skillset, array $skills, array $monsters, $isFake = false) {
            static::$skillset = $skillset;
            static::$skills   = $skills;
            static::$monsters = $monsters;
            static::$isFake   = $isFake;

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
         * @param int $range
         * @param int $num
         *
         * @return string
         */
        static function formatTarget($range, $num) {
            $str = static::CONDITION_TARGET[$range] ?? "unknown";

            switch ($range) {
                /** @noinspection PhpMissingBreakStatementInspection */
                case 1: // ally  (any)
                    if (!static::$isFake && count(static::$monsters) == 1)
                        return "self";

                case 4: // enemy (any)
                case 5: // enemy (???)
                    if ($num == 0)
                        $num = "any";

                    return "{$str}:{$num}";


                case 2: //  ally by id
                case 6: // enemy by id
                    $name = Strings::getString($num == 2 ? 'MST_MONSTER_NAME' : 'MST_UNIT_NAME', $num);

                    return $name == null
                        ? "{$str}:{$num}"
                        : "{$str}:{$num}:{$name}";

                case 3: //  ally by index
                case 7: // enemy by index
                    $monster = static::getMonsterByIndex($num - 1);

                    return $monster == null
                        ? "{$str}:{$num}"
                        : "{$str}:{$num}:{$monster['name']}";

                case 8: // enemy monster part by ID
                    [$num, $part] = explode('&', $num);
                    $name = Strings::getString('MST_MONSTER_NAME', $num);

                    $part = range("A", "Z")[$part];

                    return $name == null
                        ? "{$str}:{$num}:{$part}"
                        : "{$str}:{$name} {$part}";

                default:
                    return "{$str}:{$num}";
            }
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
                $target = static::formatTarget($target_range, $num);

                $conditions[] = self::parseCondition($target, $type, $value);
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
            $target = static::formatTargetPriority($target);

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
                    $effects = join(", ", $effects);
                    $effects = str_replace("\n", " ", $effects);

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
            $target = static::formatTargetPriority($target);
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

                    return "currentAction.timesExecuted() < {$value}";

                case 'flg_on':
                    return static::getVarName($value) . " == True";
                case 'flg2_on':
                    return static::getVarName((int) $value + 30) . " == True";

                case 'flg_off':
                    return static::getVarName($value) . " == False";
                case 'flg2_off':
                    return static::getVarName((int) $value + 30) . " == False";

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

                case "before_turn_magic_support":
                    assert($value == 1);

                    return "{$unit}.usedSupportMagicLastTurn()";

                case "before_turn_special_support":
                    assert($value == 1);

                    return "{$unit}.usedSupportSkillLastTurn()";

                case "special_user_id":
                    // king mog
                    $name = Strings::getString('MST_ABILITY_NAME', $value);

                    return "{$unit}.lastTurnHitBy($value, '{$name}')";

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

            // replace relative summs if possible
            $code = preg_replace_callback("~Summon ally #(\d+)~", function ($match) {
                $monster = static::getMonsterByIndex($match[1] - 1);
                if ($monster == null)
                    return $match[0];

                return "Summon {$monster['name']} ({$monster['id']})";
            }, $code);

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
        private static function formatTargetPriority(string $target) {
            switch (strtolower($target)) {
                case "self":
                    return "self";

                case "atk_max":
                    return "highest ATK";

                case "def_max":
                    return "highest DEF";

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

        /**
         * @param int $i
         *
         * @return array|null
         */
        private static function getMonsterByIndex($i) {
            if ($i >= count(static::$monsters))
                return null;

            $key = array_keys(static::$monsters)[$i];

            [$monster_id, $part_num] = explode('.', $key);

            $entry = static::$monsters[$key];

            $name = Strings::getString('MST_MONSTER_NAME', $monster_id) ?? $entry['name'];

            if (isset(static::$monsters["{$monster_id}.2"]))
                $name .= " " . range("A", "Z")[$part_num - 1];

            return [
                'id'    => $monster_id,
                'part'  => $part_num,
                'name'  => $name,
                'entry' => $entry
            ];
        }
    }