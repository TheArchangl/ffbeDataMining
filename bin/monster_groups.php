<?php
    /**
     * User: aEnigma
     * Date: 14.06.2017
     * Time: 17:15
     */

    use Sol\FFBE\GameFile;
    use Solaris\FFBE\Helper\Strings;

    require_once "../bootstrap.php";
    require_once "../../ffbe-discord/tmp/request_helper.php";
    require_once "../../ffbe-discord/tmp/init_strings.php";

    // setup
    $region     = 'jp';
    $mission_id = "876010*";

    // JP workaround
    if ($region == 'jp') {
        GameFile::setRegion($region);

        foreach (GameFile::loadMst('MissionMstList') as $row)
            Strings::setString("MST_MISSION_NAME", $row['mission_id'], $row['name']);


        foreach (GameFile::loadMst('ItemMstList') as $k => $row)
            Strings::setString("MST_ITEM_NAME", $row['item_id'], $row['name']);
    }

    // get data
    $files = glob(CLIENT_DIR . "missions\\{$region}\\*\\{$mission_id}\\*.json", GLOB_BRACE);
    natsort($files);

    $reader = new MonsterGroupReader();
    $reader->readFiles($files);
    $reader->printOutput();

    class MonsterGroupReader {
        // input
        protected $monsters      = [];
        protected $missions      = [];
        protected $battle_groups = [];
        protected $mission_runs  = [];
        protected $waves         = [];

        public function readFiles(array $files) {

            $last_mission = null;
            foreach ($files as $file) {
                $data = file_get_contents($file);
                $data = json_decode($data, true);
                if (!is_array($data)) {
                    var_dump(["error", $file]);
                    continue;
                }

                $data = replaceRequestKeys($data);
                $data = $data['body']['data'] ?? null;

                if ($data == null)
                    continue;

                $mission_id = $data['MissionStartRequest'][0]['mission_id'] ?? null;
                if ($mission_id == null)
                    continue;

                // ScenarioBattleGroupMst
                // BattleGroupMst
                // EncountInfo?

                @$this->mission_runs[$mission_id]++;

                // read groups
                /** @var Monster[][] $groups */
                $groups = [];
                foreach ($data['BattleGroupMst'] as $entry) {
                    $group_id            = $entry['battle_group_id'];
                    $groups[$group_id][] = $entry['monster_unit_id'];
                }


                foreach ($groups as $group_id => $monster_ids) {
                    if (isset($this->battle_groups[$group_id])) {
                        if ($this->battle_groups[$group_id] != $monster_ids) {
                            var_dump($this->battle_groups[$group_id], $monster_ids);
                            die();
                        }
                    } else {
                        $this->battle_groups[$group_id] = $monster_ids;
                    }
                }

                // read loot
                foreach ($data['MonsterPartsMst'] as $entry) {
                    $id   = $entry['monster_unit_id'];
                    $part = $entry['monster_parts_num'];

                    $entry = [
                        'name'         => Strings::getString('MST_MONSTER_NAME', $id) ?? $entry['name'],
                        'normal'       => $this->parseTable($entry["loot_table"]),
                        'unique'       => $this->parseTable($entry["loot_table_unique"]),
                        'rare'         => $this->parseTable($entry["loot_table_rare"]),
                        'steal_normal' => $this->parseTable($entry["steal_table"]),
                        'steal_unique' => $this->parseTable($entry["steal_table_unique"]),
                        'steal_rare'   => $this->parseTable($entry["steal_table_rare"]),
                        'steal_gil'    => $entry["steal_gil"],
                        'drop_gil'     => $entry["gil"],
                    ];

                    if (isset($this->monsters[$id][$part]) && $this->monsters[$id][$part] != $entry) {
                        var_dump(['old' => $this->monsters[$id][$part], 'new' => $entry]);
                        die();
                    }

                    $this->monsters[$id][$part] = $entry;
                }

                // wavebattle
                foreach ($data['MissionPhaseMst'] ?? [] as $phase) {
                    $battle_group_id = $phase['battle_group_id'];
                    if ($battle_group_id == '')
                        continue;

                    $wave_num = $phase['wave_num'];

                    //if (!array_search($battle_group_id, $this->missions[$mission_id][$wave_num]))
                    @$this->missions[$mission_id][$wave_num][$battle_group_id]++;

                    if (isset($this->waves[$wave_num]))
                        assert($this->waves[$wave_num] == $phase['weight']);
                    else
                        $this->waves[$wave_num] = (int) $phase['weight'];
                }

                // exploration
                foreach ($data['EncountInfo'] ?? [] as $entry) {
                    $fight_id        = "{$entry['EncountFieldID']}.{$entry['EncountNum']}";
                    $battle_group_id = $entry['battle_group_id'];

                    $i = $entry['order_index'] - 1;
                    // $this->>battle_groups[$battle_group_id][$i] = $entry['monster_unit_id'];
                    if ($i > 0 || $battle_group_id == '')
                        continue;

                    @$this->missions[$mission_id][$fight_id][$battle_group_id]++;
                }

                foreach ($data['ScenarioBattleInfo'] ?? [] as $phase) {
                    $scenario_id     = $phase['scenario_battle_id'];
                    $battle_group_id = $phase['battle_group_id'];

                    // $this->>battle_groups[$battle_group_id][$phase['order_index'] - 1] = $phase['monster_unit_id'];
                    if ($battle_group_id == '')
                        continue;

                    @$this->missions[$mission_id][$scenario_id][$battle_group_id]++;
                }
            }
            //ksort($this->>waves);
            $this->waves = array_filter($this->waves, function ($val) { return $val < 100; });
            ksort($this->waves);
            print_r($this->waves);

            ksort($this->missions);
        }

        function parseTable($string) {
            if (empty($string))
                return [];

            $entries = explode(',', $string);

            $array = [];
            foreach ($entries as $val) {
                [$type, $id, $a, $b] = explode(':', $val);

                $table = \Solaris\FFBE\GameHelper::TEXT_TYPE[$type];
                $name  = \Solaris\FFBE\Helper\Strings::getString($table, $id)
                    ?? "{$type}:{$id}";
                $val   = "{$name}-{$a}-{$b}";

                $array[] = $val;
            }

            return $array;
        }

        public function printOutput() {
            foreach ($this->missions as $mission_id => $this->waves) {
                $sum          = $this->mission_runs[$mission_id];
                $mission_name = Strings::getString('MST_MISSION_NAME', $mission_id) ?? "Mission {$mission_id}";
                print " + {$mission_name} ({$mission_id}) [{$sum}]\n";

                ksort($this->waves);
                foreach ($this->waves as $wave_num => $groups) {
                    $count = array_sum($groups);
                    print "    Wave {$wave_num} [{$count} - " . number_format($count / $sum * 100, 1) . "%]\n";

                    ksort($groups);

                    foreach ($groups as $group_id => $count) {
                        print "        Group {$group_id} [{$count} - " . number_format($count / $sum * 100, 1) . "%]\n";

                        foreach ($this->battle_groups[$group_id] as $monster_id) {
                            // $loot         = $this->monsters[$monster_id] ?? [];
                            // $loot         = printTable($loot, true);
                            foreach ($this->monsters[$monster_id] as $part => $monster)
                                print "            ($monster_id.{$part}) {$monster['name']}\n";
                        }
                    }

                    print "\n";
                }
                print "\n";
            }

            print "# Monsters\n";
            foreach ($this->monsters as $monster_id => $monster)
                foreach ($monster as $part => $loot_tables) {
                    $monster_name = Strings::getString('MST_MONSTER_NAME', $monster_id);
                    print "    ({$monster_id}.{$part}) {$monster_name}\n";
                    print $this->printTable($loot_tables);
                    print "\n";
                }

        }

        protected function printTable($loot) {
            $loot = array_filter($loot);

            $string = '';
            foreach ($loot as $table => $items) {
                $string .= vsprintf("        %12s   %s%s", [
                    $table,
                    is_array($items) ? implode(', ', $items) : $items,
                    "\n",
                ]);
            }

            return $string;
        }
    }