<?php
    /**
     * User: aEnigma
     * Date: 09.03.2017
     * Time: 11:57
     */

    namespace Sol\FFBE;

    use Solaris\FFBE\GameHelper;

    ini_set('zend.assertions', true);
    ini_set('assert.active', true);

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

            // unk
            31 => 'otter',
            'tiger',
            'mouse',
            'goose',
            'horse',
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

        /** @var string[] */
        const SKILL_TYPES = [
            'ab'      => 'ability',
            'special' => 'ability',
            'sm'      => 'summon',
            'beast'   => 'esper',
            'mg'      => 'magic',
            'lb'      => 'limitburst',
            'hit'     => 'attack',
        ];

        public static  $monsters = [];
        private static $skillset;
        private static $skills;
        private static $isFake;

        public static function parseSteps(array $steps) {

        }

        /**
         * @param AiAction[] $actions
         * @param int[]      $skillset
         * @param array      $skills
         * @param array      $monsters
         * @param bool       $isFake
         *
         * @return string
         */
        public static function parseAI(array $actions, array $skillset, array $skills, array $monsters, $isFake = false) {
            static::$skillset = $skillset;
            static::$skills   = $skills;
            static::$monsters = $monsters;
            static::$isFake   = $isFake;

            // add normal attacks as fallback
            $actions[] = new AiAction();

            // for each step / check
            $steps = [];
            foreach ($actions as $step) {
                $steps[] = $step;

                if (empty($step->conditions))
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
            $str = static::CONDITION_TARGET[$range] ?? "unknown:{$range}";

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
                    $name = Strings::getString($range == 2 ? 'MST_MONSTER_NAME' : 'MST_UNIT_NAME', $num);

                    return $name == null
                        ? "{$str}:{$num}"
                        : "{$str}:{$num}:{$name}";

                case 7: // enemy by index
                    return "{$str}:{$num}";

                case 3: //  ally by index
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
         * @param string $target
         * @param string $type
         * @param string $value
         *
         * @return string
         */
        public static function parseCondition($target, $type, $value) {
            // $target = static::formatTargetPriority($target);
            $unit = ($target == 'self')
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

                    return static::getVarName($var_num + 25) . " < {$value}";

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

                    return "timesExecuted() < {$value}";

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
                    $state = $value == 0 ? 'dead' : 'alive';

                    return "{$unit}.is('{$state}')";

                // actions
                case "total_damage_over":
                    return "{$unit}.totalDamage() > {$value}";

                case "special_user_id":
                    // king mog
                    $name = Strings::getString('MST_ABILITY_NAME', $value);

                    return "{$unit}.hitByLastTurn($value, '{$name}')";

                case "magic_user_id":
                    // Demon Wall
                    $name = Strings::getString('MST_MAGIC_NAME', $value);

                    return "{$unit}.hitByLastTurn($value, '{$name}')";

                case "rifrect_mode":
                    return ($value == 1 ? '' : 'not ') . "{$unit}.hasReflect()";


                case "before_turn_guard":
                case "before_turn_lb":
                case "before_turn_attack":
                case "before_turn_hit_attack":
                case "before_turn_item_attack":

                case "before_turn_sm":
                case "before_turn_beast_attack":

                case "before_turn_mg":
                case "before_turn_magic_attack":
                case "before_turn_magic_heal":
                case "before_turn_magic_support":

                case "before_turn_ab":
                case "before_turn_special_attack":
                case "before_turn_special_heal":
                case "before_turn_special_support":
                    // Reactions
                    $negate = ($value == 1 ? '' : 'not ');

                    $type = substr($type, 12);
                    if (($pos = strrpos($type, '_')) !== false) {
                        $skill_type  = substr($type, 0, $pos);
                        $action_type = substr($type, $pos + 1);
                    }
                    else {
                        $action_type = null;
                        $skill_type  = $type;
                    }

                    $skill_type = self::SKILL_TYPES[$skill_type] ?? $skill_type;

                    switch ($action_type) {
                        default:
                            return "{$negate}{$unit}.usedLastTurn('{$skill_type}')";

                        case 'attack':
                            return "{$negate}{$unit}.hitByLastTurn('{$skill_type}')";

                        case 'heal':
                            return "{$negate}{$unit}.healedByLastTurn('{$skill_type}')";

                        case 'support':
                            return "{$negate}{$unit}.supportedWithLastTurn('{$skill_type}')";
                    }

                case "magic_aero":
                case "magic_dark":
                case "magic_fire":
                case "magic_ice":
                case "magic_light":
                case "magic_quake":
                case "magic_thunder":
                case "magic_water":
                case "physics_aero":
                case "physics_dark":
                case "physics_fire":
                case "physics_ice":
                case "physics_light":
                case "physics_quake":
                case "physics_thunder":
                case "physics_water":
                    [$attack_type, $element] = explode('_', $type, 2);
                    $attack_type = ['physics' => 'physical', 'magic' => 'magical'][$attack_type] ?? $attack_type;

                    $negate = ($value == 1 ? 'not [?]' : '');

                    return "{$negate}{$unit}.sufferedDamageLastTurn('{$attack_type}', '{$element}')";

                case "party_alive_num":
                    $negate = ($value == 1 ? '' : 'not ');
                    $value  = explode(',', $value);
                    $name   = ['monsters', 'player', 'unknown'][$value[0]];

                    return "{$negate} party('{$name}').unitsAlive({$value[1]})";

                case "normal_state":
                    $negate = ($value == 1 ? '' : 'not ');

                    return "{$negate}{$unit}.is('{$type}:{$value}')";

                case "abnormal_state_heal_skill_use_possible":
                case "atk_skill_use_possible":
                case "before_turn_item_heal":
                case "heal_skill_use_possible":
                case "join_party":
                case "lb_use_possible":
                case "skill":
                case "support_skill_use_possible":
                case "turn_act":
                default:
                    return "conditionNotImplemented('{$type}:{$value}')";
            }
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

                    if ($skill_num == -1)
                        return ["useRandomSkill('{$target}')", '# [?]'];

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
                $letter = static::getVarName($var_num);
                $type   = static::getVarType($var_num);

                switch ($type) {
                    default:
                    case 'unknown':
                        if (in_array($value, [0, 1])) {
                            $note   = "# unknown flag type";
                            $action = "{$letter} = " . ($value ? 'True' : 'False');
                        }
                        else {
                            $note   = "# unknown flag type 2";
                            $action = "{$letter} = {$value}";
                        }
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
                        if (!in_array($value, [0, 1]))
                            die("WEEE WOOO");

                        $note   = "# reset next turn";
                        $action = "{$letter}  = " . ($value ? 'True' : 'False');
                        break;

                    case 'flag':
                        if (!in_array($value, [0, 1]))
                            die("WEEE WOOO");

                        $note   = "# persistent";
                        $action = "{$letter}  = " . ($value ? 'True' : 'False');
                        break;

                    case 'timer':
                        if (!in_array($value, [0, 1]))
                            die("WEEE WOOO");

                        if ($value) {
                            $note   = "# timer";
                            $action = "{$letter}  = Timer.create()";
                        }
                        else {
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
         * @param AiAction[] $steps
         *
         * @return string
         */
        private static function formatOutput(array $steps) {
            $first = true;
            $code  = "";

            foreach ($steps as $step) {
                /** @var string[] $conditions */
                // conditions
                if (empty($step->conditions))
                    $code .= $first
                        ? "if   True:\n"
                        : "else:\n";

                else {
                    $conditions = join(' and ', $step->conditions);
                    $code       .= $first
                        ? "if   {$conditions}:\n"
                        : "elif {$conditions}:\n";
                }

                // action + flags
                $code .= self::formatAction($step->action, $step->target, $step->skill_num);
                $code .= self::formatFlags($step->flags ?? []);
                $code .= "\n";

                $first = false;
            }

            // replace relative summs if possible
            $code = self::insertMonsterNames($code);

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

            if ($num < 36)
                return 'volatile';

            return 'unknown';
        }

        /**
         * @param string $target
         *
         * @return string
         */
        private static function formatTargetPriority(string $string) {
            [$target, $value] = explode(':', $string) + ['', ''];

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

                case "disp_order":
                    return "slot:" . (($value ?: 0) + 1);

                case "random":
                default:
                    return $string;
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
            $name  = Strings::getString('MST_MONSTER_NAME', $monster_id) ?: $entry['name'];
            $name  = $entry['name'];

            if (isset(static::$monsters["{$monster_id}.2"]))
                $name .= " " . chr(ord('A') + $part_num - 1);

            return [
                'id'    => $monster_id,
                'part'  => $part_num,
                'name'  => $name,
                'entry' => $entry,
            ];
        }

        /**
         * @param string $code
         *
         * @return string
         */
        public static function insertMonsterNames(string $code) {
            $code = preg_replace_callback("~Summon ally #(\d+)~", function ($match) {
                $monster = static::getMonsterByIndex($match[1] - 1);
                if ($monster == null)
                    return $match[0];

                return "Summon {$monster['name']} ({$monster['id']})";
            }, $code);

            return $code;
        }
    }