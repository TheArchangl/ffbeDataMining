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
                $entry['attack_count']  = array_map("count", $entry['attack_frames']);
            } else {
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
        static function flattenFrames(array $effects, $index = 0) {
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
        public static function parseEffects(SkillMst $skill, MstList $list) {
            $effect_str = SkillFormatter::formatEffects($skill, $list);

            $effect_raw = [];
            foreach ($skill->effects as $effect)
                $effect_raw[] = [$effect->target_range, $effect->target_type, $effect->type, $effect->parameters];

            return [$effect_str, $effect_raw];
        }

        /** @var SkillMstList */
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

            foreach (GameFile::loadMst('F_ABILITY_MST') as $row)
                $this->parseAbilityRow($row);

            foreach (GameFile::loadMst('F_MAGIC_MST') as $row)
                $this->parseMagicRow($row);

            ksort($this->entries);

            return $this->entries;
        }

        /**
         * @param $row
         *
         */
        protected function parseMagicRow($row) {
            $id = (int) $row['magic_id'];
            if (isset($entries[$id]))
                print "WARNING: {$id} already exists\n";

            $use_case = explode(",", $row['use_case']);
            assert($use_case[0] == 1);

            $flags = readIntArray($row['flags']);
            $entry = [
                'name'          => $row['name'] ?? null,
                'compendium_id' => (int) $row['order_index'],

                'type'   => 'MAGIC',
                'active' => true,

                'usable_in_exploration' => $use_case[1] == 1,

                'rarity'          => (int) $row['rarity'],
                'magic_type'      => GameHelper::MAGIC_TYPE[$row['magic_type'] ?? 0],
                //
                //                'mp_cost'         => (int) $row['mp_cost'],
                'cost'            => (object) [],

                // flags
                'is_sealable'     => (bool) $flags[0],
                'is_reflectable'  => (bool) $flags[1],

                // effect
                'attack_count'    => 0,
                'attack_damage'   => [],
                'attack_frames'   => [],
                'effect_frames'   => [],
                //
                'move_type'       => (int) $row['move_type'],
                // 'wait'   => $row['wait'],
                //
                'effect_type'     => GameHelper::SKILL_EXECUTE_TYPE[$row['execute_type']],
                'attack_type'     => GameHelper::ATTACK_TYPE[$row['attack_type'] ?? 0],
                'element_inflict' => GameHelper::readElement($row['element_inflict']) ?: null,

                'effects'      => [],
                'effects_raw'  => "",
                //
                'requirements' => SkillMstList::readRequirements($row['equip_requirements']),
                'icon'         => IconMstList::getFilename($row['icon_id']),
                //
                'strings'      => [],
            ];

            if (GameFile::getRegion() == 'gl') {
                $names = Strings::getStrings('MST_MAGIC_NAME', $id) ?? [];
                ksort($names);

                $entry['name']    = $names[0] ?? $entry['name'];
                $entry['strings'] = [
                    'name'       => $names,
                    'desc_short' => Strings::getStrings('MST_MAGIC_SHORTDESCRIPTION', $id),
                    'desc_long'  => Strings::getStrings('MST_MAGIC_LONGDESCRIPTION', $id),
                ];
            }

            if (GameFile::getRegion() == 'jp') {
                $entry['strings'] = [];
            }

            $entry = static::parseFrames($row, $entry);
            $entry = $this->parseSkillCosts($row, $entry);
            $entry = $this->parseSkillEffects($id, $entry);

            $this->entries[$id] = $entry;
        }

        /**
         * @param $row
         */
        protected function parseAbilityRow($row) {
            $id = (int) $row['ability_id'];
            if (isset($entries[$id]))
                print "WARNING: $id already exists\n";

            $mst      = $this->skill_mst->getEntry($id);
            $isActive = $mst->isActive();
            $entry    = [
                'name'          => $row['name'] ?? null,
                'compendium_id' => (int) $row['order_index'],

                'type'   => 'ABILITY',
                'active' => $isActive,
                'unique' => ($row['PermitLap'] == 0),

                'rarity'           => (int) $row['rarity'],
                //                'mp_cost'          => (int) $row['mp_cost'],
                'cost'             => (object) [],

                // effect
                'attack_count'     => 0,
                'attack_damage'    => [],
                'attack_frames'    => [],
                'effect_frames'    => [],
                //
                'move_type'        => (int) $row['move_type'],
                'motion_type'      => (int) $row['MotioinType'],
                // 'wait'   => $row['wait'],
                //
                'effect_type'      => GameHelper::SKILL_EXECUTE_TYPE[$row['execute_type']],
                'attack_type'      => GameHelper::ATTACK_TYPE[$row['attack_type'] ?? 0],
                'element_inflict'  => GameHelper::readElement($row['element_inflict']) ?: null,
                //
                'effects'          => [],
                'effects_raw'      => "",
                //
                'requirements'     => SkillMstList::readRequirements($row['equip_requirements']),
                'unit_restriction' => $row['unit_restriction'] == '' ? null : readIntArray($row['unit_restriction']),
                'icon'             => IconMstList::getFilename($row['icon_id']),
                //
                'strings'          => []
            ];

            if (GameFile::getRegion() == 'gl') {
                $names = Strings::getStrings('MST_ABILITY_NAME', $id) ?? [];
                ksort($names);

                $entry['name']    = $names[0] ?? $entry['name'];
                $entry['strings'] = [
                    'name'       => $names,
                    'desc_short' => Strings::getStrings('MST_ABILITY_SHORTDESCRIPTION', $id),
                    'desc_long'  => Strings::getStrings('MST_ABILITY_LONGDESCRIPTION', $id),
                ];
            }

            $entry = static::parseFrames($row, $entry);
            $entry = static::parseSkillCosts($row, $entry);
            $entry = $this->parseSkillEffects($id, $entry);

            $this->entries[$id] = $entry;
        }

        /**
         * @param       $id
         * @param array $entry
         *
         * @return array
         */
        protected function parseSkillEffects($id, array $entry) {
            $skill = $this->skill_mst->getEntry($id);
            if ($skill == null)
                throw new \LogicException("No skill entry {$id} found?");

            $effects              = $this->parseEffects($skill, $this->skill_mst);
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
        private function parseSkillCosts(array $row, array $entry) {
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