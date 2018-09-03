<?php
    /**
     * User: aEnigma
     * Date: 28.12.2017
     * Time: 16:49
     */

    namespace Sol\FFBE\Reader;

    use Sol\FFBE\AiParser;
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
        /** @var string */
        protected $region;

        /** @var SkillMstList */
        protected $skill_mst_list;

        /** @var bool */
        protected $isFake;

        protected $monster_ais              = [];
        protected $monster_parts            = [];
        protected $monster_skills           = [];
        protected $monster_skillsets        = [];
        protected $monster_passives         = [];
        protected $monster_passive_skillset = [];
        protected $monsters                 = [];
        protected $mission_info;
        protected $related_skills;
        private   $monster_skill_list;

        /**
         * @param string      $region
         * @param MetaMstList $skills
         * @param bool        $isFake
         */
        public function __construct($region, MetaMstList $skills, $isFake = false) {
            $this->region         = $region;
            $this->skill_mst_list = $skills;
            $this->isFake         = $isFake;

            // register monster skills
            $this->monster_skill_list = new MonsterSkillMstList();
            $this->monster_skill_list->readFile();
            $skills->addList($this->monster_skill_list);

            $this->related_skills = [];
        }

        /**
         * @param array $data
         *
         * @throws \Exception
         */
        public function readResponse(array $data) {
            // mission info
            $this->readMissionInfo($data);
            $this->readMonsterParts($data['MonsterPartsMst']);

            // skillsets
            $skillsets = array_column($this->monster_parts, "skillset_id");
            $skillsets = array_reduce($skillsets, "array_merge", []);
            $skillsets = array_unique($skillsets);

            $this->readSkillSet(GameFile::loadMst('F_MONSTER_SKILL_SET_MST'), $skillsets);
            $this->readSkillSet($data['MonsterSkillSetMst'] ?? [], $skillsets);

            $skillsets = array_column($this->monster_parts, "passive_skillset_id");
            $skillsets = array_reduce($skillsets, "array_merge", []);
            $skillsets = array_unique($skillsets);
            $this->readPassiveSkillSet($data['MonsterPassiveSkillSetMst'] ?? [], $skillsets);

            // skills
            $skill_ids = array_reduce($this->monster_skillsets, "array_merge", []);
            $this->readSkills(GameFile::loadMst('F_MONSTER_SKILL_MST'), $skill_ids);
            $this->readSkills($data['MonsterSkillMst'] ?? [], $skill_ids);

            $skill_ids = array_reduce($this->monster_passive_skillset, "array_merge", []);
            $this->readPassiveSkills($data['MonsterPassiveSkillMst'] ?? [], $skill_ids);

            // AI
            $monster_ais = array_column($this->monster_parts, "ai_id");
            $monster_ais = array_reduce($monster_ais, "array_merge", []);
            $monster_ais = array_unique($monster_ais);
            $this->readAi(GameFile::loadMst('F_AI_MST'), $monster_ais);
        }

        /**
         * @param string $file
         */
        public function saveMonsterSkills($file): void {
            print "Saving Monster Skills\n";

            ksort($this->monster_skills);
            $data = $this->monster_skills;
            $data = array_map(function ($row) {
                /** @var MonsterSkillMst $mst */
                $mst = $row['mst'];

                return [
                    'name'         => Strings::getString('MST_MONSTER_SKILL_NAME', $mst->id) ?? $row['name'],
                    'flags'        => array_map("boolval", $row['flags']),
                    'attack_type'  => GameHelper::ATTACK_TYPE[$mst->attack_type],
                    'execute_type' => GameHelper::SKILL_EXECUTE_TYPE[$mst->execute_type],
                    'effects'      => $row['effects'],
                    'effects_raw'  => $row['effects_raw'],
                    'strings'      => [
                        'name' => Strings::getStrings('MST_MONSTER_SKILL_NAME', $mst->id)
                    ]
                ];
            }, $data);
            $data = toJSON($data, false);

            foreach (['effect_frames', 'attack_frames', 'effects_raw', 'flags'] as $x)
                $data = preg_replace_callback('/(\"(?:' . $x . ')":\s+)([^:}]+)(,\s+"[^"]+":)/sm', function ($match) {
                    $trimmed = preg_replace('~\r?\n\s+~', '', $match[2]);
                    $trimmed = str_replace(',', ', ', $trimmed);

                    return $match[1] . $trimmed . $match[3];
                }, $data);
            file_put_contents($file, $data);
        }

        /**
         * @param string $file
         * @param bool   $showMonsterInfo
         *
         * @throws \Exception
         */
        public function saveOutput($file, $showMonsterInfo = true) {
            $output = $this->printOutput($showMonsterInfo);
            file_put_contents($file, $output);

            echo $output;
        }

        public function printRelatedSkills($row) {
            if (empty($this->related_skills))
                return;

            // get relevant stuff
            $skillset      = $this->getMonsterSkills($row['monster_skill_set_id']) ?? [];
            $monster_id    = $row['monster_unit_id'];
            $related_ids   = $this->getMonsterPassives($row['monster_passive_skill_set_id']) ?? [];
            $related_ids[] = $monster_id;

            $related_skills = [];
            foreach ($related_ids as $id) {
                if (empty($this->related_skills[$id]))
                    continue;

                foreach ($this->related_skills[$id] as $skill_id)
                    if (!in_array($skill_id, $skillset))
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
                if ($mst == null)
                    continue;

                $skill = [
                    'id'          => $skill_id,
                    'name'        => $mst->getName(),
                    //
                    'mst'         => $mst,
                    'effects'     => SkillFormatter::format($mst, $this->skill_mst_list),
                    'effects_raw' => SkillFormatter::formatEffectsRaw($mst),
                    //
                    'attack_type' => $mst->attack_type,
                    'flags'       => [false, false, false, false, false],
                ];

                $this->formatSkill($skill_id, $skill);
            }

            echo "\n";
        }

        public function readAllSkills(array $data) {
            $this->readSkills($data, array_column($data, 'monster_skill_id'));
        }

        /**
         * @param array $data
         * @param int[] $ids
         */
        protected function readPassiveSkills(array $data, $ids) {
            foreach ($data as $row) {
                $id = (int) $row['monster_passive_skill_id'];
                if (!in_array($id, $ids))
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
                    'effects'     => SkillFormatter::format($mst, $this->skill_mst_list),
                    'effects_raw' => SkillFormatter::formatEffectsRaw($mst),
                    //
                    'attack_type' => $row['attack_type'] ?? 99,
                    'flags'       => null //explode(',', $row['flags']),
                ];
            }

            foreach ($this->monster_passives as $id => $passive)
                if (preg_match("~\((\d+)\)~", $passive['effects'], $match))
                    $this->related_skills[$id][] = $match[1];
        }

        /**
         * @param array $data
         */
        protected function readMissionInfo(array $data) {
            ob_start();

            if (!empty($data['MissionPhaseMst'])) {
                print "##\n";
                $row  = $data['MissionPhaseMst'][0];
                $id   = (int) $row['mission_id'];
                $name = Strings::getString('MST_MISSION_NAME', $id) ?? $row['name'];

                printf("# Mission '%s' (%d)\n", $name, $id);

                if ($row['UnawaresFlg'] == "1")
                    printf("# Enemy has first strike!\n");

                if (!empty($row['battle_script_id']))
                    printf("# BattleScript: {$row['battle_script_id']}\n");

                // if ($row['RerunPermit'] == "0")
                //     printf("# No continue?\n");

                print "##\n\n";
            } elseif (!empty($data['MissionStartRequest'])) {
                print "##\n";
                $row  = $data['MissionStartRequest'][0];
                $id   = (int) $row['mission_id'];
                $name = Strings::getString('MST_MISSION_NAME', $id) ?? $row['name'];

                printf("# Mission '%s' (%d)\n", $name, $id);
                print "##\n\n";
            }

            $this->mission_info = ob_get_clean();
        }

        /**
         * @param array $data
         * @param array $ids
         */
        protected function readSkills(array $data, array $ids) {
            if (empty($data))
                return;

            foreach ($data as $row) {
                $id = $row['monster_skill_id'];
                if (!in_array($id, $ids))
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
                $mst->id           = $id;
                $mst->attack_type  = (int) $row['attack_type'];
                $mst->execute_type = (int) $row['execute_type'];
                $mst->elements     = GameHelper::readElement($row['element_inflict'], true);
                $mst->effects      = SkillMstList::parseEffects($_row, true);

                $skill = [
                    'id'          => $id,
                    'name'        => Strings::getString('MST_MONSTER_SKILL_NAME', $id) ?? $row['name'],
                    //
                    'mst'         => $mst,
                    'effects'     => SkillFormatter::format($mst, $this->skill_mst_list),
                    'effects_raw' => SkillFormatter::formatEffectsRaw($mst),
                    //
                    'attack_type' => $row['attack_type'],
                    'flags'       => explode(',', $row['flags']),
                ];

                if (isset($this->monster_skills[$id]) && $this->monster_skills[$id] != $skill) {
                    var_dump(['new' => $skill['effects'], 'old' => $this->monster_skills[$id]['effects']]);
                    continue;
                }

                // $skill = SkillReader::parseFrames($row, $skill);
                $this->monster_skills[$id] = $skill;

                // find grant skills
                foreach ($mst->effects as $effect)
                    switch ($effect->type) {
                        case 100:
                            //foreach ($this->getMonstersForSkill($mst->id) as $monster_id)
                            $this->related_skills[$id][] = $effect->parameters[1];
                            break;
                    }
            }
        }

        /**
         * @param array $data
         * @param int[] $ids
         */
        protected function readSkillSet(array $data = [], array $ids) {
            if (empty($data))
                return;

            foreach ($data as $row) {
                $id = $row['monster_skill_set_id'];
                if (!in_array($id, $ids))
                    continue;

                $skill_ids = readIntArray($row['monster_skill_ids']);
                $skill_ids = array_filter($skill_ids);

                $this->monster_skillsets[$id] = $skill_ids;
            }
        }

        /**
         * @param array $data
         */
        protected function readPassiveSkillSet(array $data) {
            foreach ($data as $row) {
                $skillset_id = (int) $row['monster_passive_skill_set_id'];
                $skill_ids   = readIntArray($row['monster_passive_skill_set_skill_ids']);
                $skill_ids   = array_filter($skill_ids);

                $this->monster_passive_skillset[$skillset_id] = $skill_ids;
            }
        }

        /**
         * @param array $data
         */
        protected function readMonsterParts(array $data) {
            foreach ($data as $row) {
                $monster_id = $row['monster_unit_id'];

                if (!isset($this->monsters[$monster_id]))
                    $this->monsters[$monster_id] = $row;

                if (!isset($this->monster_parts[$monster_id]))
                    $this->monster_parts[$monster_id] = [
                        'monster_id'          => $monster_id,
                        'name'                => $row['name'],
                        'skillset_id'         => [],
                        'passive_skillset_id' => [],
                        'ai_id'               => [],
                    ];

                if (!empty($row['monster_skill_set_id']))
                    $this->monster_parts[$monster_id]['skillset_id'][] = $row['monster_skill_set_id'];

                if (!empty($row['passive_skillset_id']))
                    $this->monster_parts[$monster_id]['passive_skillset_id'][] = $row['passive_skillset_id'];

                if (!empty($row['ai_id']))
                    $this->monster_parts[$monster_id]['ai_id'][] = $row['ai_id'];
            }
        }

        /**
         * @param $text
         * @param $keys
         * @param $vals
         *
         * @throws \Exception
         */
        protected function formatResistances($text, $keys, $vals) {
            $vals        = explode(',', $vals);
            $resistances = GameHelper::array_use_keys($keys, $vals);

            $resistances = array_map(
                function ($key, $val) {
                    return sprintf("#        %-13s %5d%%", $key, $val);
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
        protected function readAi(array $data, array $ai_ids) {
            foreach ($data as $row) {
                $id = (int) $row['ai_id'];
                if (!in_array($id, $ai_ids))
                    continue;

                $priority = $row['priority'];
                $weight   = $row['weight'];
                $name     = $row['WhQL5ev9'];

                $conditions = substr($row['conditions'], 0, -1);
                $conditions = explode('@#', $conditions);
                $conditions = array_combine(['states', 'flags'], $conditions);

                $conditions['states'] = explode('@', $conditions['states']);
                $conditions['states'] = array_filter($conditions['states'], function ($val) {
                    return $val != "0:non:non:non";
                });
                $conditions['flags']  = explode('@', $conditions['flags']);
                $conditions['flags']  = array_filter($conditions['flags'], function ($val) {
                    return $val != "non:0";
                });

                $this->monster_ais[$id]['AI']['name']      = $name;
                $this->monster_ais[$id]['AI']['actions'][] = [
                    'priority'       => $priority,
                    'weight'         => $weight,
                    'target'         => $row['AI_SEARCH_COND'],
                    'conditions'     => $conditions,
                    'conditions_str' => $row['conditions'],
                    'action_str'     => $row['action'],
                    "AI_ACT_TARGET"  => $row["AI_ACT_TARGET"],
                    "AI_COND_TARGET" => $row["AI_COND_TARGET"],
                    "AI_COND1"       => $row["AI_COND1"],
                    "AI_PARAM1"      => $row["AI_PARAM1"],
                    "AI_COND2"       => $row["AI_COND2"],
                    "AI_PARAM2"      => $row["AI_PARAM2"],
                ];
            }
        }

        /**
         * @param int   $id
         * @param array $skill
         */
        protected function formatSkill($id, $skill) {
            $name        = $skill['name'];
            $effects     = SkillFormatter::format($skill['mst'], $this->skill_mst_list, "\n#  ", true);
            $attack_type = $skill['attack_type'] == 99
                ? 'Passive'
                : GameHelper::ATTACK_TYPE[$skill['attack_type']];

            echo "#\n";
            echo "#  {$name} ({$id}) [{$attack_type}]\n";
            echo "#\n";
            echo "#  {$effects}\n";
            echo "#\n";

            if ($skill['flags'] != null) {
                $sealable    = $skill['flags'][0] == 1 ? '+' : '-';
                $reflectable = $skill['flags'][1] == 1 ? '+' : '-';
                $unk1        = $skill['flags'][2] == 1 ? '+' : '-';
                $unk2        = $skill['flags'][3] == 1 ? '+' : '-';
                echo "#  Sealable  {$sealable}    Unknown1  {$unk1}\n";
                echo "#  Reflect   {$reflectable}    Unknown2  {$unk2}\n";
                echo "#\n";
            }

            echo "##\n";
        }

        /**
         * @param bool $showMonsterInfo
         *
         * @throws \Exception
         *
         * @return string
         */
        private function printOutput($showMonsterInfo = true) {
            ob_start();

            echo $this->mission_info;

            foreach ($this->monsters as $row) {
                if ($showMonsterInfo)
                    $this->printMonsterInfo($row);

                else
                    print "###\n# {$row['name']}\n";

                $ai = $this->printAI($row);

                $this->printMonsterPassives($row);
                $this->printMonsterSkills($row);
                $this->printRelatedSkills($row);
                echo $ai;
            }

            $output = ob_get_clean();

            return $output;
        }

        /**
         * @param $row
         *
         * @throws \Exception
         */
        private function printMonsterInfo($row): void {
            $id   = (int) $row['monster_unit_id'];
            $name = Strings::getString('MST_MONSTER_NAME', $id) ?? $row['name'];

            $tribes = GameHelper::readIntArray($row['tribe_id']);
            $tribes = array_map(function ($tribe_id) {
                return Strings::getString('MST_TRIBE_NAME', $tribe_id);
            }, $tribes);
            $tribes = join(', ', $tribes);

            print "##\n# Monster Info\n##\n";
            print "#\n";
            printf("# Monster  %s (%d)\n", $name, $id);
            printf("# Race     %s\n", $tribes);
            printf("# Level    %s\n", $row['level']);
            printf("# Actions  %s\n", str_replace(',', '-', $row['num_actions']));
            vprintf("#\n#\n# Stats\n#        HP  %15d\n#        MP  %15d\n#        ATK %15d\n#        DEF %15d\n#        MAG %15d\n#        SPR %15d\n#", [
                $row['bonus_hp'],
                $row['bonus_mp'],
                $row['bonus_atk'],
                $row['bonus_def'],
                $row['bonus_mag'],
                $row['bonus_spr'],
            ]);

            $bonus_status_resist = GameHelper::readIntArray($row['BadStateResistAdd']);
            assert($bonus_status_resist[1] == 100);
            $bonus_status_resist = $bonus_status_resist[0];

            $this->formatResistances("Damage resist", ['physical', 'magical'], $row['physical_resist'] . "," . $row['magical_resist']);
            $this->formatResistances("Element resist", GameHelper::ELEMENT_TYPE, $row['element_resist']);
            $this->formatResistances("Status resist (+{$bonus_status_resist}% / application)", GameHelper::STATUS_TYPE, $row['status_resist']);
            $this->formatResistances("Debuff resist", GameHelper::DEBUFF_TYPE, $row['debuff_resist']);

            $special = readIntArray($row['special_resist']);
            $special = array_combine($special, array_fill(0, count($special), true));
            $special = $special + [1 => false, 2 => false];

            print "\n#\n# Immunity\n";
            foreach ($special as $k => $bool)
                printf("#        %-13s %5s\n", GameHelper::SPECIAL_RESIST[$k] ?? "Unknown ({$k})", $bool ? '+' : '-');
            print "#\n";

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

            print "###\n\n";
        }

        /**
         * @param array $row
         */
        private function printMonsterPassives($row): void {
            if (empty($row['monster_passive_skill_set_id']))
                return;

            print "###\n";
            print "# Passives\n";
            print "###\n";

            $passive_skillset_id = $row['monster_passive_skill_set_id'];
            $skillset            = $this->getMonsterPassives($passive_skillset_id);

            if ($skillset == null)
                echo "# Unknown passive skillset {$passive_skillset_id}!\n##";

            else
                foreach ($skillset as $passive_id)
                    $this->formatSkill($passive_id, $this->monster_passives[$passive_id]);

            print "\n";
        }

        /**
         * @param array $row
         */
        private function printMonsterSkills($row): void {
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
                if ($skill == null)
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
            $ai = $row['ai_id'] == 0
                ? []
                : ($this->monster_ais[$row['ai_id']] ?? null);

            $skillset = $this->getMonsterSkills($row['monster_skill_set_id']);

            $head = "###\n";
            $head .= "# AI\n";
            $head .= "###\n";

            if (empty($ai))
                if ($ai == null)
                    return $head . "# Missing\n##\n\n";
                else
                    return $head . "# None\n##\n\n";

            else
                return $head . AiParser::parseAI($ai, $skillset, $this->monster_skills);
        }

        /**
         * @param int $passive_skillset_id
         *
         * @return array|null
         */
        private function getMonsterPassives($passive_skillset_id) {
            if ($passive_skillset_id == 0)
                return [];

            return $this->monster_passive_skillset[$passive_skillset_id] ?? null;
        }

        /**
         * @param int $skillset_id
         *
         * @return array|null
         */
        private function getMonsterSkills($skillset_id) {
            $skillset = $skillset_id == 0
                ? []
                : $this->monster_skillsets[$skillset_id] ?? null;

            return $skillset;
        }
    }