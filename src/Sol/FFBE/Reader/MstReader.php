<?php
    /**
     * User: aEnigma
     * Date: 05.02.2018
     * Time: 01:02
     */

    namespace Sol\FFBE\Reader;

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
            $data = toJSON($entries, false);

            foreach (['effect_frames', 'attack_damage', 'attack_frames', 'effects_raw'] as $x)
                $data = preg_replace_callback('/(\"(?:' . $x . ')":\s+)([^:]+)(,\s+"[^"]+":)/sm', function ($match) {
                    $trimmed = preg_replace('~\r?\n\s+~', '', $match[2]);
                    $trimmed = str_replace(',', ', ', $trimmed);

                    return $match[1] . $trimmed . $match[3];
                }, $data);

            return $data;
        }
    }