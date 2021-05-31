<?php
    /**
     * User: aEnigma
     * Date: 28.12.2017
     * Time: 16:49
     */

    namespace Sol\FFBE\Reader;

    use Sol\FFBE\AiAction;
    use Sol\FFBE\AiParser;
    use Sol\FFBE\AiRow;
    use Sol\FFBE\GameFile;
    use Solaris\FFBE\GameHelper;
    use Solaris\FFBE\Helper\Strings;
    use Solaris\FFBE\Mst\AbilitySkillMst;
    use Solaris\FFBE\Mst\MetaMstList;
    use Solaris\FFBE\Mst\MonsterSkillMst;
    use Solaris\FFBE\Mst\MonsterSkillMstList;
    use Solaris\FFBE\Mst\SkillMstList;
    use Solaris\FFBE\MstKey;
    use Solaris\Formatter\SkillFormatter;

    class MissionResponseReader {
        protected string $region;
        protected bool   $isFake;

        protected MetaMstList  $skill_mst_list;
        protected SkillMstList $monster_skill_list;

        /** @var AiRow[] */
        protected array  $monster_ais              = [];
        protected array  $monster_groups           = [];
        protected array  $monster_parts            = [];
        protected array  $monster_skills           = [];
        protected array  $monster_skillsets        = [];
        protected array  $monster_passives         = [];
        protected array  $monster_passive_skillset = [];
        protected array  $monster_break_info       = [];
        protected array  $monsters                 = [];
        protected string $mission_info;
        protected array  $related_skills;
        protected int    $mission_id;

        /**
         * @param string      $region
         * @param MetaMstList $skills
         * @param bool        $isFake
         */
        public function __construct(string $region, MetaMstList $skills, bool $isFake = false) {
            $this->region         = $region;
            $this->skill_mst_list = new MetaMstList($skills->getClient());
            $this->isFake         = $isFake;

            // register monster skills
            $this->monster_skill_list = new MonsterSkillMstList();
            $this->monster_skill_list->readFile();

            $this->skill_mst_list->addList($this->monster_skill_list);
            $this->skill_mst_list->addList($skills);

            $this->related_skills = [];
        }

        /**
         * @param array $data
         *
         * @throws \Exception
         * @return string
         */
        public function readResponse(array $data): string {
            // mission info
            $this->readMonsterGroups($data);
            $this->readMonsterParts($data['MonsterPartsMst']);
            $this->readMonsterBreakInfo($data['7KCkJd1E'] ?? [], $data['BJ6A0KTt'] ?? []);

            // summary
            $name = $this->readMissionInfo($data);

            // skillsets
            $skillsets = array_column($this->monster_parts, 'skillset_id');
            $skillsets = array_reduce($skillsets, 'array_merge', []);
            $skillsets = array_unique($skillsets);

            $this->readSkillSet(GameFile::loadMst('F_MONSTER_SKILL_SET_MST'), $skillsets);
            $this->readSkillSet($data['MonsterSkillSetMst'] ?? [], $skillsets);

            $skillsets = array_column($this->monster_parts, 'passive_skillset_id');
            $skillsets = array_reduce($skillsets, 'array_merge', []);
            $skillsets = array_unique($skillsets);
            $this->readPassiveSkillSet($data['MonsterPassiveSkillSetMst'] ?? [], $skillsets);

            // skills
            $skill_ids = array_reduce($this->monster_skillsets, 'array_merge', []);
            $this->readSkills(GameFile::loadMst('F_MONSTER_SKILL_MST'), $skill_ids);
            $this->readSkills($data['MonsterSkillMst'] ?? [], $skill_ids);

            $skill_ids = array_reduce($this->monster_passive_skillset, 'array_merge', []);
            $this->readPassiveSkills($data['MonsterPassiveSkillMst'] ?? [], $skill_ids);

            // AI
            $monster_ais = array_column($this->monster_parts, 'ai_id');
            $monster_ais = array_reduce($monster_ais, 'array_merge', []);
            $monster_ais = array_unique($monster_ais);

            $this->readAi(GameFile::loadMst('F_AI_MST'), $monster_ais);

            return $name;
        }

        /**
         * @param string $file
         */
        public function saveMonsterSkills(string $file): void {
            print "Saving Monster Skills\n";

            ksort($this->monster_skills);
            $data = $this->monster_skills;
            $data = array_map(static function ($row) {
                /** @var MonsterSkillMst $mst */
                $mst  = $row['mst'];
                $name = GameFile::getRegion() === 'gl'
                    ? $mst->getName()
                    : $mst->name;

                return [
                    'name'         => $name,
                    'flags'        => array_map('boolval', $row['flags']),
                    // 'skill_type'   => [1 => 'Offensive','Defensive?','Heal / White','Fixed / Unprovokable?','None?'][$mst->skill_type] ?? $mst->skill_type,
                    'attack_type'  => GameHelper::ATTACK_TYPE[$mst->attack_type],
                    'execute_type' => GameHelper::SKILL_EXECUTE_TYPE[$mst->execute_type],
                    'effects'      => $row['effects'],
                    'effects_raw'  => $row['effects_raw'],
                    'strings'      => ['name' => Strings::getStrings('MST_MONSTER_SKILL_NAME', $mst->id)],
                ];
            }, $data);

            // remove GL local for jp
            if (GameFile::getRegion() === 'jp')
                foreach ($data as $k => $e)
                    unset($data[$k]['strings']);

            // json encode
            $data = toJSON($data);
            foreach (['effect_frames', 'attack_frames', 'effects_raw', 'flags'] as $x)
                $data = preg_replace_callback('/(\"(?:' . $x . ')":\s+)([^:}]+?)((?:\s*})?,\s+"[^"]+":)/sm', static function ($match) {
                    $trimmed = preg_replace('~\r?\n\s+~', '', $match[2]);
                    $trimmed = str_replace(',', ', ', $trimmed);

                    return "{$match[1]}{$trimmed}{$match[3]}";
                }, $data);

            file_put_contents($file, $data);
        }

        /**
         * @param string $file
         * @param bool   $showMonsterInfo
         * @param bool   $append
         */
        public function saveOutput(string $file, bool $showMonsterInfo = true, bool $append = false): void {
            // create data
            ob_start();
            $this->printOutput($showMonsterInfo);
            $output = ob_get_clean();

            // create dir
            $dir = dirname($file);
            if (! is_dir($dir) && ! mkdir($dir, 0777, true) && ! is_dir($dir))
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));

            // write data
            file_put_contents($file, $output, $append ? FILE_APPEND : 0);
        }

        public function printRelatedSkills($row): void {
            if (empty($this->related_skills))
                return;

            // get relevant stuff
            $skillset      = $this->getMonsterSkills($row['monster_skill_set_id']) ?? [];
            $monster_id    = "{$row['monster_unit_id']}.{$row['monster_parts_num']}";
            $related_ids   = $this->getMonsterPassives($row['monster_passive_skill_set_id'] ?? -1) ?? [];
            $related_ids[] = $monster_id;

            $related_skills = [];
            foreach ($related_ids as $id) {
                if (empty($this->related_skills[$id]))
                    continue;

                foreach ($this->related_skills[$id] as $skill_id)
                    if (! in_array($skill_id, $skillset, false))
                        $related_skills[] = $skill_id;
            }

            if (empty($related_skills))
                return;

            // output
            echo "###\n";
            echo "# Related Skills\n";
            echo "###\n";

            foreach ($related_skills as $skill_id) {
                /** @var MonsterSkillMst() $mst */
                $mst = $this->skill_mst_list->getEntry($skill_id);
                if ($mst === null)
                    continue;

                $skill = [
                    'id'          => $skill_id,
                    'name'        => $mst->getName(),
                    //
                    'mst'         => $mst,
                    'effects'     => SkillFormatter::formatFlatArray($mst, $this->skill_mst_list),
                    'effects_raw' => SkillFormatter::formatEffectsRaw($mst),
                    //
                    'attack_type' => $mst->attack_type,
                    'flags'       => [false, false, false, false, false],
                ];

                $this->formatSkill($skill_id, $skill);
            }

            echo "\n";
        }

        public function readAllSkills(array $data): void {
            $this->readSkills($data, array_column($data, 'monster_skill_id'));
        }

        /**
         * @return int
         */
        public function getMissionID(): int {
            return $this->mission_id;
        }

        /**
         * @param array $data
         * @param int[] $ids
         */
        protected function readPassiveSkills(array $data, array $ids): void {
            foreach ($data as $row) {
                $id = (int) $row['monster_passive_skill_id'];
                if (isset($this->monster_passives[$id]) || ! in_array($id, $ids))
                    continue;

                $_row = [
                    MstKey::TARGET_RANGE => $row['target_range'],
                    MstKey::TARGET       => $row['target'],
                    MstKey::EFFECT_TYPE  => $row['effect_type'],
                    MstKey::EFFECT_PARAM => $row['effect_param'],
                ];

                $mst              = new AbilitySkillMst();
                $mst->id          = $id;
                $mst->attack_type = 0;
                $mst->elements    = [];
                $mst->effects     = SkillMstList::parseEffects($_row, false);

                $this->monster_passives[$id] = [
                    'id'          => $id,
                    'name'        => Strings::getString('MST_MONSTER_SKILL_NAME', $id) ?? $row['name'],
                    //
                    'mst'         => $mst,
                    'effects'     => SkillFormatter::formatFlatArray($mst, $this->skill_mst_list),
                    'effects_raw' => SkillFormatter::formatEffectsRaw($mst),
                    //
                    'attack_type' => $row['attack_type'] ?? 99,
                    'flags'       => null //explode(',', $row['flags']),
                ];
            }

            foreach ($this->monster_passives as $id => $passive)
                foreach ($passive['effects'] as $k => $effect)
                    if (preg_match("~\((\d+)\)~", $effect, $match))
                        $this->related_skills[$id][] = $match[1];
        }

        /**
         * @param array $data
         *
         * @return string
         */
        protected function readMissionInfo(array $data): string {
            ob_start();

            if (! empty($data['MissionPhaseMst'])) {
                print "##\n";
                $row  = $data['MissionPhaseMst'][0];
                $id   = (int) $row['mission_id'];
                $name = Strings::getString('MST_MISSION_NAME', $id) ?? $row['name'];

                printf("# Mission '%s' (%d)\n", $name, $id);

                if ($row['UnawaresFlg'] === '1')
                    printf("# Enemy has first strike!\n");

                if (! empty($row['battle_script_id']))
                    printf("# BattleScript: {$row['battle_script_id']}\n");

                // if ($row['RerunPermit'] == "0")
                //     printf("# No continue?\n");

            }
            elseif (! empty($data['MissionStartRequest'])) {
                print "##\n";

                $row  = $data['MissionStartRequest'][0];
                $id   = (int) $row['mission_id'];
                $name = Strings::getString('MST_MISSION_NAME', $id) ?? $row['name'] ?? 'UNKNOWN';

                printf("# Mission '%s' (%d)\n", $name, $id);
            }

            elseif ($this->isFake)
                [$id, $name] = [0, 'Mission'];

            else
                throw new \LogicException('No mission data found');

            if (isset($this->mission_id) && $this->mission_id !== $id)
                throw new \LogicException('Cannot combine info on two different missions');

            $this->mission_id = $id;


            if (! empty($this->monster_groups)) {
                print "#\n# Battles\n";

                foreach ($this->monster_groups as $group) {
                    $starting = [];
                    $summoned = [];

                    foreach ($group as $monster) {
                        $id   = $monster['monster_id'];
                        $row  = $this->monster_parts["{$monster['monster_id']}.1"];
                        $name = Strings::getString('MST_MONSTER_NAME', $id) ?? $row['name'];

                        if ($monster['summon_limit'] > 1)
                            $name .= " (max {$monster['summon_limit']})";

                        if ($monster['initial'])
                            $starting[] = $name;

                        else
                            $summoned[] = $name;
                    }

                    print '#  * ' . join(', ', $starting);

                    if (! empty($summoned))
                        print ' [+ ' . join(', ', $summoned) . ']';

                    print "\n";
                }
            }


            $this->mission_info = ob_get_clean();

            if (! empty($this->mission_info))
                $this->mission_info .= "##\n\n";

            return $name ?? '';
        }

        /**
         * @param array $data
         * @param array $ids
         */
        protected function readSkills(array $data, array $ids): void {
            if (empty($data))
                return;

            foreach ($data as $row) {
                $id = $row['monster_skill_id'];
                if (! in_array($id, $ids, false))
                    continue;

                if (isset($this->monster_skills[$id]))
                    continue;

                // skill effect
                $_row = [
                    MstKey::TARGET_RANGE => $row['target_range'],
                    MstKey::TARGET       => $row['target'],
                    MstKey::EFFECT_TYPE  => $row['effect_type'],
                    MstKey::EFFECT_PARAM => $row['effect_param'],
                ];

                $mst               = new MonsterSkillMst();
                $mst->name         = $row['name'];
                $mst->id           = $id;
                $mst->skill_type   = (int) $row['skill_type'];
                $mst->attack_type  = (int) $row['attack_type'];
                $mst->execute_type = (int) $row['execute_type'];
                $mst->elements     = GameHelper::readElement($row['element_inflict']);
                $mst->effects      = SkillMstList::parseEffects($_row, true);

                $skill = [
                    'id'          => $id,
                    'name'        => Strings::getString('MST_MONSTER_SKILL_NAME', $id) ?? $row['name'],
                    //
                    'mst'         => $mst,
                    'effects'     => SkillFormatter::formatFlatArray($mst, $this->skill_mst_list),
                    'effects_raw' => SkillFormatter::formatEffectsRaw($mst),
                    //
                    'attack_type' => $row['attack_type'],
                    'flags'       => explode(',', $row['flags']),
                ];

                if (isset($this->monster_skills[$id]) && $this->monster_skills[$id] !== $skill) {
                    echo json_encode(['new' => $skill['effects'], 'old' => $this->monster_skills[$id]['effects']], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
                    continue;
                }

                // $skill = SkillReader::parseFrames($row, $skill);
                $this->monster_skills[$id] = $skill;

                // find grant skills
                foreach ($mst->effects as $effect)
                    switch ($effect->type) {
                        default:
                            break;

                        case 100:
                            $this->related_skills[$id][] = $effect->parameters[1];
                            break;
                    }
            }
        }

        /**
         * @param array $data
         * @param int[] $ids
         */
        protected function readSkillSet(array $data, array $ids): void {
            if (empty($data))
                return;

            foreach ($data as $row) {
                $id = $row['monster_skill_set_id'];
                if (! in_array($id, $ids, false))
                    continue;

                $skill_ids = readIntArray($row['monster_skill_ids']);
                #                $skill_ids = array_filter($skill_ids);

                $this->monster_skillsets[$id] = $skill_ids;
            }
        }

        /**
         * @param array $data
         * @param array $ids
         */
        protected function readPassiveSkillSet(array $data, array $ids = []): void {
            foreach ($data as $row) {
                $skillset_id = (int) $row['monster_passive_skill_set_id'];

                if (! empty($ids) && ! in_array($skillset_id, $ids, false))
                    continue;

                $skill_ids = readIntArray($row['monster_passive_skill_set_skill_ids']);
                $skill_ids = array_filter($skill_ids);

                $this->monster_passive_skillset[$skillset_id] = $skill_ids;
            }
        }

        /**
         * @param array $monster_parts
         */
        protected function readMonsterParts(array $monster_parts): void {
            foreach ($monster_parts as $row) {
                $monster_id = "{$row['monster_unit_id']}.{$row['monster_parts_num']}";

                // save raw data for lookup
                if (! isset($this->monsters[$monster_id]))
                    $this->monsters[$monster_id] = $row;

                // create monster part entry
                if (isset($this->monster_parts[$monster_id]))
                    $entry = $this->monster_parts[$monster_id];

                else
                    $entry = [
                        'monster_id'          => $monster_id,
                        'name'                => $row['name'],
                        'skillset_id'         => [],
                        'passive_skillset_id' => [],
                        'ai_id'               => [],
                        'break'               => null,
                    ];


                if (! empty($row['monster_skill_set_id']))
                    $entry['skillset_id'][] = (int) $row['monster_skill_set_id'];

                if (! empty($row['monster_passive_skill_set_id']))
                    $entry['passive_skillset_id'][] = (int) $row['monster_passive_skill_set_id'];

                if (! empty($row['ai_id']))
                    $entry['ai_id'][] = (int) $row['ai_id'];

                if (! empty($row['e56NZY42']) && $row['e56NZY42'] !== '0')
                    $entry['break'] = (int) $row['e56NZY42'];

                $this->monster_parts[$monster_id] = $entry;
            }
        }

        /**
         * @param $text
         * @param $keys
         * @param $vals
         *
         * @throws \Exception
         */
        protected function formatResistances($text, $keys, $vals): void {
            $vals        = explode(',', $vals);
            $resistances = GameHelper::array_use_keys($keys, $vals);

            $resistances = array_map(
                static function ($key, $val) {
                    return sprintf('#        %-13s %5d%%', $key, $val);
                },
                array_keys($resistances),
                $resistances
            );

            printf("\n#\n# %s\n%s\n#",
                   $text,
                   join("\n", $resistances)
            );
        }

        /**
         * @param array $data
         * @param int[] $ai_ids
         */
        protected function readAi(array $data, array $ai_ids): void {
            AiParser::$monsters = $this->monster_parts;

            $ais = [];
            foreach ($data as $row) {
                $id = (int) $row['ai_id'];
                if (isset($this->monster_ais[$id]) || ! in_array($id, $ai_ids))
                    continue;

                $row        = AiRow::parseRow($row);
                $ais[$id][] = AiAction::parseRow($row);
            }

            foreach ($ais as $id => $entry)
                $this->monster_ais[$id] = $entry;
        }

        /**
         * @param int   $id
         * @param array $skill
         */
        protected function formatSkill(int $id, array $skill): void {
            $name    = $skill['name'];
            $effects = SkillFormatter::format($skill['mst'], $this->skill_mst_list, "\n#  ", true);

            $attack_type = $skill['attack_type'] === 99
                ? 'Passive'
                : GameHelper::ATTACK_TYPE[$skill['attack_type']];

            echo "#\n";
            echo "#  {$name} ({$id}) [{$attack_type}]\n";
            echo "#\n";
            echo "#  {$effects}\n";
            echo "#\n";

            if ($skill['flags'] !== null) {
                $sealable    = $skill['flags'][0] ? '+' : '-';
                $reflectable = $skill['flags'][1] ? '+' : '-';
                $unk1        = $skill['flags'][2] ? '+' : '-';
                $unk2        = $skill['flags'][3] ? '+' : '-';
                echo "#  Sealable  {$sealable}    Unknown1  {$unk1}\n";
                echo "#  Reflect   {$reflectable}    Unknown2  {$unk2}\n";
                echo "#\n";
            }

            echo "##\n";
        }

        /**
         * @param array $data
         */
        private function readMonsterGroups(array $data): void {
            $groups = [];

            foreach ($data['BattleGroupMst'] ?? [] as $entry) {
                $group_id     = $entry['battle_group_id'];
                $monster_id   = $entry['monster_unit_id'];
                $initial      = $entry['initial_display'] === '1';
                $summon_limit = (int) $entry['call_max'];
                $index        = (int) $entry['order_index'];

                $groups[$group_id][$index - 1] = compact('monster_id', 'initial', 'summon_limit');
            }

            $this->monster_groups = $groups;
        }

        /**
         * @param bool $showMonsterInfo
         *
         * @throws \Exception
         */
        private function printOutput(bool $showMonsterInfo = true): void {
            echo $this->mission_info;

            foreach ($this->monsters as $row) {
                if ($showMonsterInfo)
                    $this->printMonsterInfo($row);

                else
                    print "###\n# {$row['name']} ({$row['monster_unit_id']})\n";

                $ai = $this->printAI($row);
                ob_start();
                $this->printMonsterPassives($row);
                $this->printMonsterSkills($row);
                $this->printRelatedSkills($row);
                $info = ob_get_clean();
                $info = AiParser::insertMonsterNames($info);

                echo $info;
                echo $ai;
            }
        }

        /**
         * @param $row
         *
         * @throws \Exception
         */
        private function printMonsterInfo(array $row): void {
            $id    = (int) $row['monster_unit_id'];
            $ai_id = (int) $row['ai_id'];
            $name  = Strings::getString('MST_MONSTER_NAME', $id) ?? $row['name'];
            $part  = $this->monster_parts["{$row['monster_unit_id']}.{$row['monster_parts_num']}"];

            if (isset($this->monster_parts["{$row['monster_unit_id']}.2"]))
                $name .= ' ' . range('A', 'Z')[$row['monster_parts_num'] - 1];

            $tribes = GameHelper::readIntArray($row['tribe_id']);
            $tribes = array_map(static function ($tribe_id) { return Strings::getString('MST_TRIBE_NAME', $tribe_id); }, $tribes);
            $tribes = join(', ', $tribes);

            print "##\n# Monster Info\n##\n";
            print "#\n";
            printf("# Monster  %s (%s)\n", $name, join(', ', array_unique([$id, $ai_id])));
            printf("# Race     %s\n", $tribes);
            printf("# Level    %s\n", $row['level']);
            printf("# Actions  %s\n", str_replace(',', '-', $row['num_actions']));
            $this->printMonsterStats($row);

            // todo loot
            //
            // "physical_resist": "0",
            // "magical_resist": "0",
            // "special_resist2": "0",
            // "BadStateResistAdd": "80,100",
            // "num_actions": "7,7",
            // "atk_variance": "85,100",
            // "exp": "10000",
            // "gil": "1000",
            // "loot_table": "",
            // "loot_table_rare": "20:210000600:2:100",
            // "loot_table_unique": "",
            // "steal_table": "",
            // "steal_table_rare": "",
            // "steal_gil": "1000",
            // "steal_table_unique": "",
            // "steal_count_limit": "1",
            // "loot_table_unit": "",


            if ($part['break']) {
                $info = $this->monster_break_info[$part['break']];
                print "###\n";
                print "# Break\n";
                print "###\n";

                print "#\n";
                if ($info['bonus'] === 0)
                    printf("# % -12s  % 4.0f\n", 'Hit points', $info['health']);
                else
                    printf("# % -12s  % 4.0f (+%d after first BREAK)\n", 'Hit points', $info['health'], $info['bonus']);

                printf("# % -12s  % 4.0f\n", 'Duration', $info['duration']);

                print "#\n";
                print "# Damage\n";
                foreach ($info['damage'] as $item => $d)
                    printf("# % 12s  % 4.0f\n", $item, $d);

                print "#\n";
                print "###\n";
                print "# Broken Form\n";
                print "###\n";
                $this->printMonsterStats($info['monster']);
            }

            print "###\n\n";
        }

        /**
         * @param array $row
         */
        private function printMonsterPassives(array $row): void {
            if (empty($row['monster_passive_skill_set_id']))
                return;

            print "###\n";
            print "# Passives\n";
            print "###\n";

            $passive_skillset_id = $row['monster_passive_skill_set_id'];
            $skillset            = $this->getMonsterPassives($passive_skillset_id);

            if ($skillset === null)
                echo "# Unknown passive skillset {$passive_skillset_id}!\n##";

            else
                foreach ($skillset as $passive_id)
                    $this->formatSkill($passive_id, $this->monster_passives[$passive_id]);

            print "\n";
        }

        /**
         * @param array $row
         */
        private function printMonsterSkills(array $row): void {
            $skillset = $this->getMonsterSkills($row['monster_skill_set_id']);

            if ($skillset === null) {
                echo "# MISSING SKILLSET {$row['monster_skill_set_id']}\n";
                echo "##\n#\n";
            }

            if (empty($skillset))
                return;

            // output
            echo "###\n";
            echo "# Skills\n";
            echo "###\n";
            foreach ($skillset as $skill_id) {
                $skill = $this->monster_skills[$skill_id] ?? null;
                if ($skill === null)
                    continue;

                $this->formatSkill($skill_id, $skill);
            }

            echo "\n";
        }

        /**
         * @param $row
         *
         * @return string
         */
        private function printAI($row): string {
            // ai
            $ai = $row['ai_id']
                ? ($this->monster_ais[$row['ai_id']] ?? null)
                : [];

            $skillset = $this->getMonsterSkills($row['monster_skill_set_id']);

            $head = "###\n";
            $head .= "# AI\n";
            $head .= "###\n";

            if (empty($ai))
                if ($ai === null)
                    return $head . "# Missing\n##\n\n";
                else
                    return $head . "# None\n##\n\n";

            else
                return $head . AiParser::parseAI($ai, $skillset, $this->monster_skills, $this->monster_parts, $this->isFake);
        }

        /**
         * @param int $passive_skillset_id
         *
         * @return array|null
         */
        private function getMonsterPassives(int $passive_skillset_id): ?array {
            return $passive_skillset_id
                ? $this->monster_passive_skillset[$passive_skillset_id] ?? null
                : [];
        }

        /**
         * @param int $skillset_id
         *
         * @return array|null
         */
        private function getMonsterSkills(int $skillset_id): ?array {
            return $skillset_id == 0
                ? []
                : $this->monster_skillsets[$skillset_id] ?? null;
        }

        /**
         * @param array $row
         */
        private function printMonsterStats(array $row): void {
            if ($row['G5HbL0vM'] ?? 0 < 1)
                printf("#\n# NEW DAMAGE FORMULA! (%d, %d, %d)\n", 5, 25, 185);

            vprintf("#\n#\n# Stats\n#        HP  %15d\n#        MP  %15d\n#        ATK %15d\n#        DEF %15d\n#        MAG %15d\n#        SPR %15d\n#", [
                $row['bonus_hp'] ?? 0,
                $row['bonus_mp'] ?? 0,
                $row['bonus_atk'],
                $row['bonus_def'],
                $row['bonus_mag'],
                $row['bonus_spr'],
            ]);

            $bonus_status_resist = GameHelper::readIntArray($row['BadStateResistAdd']);
            assert($bonus_status_resist[1] == 100);
            $bonus_status_resist = $bonus_status_resist[0];

            $this->formatResistances('Damage resist', ['physical', 'magical'], $row['physical_resist'] . ',' . $row['magical_resist']);
            $this->formatResistances('Element resist', GameHelper::ELEMENT_TYPE, $row['element_resist']);
            $this->formatResistances("Status resist (+{$bonus_status_resist}% / application)", GameHelper::STATUS_TYPE, $row['status_resist']);
            $this->formatResistances('Debuff resist', GameHelper::DEBUFF_TYPE, $row['debuff_resist']);

            $special = readIntArray($row['special_resist']);
            $special = array_combine($special, array_fill(0, count($special), true));
            $special += [1 => false, 2 => false];

            print "\n#\n# Immunity\n";
            foreach ($special as $k => $bool)
                printf("#        %-13s %5s\n", GameHelper::SPECIAL_RESIST[$k] ?? "Unknown ({$k})", $bool ? '+' : '-');
            print "#\n";
        }

        /**
         * @param array $break_info
         * @param array $break_stats
         */
        private function readMonsterBreakInfo(array $break_info, array $break_stats): void {
            if (empty($break_info))
                return;

            // var_dump($break_info);
            foreach ($break_info as $entry) {
                #assert($entry["JX6mCav4"] === "1");          // duration?
                #assert($entry["zW97Bico"] === "1");          // duration?
                #assert($entry["EwZ40mt3"] === "0,0,1,0,0,1,0,0,0,0,1,0,0,0,0,1");

                ['e56NZY42' => $id, 'CX5D2V1j' => $health, '8HnQR9Wx' => $bonus_health, 'RHe5r72C' => $defenses, 'JX6mCav4' => $duration] = $entry;

                $defenses = GameHelper::readIntArray((string) $defenses);
                $defenses = GameHelper::array_use_keys(GameHelper::EQUIPMENT_TYPE, $defenses, +1);
                $damages  = array_map(static fn(int $d) => 10_000.0 / $d, $defenses);

                $this->monster_break_info[$id] = [
                    'health'   => $health,
                    'bonus'    => $bonus_health,
                    'duration' => 1 + $duration,
                    'defenses' => $defenses,
                    'damage'   => $damages,
                    'monster'  => null,
                ];
            }

            // monster stats after break
            foreach ($break_stats as $row)
                $this->monster_break_info[$row['e56NZY42']]['monster'] = $row;
        }
    }