<?php /** @noinspection AutoloadingIssuesInspection */

    /**
     * User: aEnigma
     * Date: 25.02.2017
     * Time: 15:14
     */

    use Pimple\Container;
    use Sol\FFBE\GameFile;
    use Sol\FFBE\Reader\MstReader;
    use Sol\FFBE\Reader\SkillReader;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\GameHelper;
    use Solaris\FFBE\Mst\BeastBoardPieceMst;
    use Solaris\FFBE\Mst\LimitBurstMst;
    use Solaris\FFBE\Mst\MetaMstList;
    use Solaris\FFBE\Mst\MstList;
    use Solaris\FFBE\Mst\SkillMstList;
    use Solaris\FFBE\MstKey;
    use Solaris\Formatter\SkillFormatter;

    require_once dirname(__DIR__) . '/bootstrap.php';
    require_once dirname(__DIR__) . '/helpers.php';

    class BeastReader extends MstReader {
        /** @var SkillMstList|MetaMstList */
        private $skills;

        /** @var array */
        private array $boards = [];

        /** @var array */
        private array $stat_patterns = [];
        /** @var array */
        private array $exp_patterns = [];

        /**
         * @param MstList $skills
         */
        public function __construct(MstList $skills) { $this->skills = $skills; }

        public function saveBoards(string $path): void {
            file_put_contents($path, $this->formatOutput($this->boards));
        }

        public function saveExpPatterns(string $path): void {
            file_put_contents($path, $this->formatOutput($this->exp_patterns));
        }

        public function saveStatPatterns(string $path): void {
            file_put_contents($path, $this->formatOutput($this->stat_patterns));
        }


        protected function parseData():array {
            $this->entries = [];
            $this->parseBeasts();
            $this->parseBeastStats();
            $this->parseBeastSkills();
            $this->parseBeastColors();
            $this->parseBeastGrowth();
            $this->parseBeastBoards();

            return $this->entries;
        }

        private function parseBeasts(): void {
            foreach (GameFile::loadMst('F_BEAST_MST') as $row) {
                $beast_id = (int) $row['beast_id'];

                $names = GameFile::getRegion() === 'gl'
                    ? Strings::getStrings('MST_BEAST_NAME', $beast_id)
                    : [$row['name']];

                $this->entries[$beast_id] = [
                    'names'   => $names,
                    'image'   => $row['beast_art'],
                    'icon'    => $row['beast_icon'],
                    'skill'   => [],
                    'color'   => null,
                    'entries' => [],
                ];
            }
        }

        private function parseBeastStats(): void {
            foreach (GameFile::loadMst('F_BEAST_STATUS_MST') as $row) {
                $beast_id = (int) $row['beast_id'];
                $entry    = [
                    'stats'          => formatStats($row),
                    'element_resist' => readIntArray($row['element_resist']),
                    'status_resist'  => readIntArray($row['status_resist']),
                    'exp_pattern'    => (int) $row['exp_pattern'],
                    'stat_pattern'   => (int) $row['stat_pattern'],
                    'cp_pattern'     => [],
                ];

                $this->entries[$beast_id]['entries'][$row['rarity'] - 1] = $entry;

                assert($row['magical_resist'] == 0);
                assert($row['physical_resist'] == 0);
                assert($row['special_resist'] == '');
                assert($row['5hqFc4ey'] === "{$beast_id}{$row['rarity']}");
            }
        }

        private function parseBeastSkills(): void {
            foreach (GameFile::loadMst('F_BEAST_SKILL_MST') as $row) {
                $skill_id = (int) $row['beast_skill_id'];
                $beast_id = (int) substr($skill_id, 1, 2);

                $name = GameFile::getRegion() === 'gl'
                    ? Strings::getStrings('MST_BEASTSKILL_NAME', $skill_id)
                    : $row['name'];

                $desc = GameFile::getRegion() === 'gl'
                    ? Strings::getStrings('MST_BEASTSKILL_DESCRIPTION', $skill_id)
                    : $row['desc'];

                $entry = [
                    // effect
                    'effects'       => null,
                    'effects_raw'   => null,

                    // frames
                    'attack_count'  => 0,
                    'attack_damage' => [],
                    'attack_frames' => [],
                    'effect_frames' => [],

                    'strings' => compact('name', 'desc'),
                ];

                $_row = [
                    MstKey::TARGET_RANGE => $row['target_range'],
                    MstKey::TARGET       => $row['target'],
                    MstKey::EFFECT_TYPE  => $row['effect_type'],
                    MstKey::EFFECT_PARAM => $row['effect_param'],
                ];

                $skill              = new LimitBurstMst();
                $skill->id          = $skill_id;
                $skill->attack_type = $row['attack_type'];
                $skill->elements    = GameHelper::readElement($row['element_inflict'], true);
                $skill->effects     = SkillMstList::parseEffects($_row, true);

                $entry['effects']     = SkillFormatter::formatEffects($skill, $this->skills);
                $entry['effects_raw'] = SkillFormatter::formatEffectsRaw($skill);

                $entry = SkillReader::parseFrames($row, $entry);

                $this->entries[$beast_id]['skill'][$skill_id] = $entry;
            }
        }

        private function parseBeastColors(): void {
            foreach (GameFile::loadMst('F_ITEM_EXT_BEAST_MST') as $row) {
                if (! isset($row['item_id']))
                    continue;

                $bonus = $row['beast_exp_bonus'];
                if ($bonus === '')
                    continue;

                $name  = Strings::getString('MST_ITEM_NAME', $row['item_id']);
                $color = explode(' ', $name, 2)[0];
                $bonus = readParameters($bonus, ':,');

                foreach ($bonus as [$beast_id, $factor])
                    $this->entries[$beast_id]['color'][$color] = $factor / 100;
            }
        }

        private function parseBeastGrowth(): void {
            foreach (GameFile::loadMst('F_BEAST_CP_MST') as $row) {
                $beast_evo_id = (int) $row['5hqFc4ey'];
                [$beast_id, $rarity] = [substr($beast_evo_id, 0, -1), substr($beast_evo_id, -1)];
                $values = rtrim($row['hbm8t3uK'], ',');
                $values = GameHelper::readIntArray($values);

                $this->entries[$beast_id]['entries'][$rarity - 1]['cp_pattern'] = $values;
            }

            $this->stat_patterns = [];
            foreach (GameFile::loadMst('F_BEAST_GROW_MST') as $row) {
                $pattern   = $row['stat_pattern'];
                $max_level = $row['max_level'];
                $level     = $row['level'];

                $this->stat_patterns[$pattern][$max_level][$level - 1] = (int) $row['UjKF93ok'];
            }

            $this->exp_patterns = [];
            foreach (GameFile::loadMst('F_BEAST_EXP_PATTERN_MST') as $row) {
                $pattern = $row['exp_pattern'];
                $level   = $row['level'];

                $this->exp_patterns[$pattern][$level - 1] = (int) $row['93fnYUJG'];
            }
        }

        private function parseBeastBoards(): void {
            // build parent map
            $parent_map = [];
            foreach (GameFile::loadMst('F_BEAST_BOARD_PIECE_MST') as $row) {
                $node_id = (int) $row['beast_board_piece_id'];

                $child_nodes = readIntArray($row['beast_board_piece_childnodes']);
                foreach ($child_nodes as $child_node_id)
                    $parent_map[$child_node_id] = $node_id;
            }

            // fill data
            foreach (GameFile::loadMst('F_BEAST_BOARD_PIECE_MST') as $row) {
                $node_id  = (int) $row['beast_board_piece_id'];
                $beast_id = (int) $row['beast_id'];

                if (!isset($this->entries[$beast_id]))
                    continue;

                $reward = null;
                if ($row['reward_type'] != 0) {
                    if (!isset(BeastBoardPieceMst::REWARD_TYPE[$row['reward_type']]))
                        throw new RuntimeException("Unknown board reward type {$row['reward_type']} ({$row['reward_param']})");

                    $reward = [BeastBoardPieceMst::REWARD_TYPE[$row['reward_type']], (int) $row['reward_param']];
                }

                $entry = [
                    'parent_node_id' => $parent_map[$node_id] ?? null,
                    'reward'         => $reward,
                    'position'       => readIntArray(str_replace(':', ',', $row['board_piece_pos'])),
                    'cost'           => (int) $row['point'],
                ];

                assert(count($this->boards[$beast_id]) === (int) $row['index']);
                $this->boards[$beast_id][$node_id] = $entry;
            }

            assert(count(GameFile::loadMst('BeastBoardPieceExtMstList')) === 1);
        }
    }

    /** @var Container $container */
    /** @var string $region */

    $reader = new BeastReader($container[SkillMstList::class]);
    $reader->save(DATA_OUTPUT_DIR . "/{$region}/summons.json");
    $reader->saveBoards(DATA_OUTPUT_DIR . "/{$region}/summons_boards.json");
    $reader->saveExpPatterns(DATA_OUTPUT_DIR . "/{$region}/summons_exp_patterns.json");
    $reader->saveStatPatterns(DATA_OUTPUT_DIR . "/{$region}/summons_stat_patterns.json");
