<?php
    /**
     * User: aEnigma
     * Date: 05.02.2018
     * Time: 01:02
     */

    namespace Sol\FFBE\Reader;

    ini_set('pcre.backtrack_limit', '520000000') or die('could not set pcre.backtrack_limit');
    ini_set('pcre.recursion_limit', '520000000') or die('could not set pcre.recursion_limit');
    ini_set('pcre.jit', false) or die('could not set pcre.recursion_limit');

    abstract class MstReader {
        protected array $entries;

        public function save($file): void {
            $data = $this->parseData();
            $data = $this->formatOutput($data);

            file_put_contents($file, $data);
        }

        abstract protected function parseData():array;

        /**
         * @param array $entries
         *
         * @return string
         */
        protected function formatOutput(array $entries):string {
            $data = toJSON($entries);

            // un-indent arrays
            $keys = ['effect_frames', 'attack_damage', 'attack_frames', 'effects_raw', 'levels', 'requirements', 'roles'];
            $keys = array_map('preg_quote', $keys);
            // $keys = join('|', $keys);

            foreach ($keys as $key) {
                $data = preg_replace_callback("~(\"{$key}\":\\s+)\\[\\s+(?P<values>(?:[-.\\d]+|\"[^\"]*\"|\\[]|\\[\\s*(?P>values)\\s*])(?:,\\s+(?P>values)\\s*)*)\\s*]~",
                    static function ($match) {
                        $trimmed = preg_replace('~\r?\n\s+~', '', $match[2]);
                        $trimmed = str_replace(',', ', ', $trimmed);

                        return "{$match[1]}[{$trimmed}]";
                    }, $data);

                if (preg_last_error() > 0) {
                    $errs = [
                        PREG_NO_ERROR              => 'No error',
                        PREG_INTERNAL_ERROR        => 'Internal error',
                        PREG_BACKTRACK_LIMIT_ERROR => 'Backtrack limit reached error',
                        PREG_RECURSION_LIMIT_ERROR => 'Recursion limit reached error',
                        PREG_BAD_UTF8_ERROR        => 'Bad UTF-8 error',
                        PREG_BAD_UTF8_OFFSET_ERROR => 'Bad UTF-8 offset error',
                        PREG_JIT_STACKLIMIT_ERROR  => 'JIT stack limit reached error',
                    ];

                    var_dump([preg_last_error(), $errs[preg_last_error()], $key]);
                    die();
                }
            }

            // un-indent objects
            $data = preg_replace_callback('/("cost":\s*{)([^}]+)(},)/m', static function ($match) {
                $trimmed = preg_replace('~\r?\n\s+~', '', $match[2]);
                $trimmed = str_replace(',', ', ', $trimmed);

                return "{$match[1]}{$trimmed}{$match[3]}";
            }, $data);

            return $data;
        }
    }