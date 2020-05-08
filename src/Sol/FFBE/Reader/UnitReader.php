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
    use Solaris\FFBE\Model\Unit;

    class UnitReader extends MstReader {
        /** @var array */
        protected array $units = [];
        /** @var int[] */
        protected array $unit_map;
        /** @var int[] */
        protected array $nv_map;

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
            $this->nv_map   = [];

            echo "\t Units\n";
            foreach (GameFile::loadMst('F_UNIT_MST') as $k => $row)
                $this->readUnitRow($row);

            echo "\t Skills\n";
            foreach (GameFile::loadMst('F_UNIT_SERIES_LV_ACQUIRE_MST') as $row)
                $this->readUnitSkillRow($row);

            $this->units = array_map([$this, 'sortSkills'], $this->units);

            echo "\t Awakenings\n";
            foreach (GameFile::loadMst('F_UNIT_CLASS_UP_MST') as $row)
                $this->readUnitAwakeningRow($row);

            if (GameFile::getRegion() != 'jp')
                return $this->units;

            echo "\t NV awakening\n";
            foreach (GameFile::loadMst('F_NV_EX_CLASS_UP_MST') as $row)
                $this->readUnitNeoAwakeningRow($row);

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
                '~"skills": \[([^]]+)]~',
                static function ($match) {
                    $padding     = str_pad("\n", strpos($match[1], '{'), ' ', STR_PAD_RIGHT);
                    $padding_end = "\n" . substr($padding, 5);

                    $data = preg_replace('~\s+~', ' ', $match[1]);
                    $data = explode('},', $data);
                    $data = array_map(static function ($str) { return trim($str, ' {}'); }, $data);
                    $data = array_map(static function ($str) { return "{{$str}}"; }, $data);
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
        public function readUnitRow(array $row): void {
            $unit_id     = (int) $row['unit_id'];
            $unit_evo_id = (int) $row['unit_evo_id'];
            $nv_id       = (int) ($row['JPW8Vs40'] ?? 0);
            $is_neo      = $nv_id > 0;

            // get unit
            $unit = $this->units[$unit_id] ?? $this->readUnitEntry($row);

            $unit['rarity_max'] = (int) $row['rarity'];
            $unit_evo           = $this->readEvoEntry($row);
            $evo_equip          = readEquip($row['equip']);
            if ($unit['equip'] != $evo_equip)
                // fuck kupipi
                $unit_evo['equip'] = $evo_equip;

            $unit['entries'][$unit_evo_id] = $unit_evo;

            $this->units[$unit_id]        = $unit;
            $this->unit_map[$unit_evo_id] = $unit_id;

            if ($nv_id > 0)
                $this->nv_map[$nv_id][] = $unit_id;
        }

        public function getUnit(int $id) {
            return $this->units[$id] ?? null;
        }

        /**
         * @param array $row
         *
         * @return array
         */
        protected function readUnitEntry(array $row): array {
            $unit_evo_id = (int) $row['unit_evo_id'];
            $nv_id       = (int) ($row['JPW8Vs40'] ?? 0);
            $is_neo      = $nv_id > 0;
            $rarity      = (int) $row['rarity'];
            $names       = $this->getLocalization($row['name'], 'MST_UNIT_NAME', $unit_evo_id);

            $arr = [
                'rarity_min' => $rarity,
                'rarity_max' => $rarity,
                'base_id'    => $is_neo ? (int) $row['JPW8Vs40'] : null,

                'name'  => $names[0] ?? $row['name'],
                'names' => $names,

                'game_id' => (int) $row['game_id'],
                'game'    => null,
                'roles'   => $this->formatRoles($row['27GkYdEu']),
                'job_id'  => (int) $row['job_id'],
                'job'     => null,

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

            if (GameFile::getRegion() == 'jp')
                unset($arr['job'], $arr['game'], $arr['names']);

            else {
                unset($arr['neo_vision'], $arr['base_id']);

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
        protected function readEvoEntry($row): array {
            $unit_evo_id = (int) $row['unit_evo_id'];
            $nv_id       = (int) ($row['JPW8Vs40'] ?? 0);
            $rarity      = (int) $row['rarity'];

            // per evo
            $evo_entry = [
                // 'base_id'       => $nv_id ?: null,
                'compendium_id' => (int) $row['order_index'],
                'rarity'        => $rarity,

                'exp_pattern'  => (int) $row['exp_pattern'],
                'stat_pattern' => (int) $row['stat_pattern'],

                'stats' => [
                    'HP'  => GameHelper::readIntArray($row['hp']),
                    'MP'  => GameHelper::readIntArray($row['mp']),
                    'ATK' => GameHelper::readIntArray($row['atk']),
                    'DEF' => GameHelper::readIntArray($row['def']),
                    'MAG' => GameHelper::readIntArray($row['mag']),
                    'SPR' => GameHelper::readIntArray($row['spr']),
                ],

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
                'nv_upgrade'      => null,
                'brave_shift'     => ($row['rZhG3M4b'] ?? '0') === '0' ? null : (int) $row['rZhG3M4b'],
                //
                'strings'         => null,
            ];

            // $is_enhancer = $unit['job'] == "Enhancer"/* || $row['lb_cost'] == 0*/;

            if (GameFile::getRegion() == 'jp')
                unset($evo_entry['strings']);
            else {
                unset($evo_entry['brave_shift'], $evo_entry['nv_upgrade']);
                $evo_entry['strings'] = [
                    'description' => $this->getLocalization($row['name'], 'MST_UNIT_EXPLAIN_DESCRIPTION', $unit_evo_id),
                    'summon'      => $this->getLocalization($row['name'], 'MST_UNIT_EXPLAIN_SUMMON', $unit_evo_id),
                    'evolution'   => $this->getLocalization($row['name'], 'MST_UNIT_EXPLAIN_EVOLUTION', $unit_evo_id),
                    'affinity'    => $this->getLocalization($row['name'], 'MST_UNIT_EXPLAIN_AFFINITY', $unit_evo_id),
                    'fusion'      => $this->getLocalization($row['name'], 'MST_UNIT_EXPLAIN_FUSION', $unit_evo_id),
                ];
            }

            $evo_entry                  = SkillReader::parseFrames($row, $evo_entry);
            $evo_entry['attack_count']  = count($evo_entry['attack_frames'][0]);
            $evo_entry['attack_frames'] = $evo_entry['attack_frames'][0];
            $evo_entry['effect_frames'] = $evo_entry['effect_frames'][0];

            return $evo_entry;
        }

        /**
         * @param $row
         */
        private function readUnitSkillRow($row): void {
            $unit_id = (int) $row['unit_id'];
            $unit    = $this->units[$unit_id] ?? [];

            $level  = (int) $row['level'];
            $rarity = max((int) $row['rarity_min'], $unit['rarity_min']); // set rarity to min_rarity instead of 0

            $spells    = array_filter(explode(',', $row['magic_ids']));
            $abilities = array_filter(explode(',', $row['ability_ids']));

            $skills =& $this->units[$unit_id]['skills'];
            if ($skills == null)
                $skills = [];

            foreach ($abilities as $skill_id)
                if (! isset($skills[$skill_id]))
                    $skills[$skill_id] = ['rarity' => $rarity, 'level' => $level, 'type' => 'ABILITY', 'id' => (int) $skill_id];

            foreach ($spells as $skill_id)
                if (! isset($skills[$skill_id]))
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

            usort($unit['skills'], static function ($a, $b) {
                return $a['rarity'] <=> $b['rarity']    // sort by rarity
                    ?: $a['level'] <=> $b['level'];     // sort by level
            });

            return $unit;
        }

        /**
         * @param $row
         */
        private function readUnitAwakeningRow(array $row): void {
            $unit_evo_id = $row['unit_evo_id'];
            $unit_id     = $this->unit_map[$unit_evo_id] ?? null;
            if ($unit_id == null)
                return;

            $unit_evo =& $this->units[$unit_id]['entries'][$unit_evo_id];

            $mats = [];
            foreach (readParameters($row['mats'], ',:') as [$type, $item_id, $count]) {
                assert($type == 20);
                $mats[$item_id] = $count;
            }

            $unit_evo['awakening'] = [
                //                'next'      => (int) $row['unit_next_evo_id'],
                'gil'       => (int) $row['gil'],
                'materials' => $mats,
            ];
        }

        private function readUnitNeoAwakeningRow(array $row): void {
            ['JPW8Vs40' => $nv_id, 'f8vk4JrD' => $rank] = $row;
            if ($nv_id == '1')
                return;

            $unit_id  = $this->unit_map[$nv_id];
            $unit_evo =& $this->units[$unit_id]['entries'][$nv_id];

            $mats = [];
            foreach (readParameters($row['mats'], ',:') as [$type, $item_id, $count])
                $mats[$item_id] = $count;

            $unit_evo['nv_upgrade'][$rank - 1] = [
                'gil'       => (int) $row['gil'],
                'materials' => $mats,
                'reward'    => GameHelper::parseItemList($row['reward']),
                'stats'     => GameHelper::array_use_keys(Unit::STAT_NAMES, GameHelper::readIntArray($row['3X2V5mxS'])),
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

        /**
         * @param string $row
         *
         * @return string[]
         */
        private function formatRoles(string $row): array {
            $row = GameHelper::readIntArray($row);
            foreach ($row as $k => $key)
                $row[$k] = GameHelper::UNIT_ROLE[$key] ?? "UNKNOWN ROLE {$key}";

            return $row;
        }
    }