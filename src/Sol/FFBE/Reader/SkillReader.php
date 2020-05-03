<?php
    /**
     * User: aEnigma
     * Date: 03.08.2017
     * Time: 22:51
     */

    namespace Sol\FFBE\Reader;

    use Sol\FFBE\GameFile;
    use Sol\FFBE\MstList\IconMstList;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\GameHelper;
    use Solaris\FFBE\Helper\Environment;
    use Solaris\FFBE\Mst\MstList;
    use Solaris\FFBE\Mst\SkillMst;
    use Solaris\FFBE\Mst\SkillMstList;
    use Solaris\Formatter\SkillFormatter;

    class SkillReader extends MstReader {
        /** @var SkillMstList */
        protected $skill_mst;

        /**
         * @param string  $region
         * @param MstList $skill_mst
         */
        public function __construct($region, MstList $skill_mst = null) {
            GameFile::setRegion($region);
            $this->skill_mst = $skill_mst ?? Environment::getInstance($region);
        }

        /**
         * @param array $data
         * @param array $entry
         *
         * @return mixed
         */
        public static function parseFrames($data, $entry) {
            // attack frames
            if (!empty($data['attack_frames'])) {
                $frames = parseList($data['attack_frames'], '@-:');

                $entry['attack_frames'] = static::flattenFrames($frames, 0);
                $entry['attack_damage'] = static::flattenFrames($frames, 1);
                $entry['attack_count']  = array_map('count', $entry['attack_frames']);
            }
            else {
                $entry['attack_frames'] = [[]];
                $entry['attack_damage'] = [[]];
                $entry['attack_count']  = [0];
            }

            // effect frames
            $frames = parseList($data['effect_frames'], '@&:');
            $frames = static::flattenFrames($frames, 0);
            // $frames   = $data['effect_frames'];
            $entry['effect_frames'] = $frames;

            return $entry;
        }

        /**
         * @param array $effects
         * @param int   $index
         *
         * @return array
         */
        public static function flattenFrames(array $effects, $index = 0): array {
            $frames = [];

            foreach ($effects as $effect) {
                $array = [];

                foreach ($effect as $hit)
                    if (isset($hit[$index]) && $hit[$index] != '')
                        $array[] = (int) $hit[$index];

                $frames[] = $array;
            }

            return $frames;
        }

        /**
         * @param SkillMst $skill
         * @param MstList  $list
         *
         * @return array
         */
        public static function parseEffects(SkillMst $skill, MstList $list): array {
            $effect_str = SkillFormatter::formatEffects($skill, $list);

            $effect_raw = [];
            foreach ($skill->effects as $effect)
                $effect_raw[] = [$effect->target_range, $effect->target_type, $effect->type, $effect->parameters];

            return [$effect_str, $effect_raw];
        }

        /**
         * @return void
         */
        public function parseData() {
            throw new \LogicException();
        }

        /**
         * @param string $file
         */
        public function saveMagic(string $file): void {
            $data = [];
            foreach (GameFile::loadMst('F_MAGIC_MST') as $row) {
                $id    = current($row);
                $entry = $this->parseMagicRow($row);

                $data[$id] = $entry;
            }

            $data = $this->formatOutput($data);

            file_put_contents($file, $data);
        }

        /**
         * @param string $file
         */
        public function saveAbilities(string $file): void {
            $data = [];
            foreach (GameFile::loadMst('F_ABILITY_MST') as $row) {
                $id  = current($row);
                $mst = $this->skill_mst->getEntry($id);
                if ($mst == null || !$mst->isActive())
                    continue;

                $entry = $this->parseAbilityRow($row);

                $data[$id] = $entry;
            }

            $data = $this->formatOutput($data);

            file_put_contents($file, $data);
        }


        /**
         * @param string $file
         */
        public function savePassives(string $file): void {
            $data = [];
            foreach (GameFile::loadMst('F_ABILITY_MST') as $row) {
                $id  = current($row);
                $mst = $this->skill_mst->getEntry($id);
                if ($mst == null || $mst->isActive())
                    continue;

                $id    = current($row);
                $entry = $this->parsePassiveRow($row);

                $data[$id] = $entry;
            }

            $data = $this->formatOutput($data);

            file_put_contents($file, $data);
        }

        /**
         * @param array $row
         *
         * @return array
         */
        protected function parseMagicRow(array $row): array {
            $id   = (int) $row['magic_id'];
            $name = GameFile::getRegion() == 'gl'
                ? Strings::getString('MST_MAGIC_NAME', $id) ?? $row['name']
                : $row['name'];

            $flags    = readIntArray($row['flags']);
            $use_case = explode(',', $row['use_case']);
            assert($use_case[0] == 1);

            $entry = [
                'name'            => $name,
                'icon'            => IconMstList::getFilename($row['icon_id']),
                'compendium_id'   => (int) $row['order_index'],
                'rarity'          => (int) $row['rarity'],
                'cost'            => (object) [],
                // mag spec
                'magic_type'      => GameHelper::MAGIC_TYPE[$row['magic_type'] ?? 0],

                // flags
                'is_sealable'     => (bool) $flags[0],
                'is_reflectable'  => (bool) $flags[1],
                'in_exploration'  => (bool) $use_case[1],

                // effect
                'attack_count'    => 0,
                'attack_damage'   => [],
                'attack_frames'   => [],
                'effect_frames'   => [],

                //
                'move_type'       => (int) $row['move_type'],
                'effect_type'     => GameHelper::SKILL_EXECUTE_TYPE[$row['execute_type']],
                'attack_type'     => GameHelper::ATTACK_TYPE[$row['attack_type'] ?? 0],
                'element_inflict' => GameHelper::readElement($row['element_inflict']) ?: null,

                'effects'      => [],
                'effects_raw'  => '',
                //
                'requirements' => SkillMstList::readRequirements($row),
            ];

            $entry = static::parseFrames($row, $entry);
            $entry = $this->parseSkillCosts($row, $entry);
            $entry = $this->parseSkillEffects($id, $entry);

            return $entry;
        }

        /**
         * @param array $row
         *
         * @return array
         */
        protected function parseAbilityRow(array $row): array {
            $id   = (int) $row['ability_id'];
            $name = GameFile::getRegion() == 'gl'
                ? Strings::getString('MST_ABILITY_NAME', $id) ?? $row['name']
                : $row['name'];

            $entry = [
                'name'             => $name,
                'icon'             => IconMstList::getFilename($row['icon_id']),
                'compendium_id'    => (int) $row['order_index'],
                'rarity'           => (int) $row['rarity'],
                'cost'             => (object) [],

                // effect
                'attack_count'     => 0,
                'attack_damage'    => [],
                'attack_frames'    => [],
                'effect_frames'    => [],
                //
                'move_type'        => (int) $row['move_type'],
                'motion_type'      => (int) $row['MotioinType'],
                'effect_type'      => GameHelper::SKILL_EXECUTE_TYPE[$row['execute_type']],
                'attack_type'      => GameHelper::ATTACK_TYPE[$row['attack_type'] ?? 0],
                'element_inflict'  => GameHelper::readElement($row['element_inflict']) ?: null,
                //
                'effects'          => [],
                'effects_raw'      => '',
                //
                'requirements'     => SkillMstList::readRequirements($row),
                'unit_restriction' => $row['unit_restriction'] == '' ? null : readIntArray($row['unit_restriction']),
            ];


            $entry = static::parseFrames($row, $entry);
            $entry = $this->parseSkillCosts($row, $entry);
            $entry = $this->parseSkillEffects($id, $entry);

            return $entry;
        }

        /**
         * @param $row
         *
         * @return array
         */
        protected function parsePassiveRow($row): array {
            $id   = (int) $row['ability_id'];
            $name = GameFile::getRegion() == 'gl'
                ? Strings::getString('MST_ABILITY_NAME', $id) ?? $row['name']
                : $row['name'];

            $entry = [
                'name'          => $name,
                'icon'          => IconMstList::getFilename($row['icon_id']),
                'compendium_id' => (int) $row['order_index'],
                'rarity'        => (int) $row['rarity'],
                'unique'        => ($row['PermitLap'] == 0),

                'effect_type'      => GameHelper::SKILL_EXECUTE_TYPE[$row['execute_type']],
                'attack_type'      => GameHelper::ATTACK_TYPE[$row['attack_type'] ?? 0],
                'element_inflict'  => GameHelper::readElement($row['element_inflict']) ?: null,

                //
                'effects'          => [],
                'effects_raw'      => '',
                //
                'requirements'     => SkillMstList::readRequirements($row),
                'unit_restriction' => $row['unit_restriction'] == '' ? null : readIntArray($row['unit_restriction']),
            ];

            $entry = $this->parseSkillEffects($id, $entry);

            return $entry;
        }

        /**
         * @param       $id
         * @param array $entry
         *
         * @return array
         */
        protected function parseSkillEffects($id, array $entry): array {
            $skill = $this->skill_mst->getEntry($id);
            if ($skill == null)
                throw new \LogicException("No skill entry {$id} found?");

            $effects              = self::parseEffects($skill, $this->skill_mst);
            $entry['effects']     = $effects[0];
            $entry['effects_raw'] = array_map('array_values', $effects[1]);

            return $entry;
        }

        /**
         * @param array $row
         * @param array $entry
         *
         * @return array
         */
        private function parseSkillCosts(array $row, array $entry): array {
            $costs = $entry['cost'];

            if ($row['mp_cost'] > 0)
                $costs->MP = (int) $row['mp_cost'];

            if ($row['skill_cost'] != '') {
                [$type, $cost] = explode(':', $row['skill_cost']);

                switch ($type) {
                    case 1:
                        $costs->EP = (int) $cost;
                        break;

                    case 2:
                        $costs->LB = $cost / 100;
                        break;
                }
            }

            return $entry;
        }
    }