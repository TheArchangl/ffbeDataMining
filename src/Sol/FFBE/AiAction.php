<?php


    namespace Sol\FFBE;

    use Solaris\FFBE\GameHelper;

    class AiAction {
        /**
         * @param AiRow $row
         *
         * @return AiAction
         */
        public static function parseRow(AiRow $row): AiAction {
            [$action, $flags] = static::parseActionFlags($row->action_str);
            [$conditions, $skill_num] = $row->parseConditions();

            $step             = new static;
            $step->action     = $action;
            $step->flags      = $flags;
            $step->conditions = $conditions;
            $step->skill_num  = $skill_num;
            $step->target     = $row->parseTarget();

            return $step;
        }

        /** @var string */
        public string $target = 'random';

        /** @var string */
        public string $action = 'attack';

        /** @var string[] */
        public array $conditions = [];

        /** @var int[] */
        public array $flags;

        /** @var int */
        public int $skill_num = -1;


        /**
         * @param string $string
         *
         * @return array
         */
        protected static function parseActionFlags(string $string): array {
            [$action, $flags] = explode('@', $string, 2);

            return [$action, self::parseSetFlags($flags)];
        }

        /**
         * @param string $string
         *
         * @return array
         */
        protected static function parseSetFlags(string $string): array {
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
    }