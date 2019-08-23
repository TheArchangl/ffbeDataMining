<?php
    /**
     * User: aEnigma
     * Date: 05.02.2018
     * Time: 01:02
     */

    namespace Sol\FFBE\Reader;

    ini_set('pcre.backtrack_limit', "520000000") or die('could not set pcre.backtrack_limit');
    ini_set('pcre.recursion_limit', "520000000") or die('could not set pcre.recursion_limit');
    ini_set('pcre.jit', false) or die('could not set pcre.recursion_limit');

    abstract class MstReader {
        protected $entries;

        public function save($file) {
            $data = $this->parseData();
            $data = $this->formatOutput($data);

            file_put_contents($file, $data);
        }

        abstract protected function parseData();

        /**
         * @param array $entries
         *
         * @return string
         */
        protected function formatOutput(array $entries) {
            ksort($entries);
            $data = toJSON($entries, false);

            // un-indent arrays
            $keys = ['effect_frames', 'attack_damage', 'attack_frames', 'effects_raw', 'levels', 'requirements', 'roles'];
            $keys = array_map('preg_quote', $keys);
            $keys = join('|', $keys);

            $data = preg_replace_callback('~("(?:' . $keys . ')":\s+)\[\s+(?P<values>(?:[-.\d]+|"[^"]*"|\[]|\[\s*(?P>values)\s*])(?:,\s+(?P>values)\s*)*)\s*]~',
                function ($match) {
                    $trimmed = preg_replace('~\r?\n\s+~', '', $match[2]);
                    $trimmed = str_replace(',', ', ', $trimmed);

                    return "{$match[1]}[{$trimmed}]";
                }, $data);

            $errs = [
                PREG_NO_ERROR              => 'No error',
                PREG_INTERNAL_ERROR        => 'Internal error',
                PREG_BACKTRACK_LIMIT_ERROR => 'Backtrack limit reached error',
                PREG_RECURSION_LIMIT_ERROR => 'Recursion limit reached error',
                PREG_BAD_UTF8_ERROR        => 'Bad UTF-8 error',
                PREG_BAD_UTF8_OFFSET_ERROR => 'Bad UTF-8 offset error',
                PREG_JIT_STACKLIMIT_ERROR  => 'JIT stack limit reached error',
            ];

            if (preg_last_error() > 0) {
                var_dump(['preg_err', preg_last_error(), $errs[preg_last_error()]]);
                die();
            }

            // un-indent objects
            $data = preg_replace_callback('/("cost":\s*{)([^}]+)(},)/sm', function ($match) {
                $trimmed = preg_replace('~\r?\n\s+~', '', $match[2]);
                $trimmed = str_replace(',', ', ', $trimmed);

                return "{$match[1]}{$trimmed}{$match[3]}";
            }, $data);

            return $data;
        }
    }