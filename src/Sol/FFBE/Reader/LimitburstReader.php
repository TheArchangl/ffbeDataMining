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
    use Solaris\FFBE\Helper\Environment;
    use Solaris\FFBE\Mst\LimitBurstMst;
    use Solaris\FFBE\Mst\MstList;
    use Solaris\FFBE\Mst\SkillMstList;
    use Solaris\FFBE\MstKey;
    use Solaris\Formatter\SkillFormatter;

    class LimitburstReader extends MstReader {
        /** @var MstList */
        protected $skill_mst;
        /** @var array */
        protected $lb_map;

        /**
         * @param string  $region
         * @param MstList $skill_mst
         */
        public function __construct($region, MstList $skill_mst = null) {
            GameFile::setRegion($region);
            $this->skill_mst = $skill_mst ?? Environment::getInstance($region);
        }

        /**
         * @return array
         */
        public function parseData() {
            $this->entries = [];
            $this->lb_map  = [];

            foreach (GameFile::loadMst('F_LIMITBURST_MST') as $row)
                $this->readLimitburstRow($row);

            foreach (GameFile::loadMst('F_LIMITBURST_LV_MST') as $row)
                $this->readLimitburstLevelRow($row);

            foreach ($this->entries as $id => $entry) {
                assert(max(array_keys($entry['levels'])) == count($entry['levels']));
                ksort($entry['levels']);
                $this->entries[$id]['levels'] = array_values($entry['levels']);
            }

            return $this->entries;
        }

        /**
         * @param array $row
         */
        public function readLimitburstRow(array $row) {
            $lb_id = (int) $row['lb_id'];
            $names = GameFile::getRegion() == 'jp'
                ? []
                : Strings::getStrings('MST_LIMITBURST_NAME', $lb_id);

            $entry = [
                'name'            => $names[0] ?? $row['name'] ?? null,
                'cost'            => 0,

                // frames
                'attack_count'    => 0,
                'attack_damage'   => [],
                'attack_frames'   => [],
                'effect_frames'   => [],

                //
               // 'effect_type'     => GameHelper::SKILL_EXECUTE_TYPE[$row['execute_type']],
                'move_type'       => (int) $row['move_type'],
                //
                'damage_type'     => GameHelper::ATTACK_TYPE[$row['attack_type']],
                'element_inflict' => GameHelper::readElement($row['element_inflict']) ?: null,
                //
                'min_level'       => [],
                'max_level'       => [],
                'levels'          => [],

                //
                'strings'         => [
                    'name' => $names,
                    'desc' => Strings::getStrings('MST_LIMITBURST_DESCRIPTION', $lb_id),
                ],
            ];

            if (GameFile::getRegion() == 'jp')
                $entry['strings'] = [];

            $entry = SkillReader::parseFrames($row, $entry);

            $this->entries[$lb_id] = $entry;
            $this->lb_map[$lb_id]  = $row;
        }

        /**
         * @param array $row
         */
        protected function readLimitburstLevelRow(array $row) {
            // if ($row['level'] != 1 && $row['level'] != 15 && $row['level'] != 20 && $row['level'] != 25)
            //     continue;

            $lb_id  = (int) $row['lb_id'];
            $lb_row = $this->lb_map[$lb_id] ?? null;
            if ($lb_row == null)
                return;

            $entry =& $this->entries[$lb_id];
            $level = (int) $row['level'];
            $cost  = $row['cost'] / 100;

            $combined_row = [
                MstKey::TARGET_RANGE => $lb_row['target_range'],
                MstKey::TARGET       => $lb_row['target'],
                MstKey::EFFECT_TYPE  => $lb_row['effect_type'],
                MstKey::EFFECT_PARAM => $row['effect_param'],
            ];

            $skill               = new LimitBurstMst();
            $skill->id           = $lb_id;
            $skill->attack_type  = $lb_row['attack_type'];
            $skill->execute_type = $lb_row['execute_type'];
            $skill->elements     = GameHelper::readElement($lb_row['element_inflict'], true);
            $skill->effects      = SkillMstList::parseEffects($combined_row, true);

            $entry['levels'][$level] = [$cost, SkillFormatter::formatEffectsRaw($skill)];


            if (empty($entry['min_level']))
                $entry['min_level'] = SkillFormatter::formatEffects($skill, $this->skill_mst);

            else
                $entry['max_level'] = SkillFormatter::formatEffects($skill, $this->skill_mst);

        }
    }