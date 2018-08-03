<?php
    /**
     * User: aEnigma
     * Date: 03.08.2017
     * Time: 22:51
     */

    namespace Sol\FFBE\Reader;

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\GameHelper;

    class UnitReader extends MstReader {
        /** @var array */
        protected $units = [];
        /** @var array */
        protected $unit_map;

        /**
         * @param string $region
         */
        public function __construct($region) {
            GameFile::setRegion($region);
        }

        /**
         * @return array
         */
        public function parseData() {
            $this->units    = [];
            $this->unit_map = [];
            foreach (GameFile::loadMst('F_UNIT_MST') as $row)
                $this->readUnitRow($row);

            foreach (GameFile::loadMst('F_UNIT_SERIES_LV_ACQUIRE_MST') as $row)
                $this->readUnitSkillRow($row);

            $this->units = array_map([$this, 'sortSkills'], $this->units);

            foreach (GameFile::loadMst('F_UNIT_CLASS_UP_MST') as $row)
                $this->readUnitAwakeningRow($row);

            return $this->units;
        }

        /**
         * @param array $entries
         *
         * @return string
         */
        protected function formatOutput(array $entries) {
            $data = toJSON($entries, false);
            $data = preg_replace_callback(
                '~"skills": \[([^]]+)\]~',
                function ($match) {
                    $padding     = str_pad("\n", strpos($match[1], '{'), ' ', STR_PAD_RIGHT);
                    $padding_end = "\n" . substr($padding, 5);

                    $data = preg_replace('~\s+~', ' ', $match[1]);
                    $data = explode("},", $data);
                    $data = array_map(function ($str) { return trim($str, ' {}'); }, $data);
                    $data = array_map(function ($str) { return "{{$str}}"; }, $data);
                    $data = implode(",{$padding}", $data);

                    return "\"skills\": [{$padding}{$data}{$padding_end}]";
                },
                $data
            );

            return $data;
        }

        /**
         * @param array $row
         */
        public function readUnitRow(array $row) {
            $unit_id     = (int) $row['unit_id'];
            $unit_evo_id = (int) $row['unit_evo_id'];

            // add evo to mapping
            if (!array_key_exists($unit_id, $this->units))
                // new unit
                $unit = $this->readUnitEntry($row);

            else
                // new evo
                $unit = $this->units[$unit_id];

            $unit['rarity_max']            = (int) $row['rarity'];
            $unit_evo                      = $this->readEvoEntry($row);
            $this->unit_map[$unit_evo_id]  = $unit_id;
            $unit['entries'][$unit_evo_id] = $unit_evo;

            $evo_equip = readEquip($row['equip']);
            if ($unit['equip'] != $evo_equip) {
                // fuck kupipi
                $unit_evo['equip'] = $evo_equip;
            }

            if ($row['lb_id'] != 0 && $row['lb_id'] != $row['unit_evo_id'])
                vprintf("Warning: Unit - LB mismatch: %s %d vs %d\n", [$unit['names'][0], $row['lb_id'], $row['unit_evo_id']]);

            $this->units[$unit_id] = $unit;
        }

        /**
         * @param array $row
         *
         * @return array
         */
        protected function readUnitEntry(array $row): array {
            $unit_evo_id = (int) $row['unit_evo_id'];
            $rarity      = (int) $row['rarity'];
            $names       = $this->getLocalization($row['name'], 'MST_UNIT_NAME', $unit_evo_id);

            $arr = [
                'rarity_min' => $rarity,
                'rarity_max' => $row['rarity'],

                'name'  => $names[0] ?? $row['name'],
                'names' => $names,

                'game_id' => (int) $row['game_id'],
                'game'    => null,

                'job_id' => (int) $row['job_id'],
                'job'    => null,

                'sex_id' => (int) $row['sex'],
                'sex'    => GameHelper::UNIT_SEX[$row['sex']],

                'tribe_id' => (int) $row['tribe_id'],

                'is_summonable' => (bool) $row['is_summonable'],

                'TMR'  => readTM($row['trustmaster']),
                'sTMR' => readTM($row['supertrust']),
                /*2
                'move_type'  => (int)$row['MoveType'],
                'move_speed' => (int)$row['MoveSpeed'],
                'wait' => (int)$row['wait'],
                */

                'equip' => readEquip($row['equip']),

                'entries' => [],
            ];

            if (GameFile::getRegion() == 'jp') {
                unset($arr['job']);
                unset($arr['game']);
                unset($arr['names']);
            } else {
                $arr['game'] = Strings::getString('MST_GAME_TITLE_NAME', $row['game_id']);
                $arr['job']  = Strings::getString('MST_JOB_NAME', $row['job_id']);
            }

            return $arr;
        }

        /**
         * @param $row
         *
         * @return array
         */
        protected function readEvoEntry($row) {
            $unit_evo_id = (int) $row['unit_evo_id'];
            $rarity      = (int) $row['rarity'];

            // per evo
            $evo_entry = [
                'compendium_id' => (int) $row['order_index'],
                'rarity'        => $rarity,

                'exp_pattern'  => (int) $row['exp_pattern'],
                'stat_pattern' => (int) $row['stat_pattern'],

                'stats'         => formatStats($row),
                'limitburst_id' => ((int) $row['lb_id']) ?: null,

                'attack_count'  => 0,
                'attack_damage' => [],
                'attack_frames' => [],
                'effect_frames' => [],
                'max_lb_drop'   => (int) $row['lb_fillrate'],
                'ability_slots' => (int) $row['ability_slots'],

                'magic_affinity'  => readIntArray($row['magic_affinity']),
                'element_resist'  => readIntArray($row['element_resist']),
                'status_resist'   => readIntArray($row['status_resist']),
                'physical_resist' => (int) $row['physical_resist'],
                'magical_resist'  => (int) $row['magical_resist'],
                //
                'awakening'       => null,
                //
                'strings'         => [
                    'description' => $this->getLocalization($row['name'], 'MST_UNIT_EXPLAIN_DESCRIPTION', $unit_evo_id),
                    'summon'      => $this->getLocalization($row['name'], 'MST_UNIT_EXPLAIN_SUMMON', $unit_evo_id),
                    'evolution'   => $this->getLocalization($row['name'], 'MST_UNIT_EXPLAIN_EVOLUTION', $unit_evo_id),
                    'affinity'    => $this->getLocalization($row['name'], 'MST_UNIT_EXPLAIN_AFFINITY', $unit_evo_id),
                    'fusion'      => $this->getLocalization($row['name'], 'MST_UNIT_EXPLAIN_FUSION', $unit_evo_id),
                ],
                //
                // 'debug' => $row,
            ];

            // $is_enhancer = $unit['job'] == "Enhancer"/* || $row['lb_cost'] == 0*/;

            if (GameFile::getRegion() == 'jp')
                unset($evo_entry['strings']);

            $evo_entry                  = SkillReader::parseFrames($row, $evo_entry);
            $evo_entry['attack_count']  = count($evo_entry['attack_frames'][0]);
            $evo_entry['attack_frames'] = $evo_entry['attack_frames'][0];
            $evo_entry['effect_frames'] = $evo_entry['effect_frames'][0];

            return $evo_entry;
        }

        /**
         * @param $row
         */
        private function readUnitSkillRow($row) {
            $unit_id = (int) $row['unit_id'];
            $unit    = $this->units[$unit_id] ?? [];

            $level  = (int) $row['level'];
            $rarity = (int) max($row['rarity_min'] ?? 10, $unit['rarity_min'] ?? 10); // set rarity to min_rarity instead of 0

            $spells    = array_filter(explode(',', $row['magic_ids']));
            $abilities = array_filter(explode(',', $row['ability_ids']));

            $skills =& $this->units[$unit_id]['skills'];
            if ($skills == null)
                $skills = [];

            foreach ($abilities as $skill_id)
                $skills[$skill_id] = ['rarity' => $rarity, 'level' => $level, 'type' => 'ABILITY', 'id' => (int) $skill_id];

            foreach ($spells as $skill_id)
                $skills[$skill_id] = ['rarity' => $rarity, 'level' => $level, 'type' => 'MAGIC', 'id' => (int) $skill_id];
        }

        /**
         * @param $unit
         *
         * @return mixed
         */
        private function sortSkills($unit) {
            if (empty($unit['skills']))
                return $unit;

            usort($unit['skills'], function ($a, $b) {
                return $a['rarity'] <=> $b['rarity']    // sort by rarity
                    ?: $a['level'] <=> $b['level'];     // sort by level
            });

            return $unit;
        }

        /**
         * @param $row
         */
        private function readUnitAwakeningRow($row) {
            $unit_evo_id = $row['unit_evo_id'];
            $unit_id     = $this->unit_map[$unit_evo_id] ?? null;
            if ($unit_id == null)
                return;

            $unit_evo =& $this->units[$unit_id]['entries'][$unit_evo_id];

            $mats = [];
            foreach (readParameters($row['mats'], ',:') as list($type, $item_id, $count)) {
                assert($type == 20);
                $mats[$item_id] = $count;
            }

            $unit_evo['awakening'] = [
                'gil'       => (int) $row['gil'],
                'materials' => $mats,
            ];
        }

        /**
         * @param string $jp_val
         * @param string $table
         * @param string $id
         *
         * @return array|string
         */
        private function getLocalization($jp_val, $table, $id) {
            if (GameFile::getRegion() == 'gl')
                return Strings::getStrings($table, $id) ?? [];

            return [$jp_val];
        }
    }