<?php


    namespace Sol\FFBE;

    use Solaris\FFBE\GameHelper;

    class AiRow {
        /**
         * @param array $row
         *
         * @return AiRow
         */
        public static function parseRow(array $row): AiRow {
            [$states, $flags] = explode('@#', rtrim($row['conditions'], '@'));
            $states = explode('@', $states);
            $states = array_filter($states, static function ($val) { return $val !== '0:non:non:non'; });

            $flags = explode('@', $flags);
            $flags = array_filter($flags, static function ($val) { return $val !== 'non:0'; });


            $o                 = new static();
            $o->priority       = $row['priority'];
            $o->weight         = $row['weight'];
            $o->target         = $row['AI_SEARCH_COND'];
            $o->states         = $states;
            $o->flags          = $flags;
            $o->conditions_str = $row['conditions'];
            $o->action_str     = $row['action'];
            $o->AI_ACT_TARGET  = $row['AI_ACT_TARGET'];
            $o->AI_COND_TARGET = $row['AI_COND_TARGET'];
            $o->AI_COND1       = $row['AI_COND1'];
            $o->AI_PARAM1      = $row['AI_PARAM1'];
            $o->AI_COND2       = $row['AI_COND2'];
            $o->AI_PARAM2      = $row['AI_PARAM2'];

            return $o;
        }

        /**
         * @param string $string
         *
         * @return array
         */
        protected static function parseSetFlags($string): array {
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

        public $priority;
        public $weight;
        public $target;
        public $states;
        public $flags;
        public $conditions_str;
        public $action_str;
        public $AI_ACT_TARGET;
        public $AI_COND_TARGET;
        public $AI_COND1;
        public $AI_PARAM1;
        public $AI_COND2;
        public $AI_PARAM2;

        /**
         * @return string
         */
        public function parseTarget(): string {
            [$target, $i] = explode(':', $this->target, 2);

            return $i == 0
                ? $target
                : $this->target;
        }

        /**
         * @return array
         */
        public function parseConditions(): array {
            $conditions = [];

            // RNG
            if ($this->weight != 100)
                $conditions[] = sprintf('random() <= %.2f', $this->weight / 100);

            // states
            foreach ($this->states as $val) {
                [$target_range, $num, $type, $value] = explode(':', $val);
                $target = AiParser::formatTarget($target_range, $num);

                $conditions[] = AiParser::parseCondition($target, $type, $value);
            }

            // flags
            $skill_num = -1;
            foreach ($this->flags as $val) {
                [$key, $val] = explode(':', $val);

                if ($key === 'skill')
                    $skill_num = (int) $val;

                else
                    $conditions[] = AiParser::parseCondition('self', $key, $val);
            }

            return [$conditions, $skill_num];
        }

        public function parseActionFlags(): array {
            [$action, $flags] = explode('@', $this->action_str, 2);
            $flags = self::parseSetFlags($flags);

            return [$action, $flags];
        }
    }

    class AiAction {
        /**
         * @param array $row
         *
         * @return AiAction
         */
        public static function parseRow(array $row): AiAction {
            $row = AiRow::parseRow($row);

            [$action, $flags] = $row->parseActionFlags();
            [$conditions, $skill_num] = $row->parseConditions();

            $step             = new static;
            $step->action     = $action;
            $step->flags      = $flags;
            $step->conditions = $conditions;
            $step->skill_num  = $skill_num;
            $step->target     = $row->parseTarget();

            return $step;
        }

        /** @var AiRow */
        public $row;

        /** @var string */
        public $target = 'random';

        /** @var string */
        public $action = 'attack';

        /** @var string[] */
        public $conditions = [];

        /** @var int[] */
        public $flags;

        /** @var int */
        public $skill_num = -1;
    }