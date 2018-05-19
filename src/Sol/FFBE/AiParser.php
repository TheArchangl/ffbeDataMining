<?php
    /**
     * User: aEnigma
     * Date: 09.03.2017
     * Time: 11:57
     */

    namespace Sol\FFBE;

    use Solaris\FFBE\GameHelper;

    class AiStep {
        public $weight     = 1;
        public $conditions = [];
        public $set_flags  = [];
        public $action     = 'attack';
        public $skill      = null;
        public $target     = 'random';

        public function parseAction(array $step) {
            // set flags
            [$a, $flags] = explode('@', $step['action_str'], 2);

            $flags = str_replace('@', '', $flags); // merge both flag types
            $flags = rtrim($flags, ',');
            $flags = explode(',', $flags);
            $flags = array_chunk($flags, 2);
            $flags = array_filter($flags, function ($flag) {
                return $flag[0] != -1;
            });

            $this->action    = $a;
            $this->set_flags = $flags;
        }

        public function parseConditions($step) {
            $this->weight = $step['weight'] / 100;

            $arr = [];
            foreach ($step['conditions']['flags'] as $val) {
                list($type, $value) = explode(':', $val);

                if ($type == 'skill') {
                    $this->skill = (int)$value;
                    continue;
                }

                $arr[] = ['self', $type, $value];
            }

            // state conditions
            foreach ($step['conditions']['states'] as $val) {
                list($target_range, $num, $type, $value) = explode(':', $val);
                $target_type  = $num ?: 'any';
                $target_range = AiParser::CONDITION_TARGET[$target_range] ?? $target_range;

                $arr[] = ["{$target_range}_{$target_type}", $type, $value]; //$this->parseCondition("{$target_range}_{$target_type}", $type, $value);
            }

            $this->conditions = $arr;
        }

        public function parseTarget($step) {
            [$target, $i] = explode(':', $step['target'], 2);

            $this->target = $i == 0
                ? $target
                : $step['target'];
        }

        public function conditionContainsFlag($flag) {
            foreach ($this->conditions as [$target, $type, $value])
                if ($value == $flag && substr($type, 0, 3) == 'flg')
                    return true;

            return false;
        }
    }

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
            36 => 'unk1',
            'unk2',
            'unk3',
            'unk4',
            'unk5',
        ];

        /** @var string[] */
        const CONDITION_TARGET = [
            1 => 'player',// Intangir2
            2 => 'party', // Robo
            3 => 'party', // Moon
            4 => 'party', // Orthros & Typhon
            5 => 'player', // Azure Knight (any)
        ];

        /**
         * @param int   $id
         * @param array $ai
         * @param int[] $skillset
         * @param array $skills
         *
         * @return string
         */
        public static function parseAI($id, array $ai, array $skillset, array $skills) {
            $parser = new static($id, $ai);
            $str    = $parser->parse($skillset, $skills);

            return $str;
        }

        private $flag_turns = [];
        private $id;

        /** @var AiStep[] */
        private $steps;
        private $var_types = [];

        //
        private $skillset = [];
        private $skills   = [];

        public function __construct($id, array $ai) {
            if (isset($ai['AI']['actions']))
                $ai = $ai['AI']['actions'];

            $this->id         = $id;
            $this->steps      = $this->parseSteps($ai);
            $this->var_types  = $this->readVarTypes($this->steps);
            $this->flag_turns = $this->countFlagUsage();
        }

        /**
         * @param AiStep[] $steps
         *
         * @return array
         */
        public function readVarTypes(array $steps) {
            $var_types = [];
            foreach ($steps as $step)
                foreach ($step->conditions as [$target, $type, $value])
                    switch ($type) {
                        case 'flg_cntup_act':
                        case 'flg_cntup_over':
                        case 'flg_cntup_under':
                            $var_num             = explode(',', $value)[0] + 25;
                            $var_types[$var_num] = 'count';
                            break;

                        case 'flg_timer_act':
                        case 'flg_timer_over':
                        case 'flg_timer_under':
                            $var_num             = explode(',', $value)[0] + 20;
                            $var_types[$var_num] = 'timer';
                            break;

                        case 'flg_on':
                        case 'flg_off':
                            $var_types[$value] = ($value > 10)
                                ? 'volatile'
                                : 'flag';
                            break;

                        case 'flg2_on':
                        case 'flg2_off':
                            $var_types[$value] = 'global';
                            break;
                    }

            return $var_types;
        }

        /**
         * @param string $target
         * @param string $type
         * @param int    $value
         *
         * @return string
         */
        protected function parseCondition($target, $type, $value) {
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

                    return "{$letters[$var_num]} == $value";

                case 'flg_cntup_over':
                    list($var_num, $value) = explode(',', $value);
                    $var_num += 25;

                    return "{$letters[$var_num]} > $value";

                case 'flg_cntup_under':
                    list($var_num, $value) = explode(',', $value);
                    $var_num += 25;

                    return "{$letters[$var_num]} < $value";

                //
                case 'flg_timer_act':
                    list($var_num, $value) = explode(',', $value);
                    $var_num += 20;

                    return "{$letters[$var_num]} == $value";

                case 'flg_timer_over':
                    list($var_num, $value) = explode(',', $value);
                    $var_num += 20;

                    return "{$letters[$var_num]} > $value";

                case 'flg_timer_under':
                    list($var_num, $value) = explode(',', $value);
                    $var_num += 20;

                    return "{$letters[$var_num]} < $value";

                case 'actbetween':
                    return "isTurnMod($value)";

                case 'act':
                    // if ($value == 1)
                    //     return "isFirstTurn()";

                    return "isTurn({$value})";

                case 'limited_act':
                    if ($value == 1)
                        return "onlyOncePerFight()";

                    return "uses() < {$value}";

                case 'flg_on':
                case 'flg2_on':
                    return "{$letters[$value]} == True";
                    break;

                case 'flg_off':
                case 'flg2_off':
                    // check for simple turn flags
                    if ($this->isSimpleTurnFlag($value))
                        return "onlyOncePerTurn()";

                    if ($letters[$value] == 'lemon')
                        var_dump($this->flag_turns[$value]);

                    //
                    return "{$letters[$value]} == False";

                case 'abnormal_state':
                    $state = $value == 0 ? 'any' : GameHelper::STATUS_TYPE[$value - 1] ?? $value;

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

                case "before_turn_guard":
                    assert($value == 1);

                    return "{$unit}lastTurnGuarded()";

                case "special_user_id":
                    // king mog
                    $name = Strings::getString('MST_ABILITY_NAME', $value);

                    return "{$unit}lastTurnUsedAbility($value, \"{$name}\")";

                default:
                    if (preg_match('~^physics_(.+)$~', $type, $match))
                        return ($value == 1 ? 'not ' : '') . "{$unit}sufferedDamageLastTurn('{$match[1]}', 'phys')";

                    elseif (preg_match('~^magic_(.+?)$~', $type, $match))
                        return ($value == 1 ? 'not ' : '') . "{$unit}sufferedDamageLastTurn('{$match[1]}', 'mag')";

                    return "{$unit}is('{$type}:{$value}')";
            }
        }

        /**
         * @param AiStep $step
         *
         * @return string
         */
        protected function formatFlagsSetter(AiStep $step) {
            if (empty($step->set_flags))
                return '';

            // sort flags by type
            $flags = $step->set_flags;

            uasort($flags, function ($a, $b) {
                return ($this->var_types[$b[0]] ?? 0) <=> ($this->var_types[$a[0]] ?? 0);
            });

            $strs = [];
            foreach ($flags as list($flag, $value)) {
                $name   = static::VAR_NAMES[$flag] ?? "Unk{$flag}";
                $action = null;
                $note   = null;

                switch ($this->var_types[$flag] ?? null) {
                    default:
                        $action = "{$name}  = $value";
                        break;

                    case 'count':
                        if ($value == 0)
                            $action = "{$name}  = $value";

                        elseif ($value == 1)
                            $action = "{$name} += 1";

                        elseif ($value == -1)
                            $action = "{$name} -= 1";

                        break;

                    case 'volatile':
                        if ($this->isSimpleTurnFlag($flag) && $step->conditionContainsFlag($flag))
                            continue 2;

                        $action = "{$name}  = " . ($value ? 'True' : 'False');
                        $note   = "reset next turn";
                        break;

                    case 'global':
                        $action = "{$name}  = " . ($value ? 'True' : 'False');
                        $note   = "global (?)";
                        break;

                    case 'flag':
                        $note   = "persistent";
                        $action = "{$name}  = " . ($value ? 'True' : 'False');
                        break;

                    case 'timer':
                        $note   = "timer";
                        $action = $value
                            ? "{$name}  = Timer.create()"
                            : "{$name}.stop()";

                        break;
                }

                $strs[] = $this->formatLine($action, $note);
            }

            return join("\n", $strs);
        }

        /**
         * @param int $flag_num
         *
         * @return bool
         */
        protected function isSimpleTurnFlag($flag_num): bool {
            $var = $this->flag_turns[$flag_num];
            if (array_sum($var) == 0)
                return true;

            return $var['set'] == 1 && $var['get'] == 1;
        }

        private function parse($skillset, $skills) {
            $this->skills   = $skills;
            $this->skillset = $skillset;

            $code = [];
            foreach ($this->steps as $k => $step) {
                $conditions = $this->formatConditions($step);
                $actions    = [$this->formatAction($step), $this->formatFlagsSetter($step)];
                $actions    = join("\n", array_filter($actions));

                if ($k == 0)
                    // first
                    $code[] = "if   {$conditions}:\n{$actions}\n";

                elseif (!empty($step->conditions))
                    // others
                    $code[] = "elif {$conditions}:\n{$actions}\n";

                else {
                    // last
                    $code[] = "else:\n{$actions}\n";
                    break;
                }
            }

            return join("\n", $code);
        }

        private function parseSteps(array $data) {
            $steps = [];
            foreach ($data as $step) {
                $s = new AiStep();
                $s->parseConditions($step);
                $s->parseAction($step);
                $s->parseTarget($step);

                $steps[] = $s;
            }

            // add dummy atk step
            $steps[] = new AiStep();

            return $steps;
        }

        private function countFlagUsage() {
            $arr = array_fill(0, 50, ['get' => 0, 'set' => 0]);

            foreach ($this->steps as $step) {
                foreach ($step->conditions as [$target, $type, $value])
                    if (substr($type, 0, 3) == 'flg')
                        $arr[$value]['get']++;

                foreach ($step->set_flags as [$flag, $value])
                    $arr[$flag]['set']++;
            }

            return $arr;
        }

        private function formatConditions(AiStep $step) {
            if (empty($step->conditions) && $step->weight == 1)
                return 'True';

            $conditions = $step->conditions;
            $conditions = array_map(function ($arr) { return $this->parseCondition(...$arr); }, $conditions);

            if ($step->weight < 1)
                array_unshift($conditions, sprintf("random() <= %.2f", $step->weight));

            return join(' and ', $conditions);
        }

        private function formatAction(AiStep $step) {
            switch ($step->action) {
                case 'turn_end':
                    return $this->formatLine("end_turn()");
                    break;

                case 'skill':
                    if ($step->skill == 0)
                        return $this->formatLine("useRandomSkill('{$step->target}')");


                    $skill_id = $this->skillset[$step->skill - 1] ?? null;
                    if ($skill_id == null)
                        $note = "Unknown skill - wrong skillset?";

                    else {
                        $skill   = $this->skills[$skill_id];
                        $effects = implode(', ', $skill['effects']);
                        $effects = str_replace("\n", " ", $effects);

                        $note = "{$skill['name']} ({$skill_id}): {$effects}";
                    }

                    return $this->formatLine("useSkill({$step->skill}, '{$step->target}')", $note);

                default:
                    // mostly attack
                    return $this->formatLine("{$step->action}('{$step->target}')");
            }
        }

        /**
         * @param string $string
         * @param string $note
         *
         * @return string
         */
        private function formatLine($string, $note = null) {
            if (empty($note))
                return "\t{$string}";

            return sprintf("\t%-30s # %s", $string, $note);
        }

    }