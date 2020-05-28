<?php

    namespace Sol\FFBE;

    class AiRow {
        /**
         * @param string[] $row
         *
         * @return AiRow
         */
        public static function parseRow(array $row): AiRow {
            [$states, $flags] = explode('@#', rtrim($row['conditions'], '@'));
            $states = explode('@', $states);
            $states = array_filter($states, static fn($val): bool => $val !== '0:non:non:non');

            $flags = explode('@', $flags);
            $flags = array_filter($flags, static fn($val): bool => $val !== 'non:0');

            $o           = new static();
            $o->priority = $row['priority'];
            $o->weight   = $row['weight'];
            $o->target   = $row['AI_SEARCH_COND'];
            $o->states   = $states;
            $o->flags    = $flags;
            $o->action_str = $row['action'];

            /* unused
            $o->AI_ACT_TARGET  = $row['AI_ACT_TARGET'];
            $o->AI_COND_TARGET = $row['AI_COND_TARGET'];
            $o->AI_COND1       = $row['AI_COND1'];
            $o->AI_PARAM1      = $row['AI_PARAM1'];
            $o->AI_COND2       = $row['AI_COND2'];
            $o->AI_PARAM2      = $row['AI_PARAM2'];
            */

            return $o;
        }

        public int      $priority;
        public float    $weight;
        public string   $target;
        public array    $states;
        public array    $flags;
        public string   $action_str;

        /*
         * unused
        public int                    $AI_ACT_TARGET;
        public int                    $AI_COND_TARGET;
        public string                 $AI_COND1;
        public string                 $AI_PARAM1;
        public string                 $AI_COND2;
        public string                 $AI_PARAM2;
        */


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
    }