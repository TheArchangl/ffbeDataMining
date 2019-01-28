<?php
    /**
     * User: aEnigma
     * Date: 25.08.2017
     * Time: 23:24
     */

    require_once dirname(__DIR__) . "/bootstrap.php";

    $data = `php se_loot.php`;

    echo(SELootParser::parse($data));

    class Dump {
        static           $level = 0;
        protected static $useToString;

        static function dump_var($var, $useToString = true) {
            static::$level       = 0;
            static::$useToString = $useToString;

            echo self::dump_unk($var);
        }

        static function dump_unk($var) {
            if (is_array($var))
                return static::dump_array($var);

            elseif (is_object($var))
                return static::dump_object($var);

            else
                return "{$var}\n";
        }

        static function dump_array(array $array) {
            if (empty($array))
                return "[]\n";

            if (count($array) == 1) {
                // single entry
                $key = key($array);
                if ($key == 0)
                    return "[   " . trim(static::dump_unk(current($array))) . "    ]\n";

                return "['" . key($array) . "' => " . trim(static::dump_unk(current($array))) . " ]\n";
            }


            // multiline
            $str = "[\n";

            static::$level++;

            $keys = array_keys($array);
            if ($keys == range(0, count($keys) - 1))
                $len = 0;

            else
                $len = max(array_map('strlen', $keys));

            foreach ($array as $k => $v) {
                $str .= static::indent();
                if ($len > 0)
                    $str .= "'" . str_pad($k, $len) . "' => ";

                $str .= static::dump_unk($v);
            }

            static::$level--;
            $str .= static::indent() . "]";
            $str .= "\n";

            return $str;
        }

        static function dump_object($object) {
            $class = get_class($object);

            if (static::$useToString && is_callable([$object, '__toString'])) {
                $string = ([$object, "__toString"])();

                return "({$class}): '{$string}'\n";

            } elseif (is_callable([$object, '__debugInfo'])) {
                $arr = ([$object, "__debugInfo"])();

                return "({$class})# " . static::dump_array($arr);
            } else {
                return " = [#todo#]\n";
            }
        }

        private static function indent($level = null) {
            return str_repeat('    ', $level ?? static::$level);
        }
    }

    class LootBag {
        /** @var SimpleLootEntry[] */
        protected $loot = [];
        /** int */
        private $color;

        /**
         * @param int|null $color
         */
        public function __construct(int $color = null) {
            $this->color = $color;
        }

        /**
         * @return SimpleLootEntry[]
         */
        public function getLoot(): array {
            return $this->loot;
        }

        /**
         * @param SimpleLootEntry[] $loot
         */
        public function setLoot(array $loot): void {
            $this->loot = $loot;
        }

        /**
         * @return int
         */
        public function getColor() {
            return $this->color;
        }

        /**
         * @param int $color
         */
        public function setColor($color): void {
            $this->color = $color;
        }

        /**
         * @param int    $tier
         * @param string $table
         * @param int    $chance
         * @param        $nums
         *
         * @return SimpleLootEntry
         */
        public function addRow(int $tier, string $table, int $chance, $nums) {
            return new SimpleLootEntry($tier, $table, $chance, 1, $nums);
        }

        /**
         *
         * @return array
         */
        public function getSplit() {
            $arr = [];
            foreach ($this->loot as $entry)
                @$arr[$entry->tier][] = $entry;

            foreach ($arr as $tier => $a)
                uasort($arr[$tier],
                    function (SimpleLootEntry $a, SimpleLootEntry $b) { return $a->tier <=> $b->tier ?: $b->chance <=> $a->chance; }
                );


            return $arr;
        }

        /**
         *
         * @return array
         */
        public function __debugInfo() {
            return [
                'color' => $this->color,
                'loot'  => $this->loot
            ];
        }

        /**
         * @param SimpleLootEntry $entry
         */
        public function addEntry(SimpleLootEntry $entry) {
            $this->loot[] = $entry;
        }

        /**
         *
         * @return array
         */
        public function getStrings() {
            $arr   = ["", "", "", "", "", "",];
            $split = $this->getSplit();

            for ($i = 1; $i <= count(SELootParser::TIERS); $i++) {
                if (empty($split[$i])) {
                    $arr[$i] = "-";
                    continue;
                }

                $substrs = [];
                foreach ($split[$i] as $chance => $drop)
                    $substrs[] = $drop;

                $arr[$i] = join(' + ', $substrs);
            }

            return $arr;
        }
    }

    class MetaEntry {
        /** @var SimpleLootEntry[] */
        private $entries;
        private $note;

        public function __construct(array $entries, $note) {
            $this->entries = $entries;
            $this->note    = $note;
        }
    }

    class SimpleLootEntry {
        public $tier;
        public $chance;

        /** @var int */
        public $count;
        /** @var int[] */
        public $nums;

        /** @var string */
        public $table;

        public $note = '';

        /**
         * @param int   $tier
         * @param       $table
         * @param int   $chance
         * @param int   $count
         * @param int[] $nums
         */
        public function __construct($tier, $table, $chance, $count = 1, array $nums) {
            $this->tier   = $tier;
            $this->chance = $chance;
            $this->count  = $count;
            $this->nums   = $nums;
            $this->table  = $table;
        }

        public function addNum($num) {
            $this->nums[] = $num;
        }

        public function __toString() {
            $str = '';

            if ($this->nums[0] == $this->nums[1])
                $str .= $this->nums[0] * $this->count;
            else {
                if ($this->count > 1)
                    $str .= "{$this->count}*";
                $str .= join('/', $this->nums);
            }

            if ($this->chance < 100)
                $str .= "x {$this->chance}%";

            if ($this->note != '')
                $str .= " ^({$this->note})";

            return $str;
        }
    }

    class SELootParser {
        const COLORS = [
            ['White', 'Black', 'Green', 'Healing'],
            ['Power', 'Guard', 'Support', 'Tech']
        ];
        const TIERS  = [
            'Al'    => 1,
            'Mil'   => 2,
            'Heavi' => 3,
            'Gian'  => 4,
            'Pure'  => 5,
        ];
        public static $i = 0;

        public static function parse(string $data) {
            $stages = explode('# Stage ', $data);
//            unset($stages[0]);

            return join('', array_map([static::class, 'parseStage'], $stages));
        }

        public static function parseStage(string $data) {
            if (empty(trim($data)) || $data == "# Stage ")
                return "";

            $stage = trim(substr($data, 0, 3));
            $loot  = static::readLoot($data);

            if ($loot == null)
                return "";

            $loot = static::combineLoot($loot);

            return static::formatStage($stage, $loot);
        }

        /**
         * @param $data
         *
         * @return LootBag
         */
        public static function readLoot($data) {
            if (!preg_match_all('~\s+[*]\s+(normal|rare|unique)\s+(.+?)\n~', $data, $matches, PREG_SET_ORDER))
                return null;

            $i     = 1;
            $stage = new LootBag(null);

            foreach ($matches as $k => $match) {
                $drops = explode(', ', $match[2]);
                if (empty($drops))
                    continue;

                $drops = array_chunk($drops, 4);
                $drops = array_map([static::class, 'readDrop'], $drops);
                $table = $match[1];
                $color = (int)($drops[0][0] == 'Power');

                if ($stage->getColor() == null)
                    $stage->setColor($color);
                else
                    assert($color == $stage->getColor());

                $entries = [];
                foreach ($drops as $j => [$type, $tier, $num, $chance])
                    $entries[] = new SimpleLootEntry(static::TIERS[$tier], $table, (int)$chance, 1, [$num, $num]);
                //
                //                if (count($entries) == 1)
                //                    $entry = $entries[0];
                //
                //                else
                //                    $entry = new MetaEntry($entries, $i++);
                if (count($entries) > 1) {
                    foreach ($entries as $entry)
                        $entry->note = $i;
                    $i++;
                }

                foreach ($entries as $entry)
                    $stage->addEntry($entry);
            }


            return $stage;
        }

        /**
         * @param array $drops
         *
         * @return array
         */
        static function readDrop(array $drops): array {
            $drops = array_map(function ($val) { return preg_split('~( |-|cryst-)~', $val); }, $drops);

            // verify colors
            $colors = array_column($drops, 0);
            assert(in_array($colors, static::COLORS)) or die(print_r($colors, true));

            // check tier
            assert(count(array_unique(array_column($drops, 1))) == 1);

            // check drop%
            assert(count(array_unique(array_column($drops, 2))) == 1);

            return $drops[0];
        }

        /**
         * @param LootBag $loot
         *
         * @return LootBag
         */
        private static function combineLoot(LootBag $loot) {
            $test = new LootBag($loot->getColor());

            $entries = [];
            $getKey  = function (SimpleLootEntry $entry) { return "{$entry->tier}.{$entry->chance}"; };

            $todo = [];
            foreach ($loot->getLoot() as $index => $drop) {
                $key = $getKey($drop);
                if ($drop->note == '')
                    // add
                    if (isset($entries[$key]))
                        $entries[$key]->count += $drop->count;

                    else
                        $entries[$key] = $drop;

                else
                    $todo[$drop->note][$key][] = $drop;

            }

            foreach ($todo as $group => $drops) {
                if (count($drops) == 1) {
                    /** @var SimpleLootEntry $first */
                    $drops = current($drops);
                    $first = $drops[0];
                    $nums  = array_map(function (SimpleLootEntry $e) { return $e->nums[0]; }, $drops);

                    $key = "{$first->tier}.{$first->chance}." . join('-', $nums);
                    if (isset($entries[$key]))
                        $entries[$key]->count += $first->count;

                    else
                        $entries[$key] = new SimpleLootEntry($first->tier, $first->table, $first->chance, 1, [$nums[0], $nums[1]]);

                } else foreach ($drops as $drop)
                    foreach ($drop as $entry)
                        $entries[] = $entry;

            }

            $test->setLoot(array_values($entries));

            return $test;
        }

        private static function formatStage($stage, LootBag $loot) {
            $strings = $loot->getStrings();

            return sprintf("%02s %33s |    |%13s |%13s |%13s |%13s | %s | \n",
                           $stage, ' ',
                           $strings[1],
                           $strings[2],
                           $strings[3],
                           $strings[4],
                           $loot->getColor() == 0 ? 'W B G H' : 'P G S T'
            );
        }
    }