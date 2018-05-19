<?php
    /**
     * User: aEnigma
     * Date: 15.07.2017
     * Time: 21:34
     */

    namespace Sol\FFBE;

    use Solaris\FFBE\GameHelper;
    use Solaris\Formatter\FormatHelper;

    class ChallengeParser {

        public static function parse($condition_str, $game_local = false) {
            // parse
            $result   = [];
            $callable = $game_local
                ? [static::class, 'parseConditionGame']
                : [static::class, 'parseCondition'];

            $conditions = GameHelper::readParameters($condition_str, ':,');
            foreach ($conditions as $condition)
                $result[] = $callable($condition[0], array_slice($condition, 1));

            return $result;
        }

        /**
         * @param int   $type
         * @param int[] $vals
         *
         * @return string
         */
        protected static function parseCondition($type, $vals): string {
            switch ($type) {
                // (Don't) use
                case '0':
                    return 'Use an item';

                case '1':
                    return 'No items';

                case '2':
                    assert(count($vals) == 1);
                    $name = Strings::getString('MST_ITEM_NAME', $vals[0]);

                    return "Use {$name}";

                case '40':
                    return "Use <= {$vals[0]} items";

                case '6':
                    return "No magic";

                case '5':
                    return "Use magic";

                case '41':
                    return "Use >= {$vals[0]} magic";

                case '12':
                    assert($vals[0] == '3');

                    return 'No recovery magic';

                case '13':
                    assert(count($vals) == 1);
                    $type = GameHelper::MAGIC_TYPE[$vals[0]];

                    return "Use {$type} magic";

                case '14':
                    assert(count($vals) == 1);
                    $type = GameHelper::MAGIC_TYPE[$vals[0]];

                    return "No {$type} magic";

                case '16':
                    return "Use a limitburst";

                case '49':
                    return "Use {$vals[0]} limitbursts";

                case '17':
                    return "No limitbursts";

                case '20':
                    return "No abilities";

                case '7':
                    $names = self::getSkillNames($vals, true);

                    return "Use {$names}";

                case '21':
                    $names = self::getSkillNames($vals, true);

                    return "Use {$names}";

                case '22':
                    $names = self::getSkillNames($vals, true);

                    return "Don't use {$names}";

                case '29':
                    return 'No espers';

                case '28':
                    return 'Summon an esper';

                case '30':
                    $name = Strings::getString('MST_BEAST_NAME', $vals[0]);

                    return "Summon {$name}";

                case '45':
                    return "Summon {$vals[0]} espers";

                // Defeat with
                case '4':
                    $name = Strings::getString('MST_MONSTER_NAME', $vals[1]) ?? $vals[1];
                    assert($vals[2] == 1);

                    return "Defeat {$name} with an item";

                case '15':
                    $name = Strings::getString('MST_MONSTER_NAME', $vals[1]) ?? $vals[1];
                    assert($vals[2] == 1);

                    return "Defeat {$name} with magic";

                case '18':
                    $name = Strings::getString('MST_MONSTER_NAME', $vals[1]) ?? $vals[1];
                    assert($vals[2] == 1);

                    return "Defeat {$name} with a limit burst";

                case '23':
                    $name = Strings::getString('MST_MONSTER_NAME', $vals[1]) ?? $vals[1];
                    assert($vals[2] == 1);

                    return "Defeat {$name} with an ability";

                case 67:
                    $name = Strings::getString('MST_MONSTER_NAME', $vals[1]) ?? $vals[1];

                    return "Defeat {$name} with {$vals[0]},{$vals[1]}";

                case '32':
                    $name = Strings::getString('MST_MONSTER_NAME', $vals[1]) ?? $vals[1];
                    assert($vals[2] == 1);

                    return "Defeat {$name} with an esper";

                // Deal damage
                case '26':
                    $type = GameHelper::ELEMENT_TYPE[$vals[0] - 1];

                    return "Deal {$type} damage";

                case '59':
                    $type = GameHelper::ELEMENT_TYPE[$vals[1]];

                    return "Deal {$type} damage {$vals[0]} times";

                case '47':
                    $type = GameHelper::ELEMENT_TYPE[$vals[1]];

                    return "Deal {$type} damage {$vals[0]} times or more";

                // Misc
                case '33':
                    return 'No KOs';

                case '34':
                    return "Party size >= {$vals[0]}";

                case '35':
                    return "Party size <= {$vals[0]}";

                case '36':
                    $name = Strings::getString('MST_UNIT_NAME', $vals[0]);

                    return "{$name} in party";

                case '38':
                    return 'No continues';

                case '68':
                    return 'Complete mission';

                // Turn counts
                case '75':
                    $turns = $vals[0] == $vals[1]
                        ? $vals[0]
                        : join('-', $vals);

                    return "Clear in {$turns} turns";

                case '76':
                    // mission id?
                    return "Clear in {$vals[1]} turns or more";

                case '77':
                    // mission id?
                    return "Clear in {$vals[1]} turns or less";


                // Apply rule!
                /** @noinspection PhpMissingBreakStatementInspection */
                case '69':
                    switch ($vals[0]) {
                        case '2001':
                            return 'No Rain or Lasswell';

                        case '2002':
                        case '2004':
                        case '2006':
                        case '2008':
                        case '2010':
                            return 'Reach the goal';

                        case '2003': // 10
                        case '2005': // 30
                        case '2007': // 30
                        case '2009': // 30
                        case '2011': // 30
                            return 'Answer all questions correctly';
                    }

                default:
                    var_dump($type);
                    die();
            }
        }

        /**
         * @param array $names
         * @param bool  $skillIDs
         *
         * @return string
         */
        protected static function getSkillNames(array $names, $skillIDs = false) {
            $names = array_map(function ($skill_id) use ($skillIDs) {
                $name = Strings::getString('MST_ABILITY_NAME', $skill_id)
                    ?? Strings::getString('MST_MAGIC_NAME', $skill_id);

                return $skillIDs
                    ? "{$name} ({$skill_id})"
                    : $name;
            }, $names);

            $names = join(' / ', $names);

            return $names;
        }

        /**
         * @param int   $type
         * @param int[] $vals
         *
         * @return string
         */
        private static function parseConditionGame($type, $vals) {
            switch ($type) {
                case '3':
                    // battle id?
                    return 'No escapes';
                case '71':
                    // 51?
                    return 'No escapes';

                // (Don't) use
                case '0':
                    return 'Use an item';

                case '1':
                    return 'No items';

                case '2':
                    assert(count($vals) == 1);
                    $name = (Strings::getString('MST_ITEM_NAME', $vals[0]));

                    return "Use {$name}";

                case '40':
                    return "Use no more than {$vals[0]} items";

                case '6':
                    return "No magic";

                case '5':
                    return "Use magic";

                case '41':
                    $num = $vals[0] + 1;

                    return "Use magic {$num} or more times";

                case '12':
                    assert($vals[0] == '3');

                    return 'No recovery magic';

                case '13':
                    assert(count($vals) == 1);
                    $type = (GameHelper::MAGIC_TYPE[$vals[0]]);

                    return "Use {$type} magic";

                case '14':
                    assert(count($vals) == 1);
                    $type = (GameHelper::MAGIC_TYPE[$vals[0]]);

                    return "No {$type} magic";

                case '16':
                    return "Use a limit burst";

                case '49':
                    $num = $vals[0] + 1;

                    return "Use {$num} or more limit bursts";

                case '17':
                    return "No limit bursts";

                case '20':
                    return "No abilities";

                case '7':
                    $names = self::getSkillNames($vals);

                    return "Use {$names}";

                case '21':
                    $names = self::getSkillNames($vals);

                    return "Use {$names}";

                case '22':
                    $names = self::getSkillNames($vals);

                    return "Don't use {$names}";


                case '29':
                    return 'No espers';

                case '28':
                    return 'Evoke an esper';

                case '30':
                    $name = Strings::getString('MST_BEAST_NAME', $vals[0])
                    ?? "Esper #{$vals[0]}";

                    return "Evoke {$name}";

                case '45':
                    $num = $vals[0] + 1;

                    return "Evoke {$num} or more espers";

                // Defeat with
                case '4':
                    $name = Strings::getString('MST_MONSTER_NAME', $vals[1]) ?? $vals[1];
                    assert($vals[2] == 1);

                    return "Defeat {$name} with an item";

                case '15':
                    $name = Strings::getString('MST_MONSTER_NAME', $vals[1]) ?? $vals[1];
                    assert($vals[2] == 1);

                    return "Defeat {$name} with magic";

                case '18':
                    $name = Strings::getString('MST_MONSTER_NAME', $vals[1]) ?? $vals[1];
                    assert($vals[2] == 1);

                    return "Defeat {$name} with a limit burst";

                case '23':
                    $name = Strings::getString('MST_MONSTER_NAME', $vals[1]) ?? $vals[1];
                    assert($vals[2] == 1);

                    return "Defeat {$name} with an ability";

                case '32':
                    $name = Strings::getString('MST_MONSTER_NAME', $vals[1]) ?? $vals[1];
                    assert($vals[2] == 1);

                    return "Defeat {$name} with an esper";

                case 63:
                    // Defeat with specific skill
                    assert($vals[2] == 1);

                    $name  = Strings::getString('MST_MONSTER_NAME', $vals[1]);
                    $skill = Strings::getString('MST_ABILITY_NAME', $vals[3])
                        ?? $vals[3];

                    return "Defeat {$name} with {$skill}";

                case 65:
                    // Defeat with specific esper
                    assert($vals[2] == 1);

                    $name  = Strings::getString('MST_MONSTER_NAME', $vals[1]);
                    $skill = Strings::getString('MST_BEAST_NAME', $vals[3])
                        ?? "Esper #{$vals[3]}";

                    return "Defeat {$name} with {$skill}";

                case 67:
                    // Defeat with specific limitburst?
                    assert($vals[2] == 1);

                    $name  = (Strings::getString('MST_MONSTER_NAME', $vals[1]));
                    $skill = Strings::getString('MST_ABILITY_NAME', $vals[3])
                        ?? Strings::getString('MST_MAGIC_NAME', $vals[3])
                        ?? Strings::getString('MST_LIMITBURST_NAME', $vals[3])
                        ?? $vals[3];

                    $skill = ($skill);

                    return "Defeat {$name} with {$skill}";

                // Deal damage
                case '26':
                    $type = (GameHelper::ELEMENT_TYPE[$vals[0] - 1]);

                    return "Deal {$type} damage";

                case '59':
                    $type = $vals[0] - 1;
                    $type = (GameHelper::ELEMENT_TYPE[$type]);
                    $num  = $vals[1] + 1;

                    return "Deal {$type} damage {$num} times or more";

                case '47':
                    $type = $vals[1] - 1;
                    $type = (GameHelper::ELEMENT_TYPE[$type]);

                    return "Deal {$type} damage {$vals[0]} times or more";

                // Misc
                case '33':
                    return 'Clear without an ally being KO\'d';

                case '34':
                    $num = $vals[0] + 1;

                    return "Party of {$num} or more (Companion included)";

                case '35':
                    $num = $vals[0];

                    return "Party of {$num} or less (Companion included)";

                case '36':
                    $name = Strings::getString('MST_UNIT_NAME', $vals[0]);

                    return "{$name} in party (Companion included)";

                case '38':
                    return 'No continues';


                case '68':
                    return 'Complete the quest';


                // Turn counts
                case '75':
                    $num = $vals[1];

                    return "Clear within {$num} turns";
                /*
                $turns = $vals[0] == $vals[1]
                    ? $vals[0]
                    : join('-', $vals);

                return "Clear in {$turns} turns";
                */

                case '76':
                    // mission id?
                    return "Clear in {$vals[1]} turns or more";

                case '77':
                    // scenario battle id
                    return "Clear in {$vals[1]} turns or less";

                case 102:
                    return "Exploit elemental[?] weakness {$vals[0]} times or more";

                case 102:
                    return "Exploit elemental[?] weakness {$vals[0]} times or more";

                case 122:
                    $chain = $vals[0] + 1;
                    $turns = FormatHelper::formatTurns($vals[1] ?? 1);

                    return "Get a chain of {$chain} or more in {$turns}";

                case 132:
                    $chain = $vals[0] + 1;
                    $turns = FormatHelper::formatTurns($vals[1] ?? 1);

                    return "Activate an element chain {$chain} times or more in {$turns}";

                // Apply rule!
                /** @noinspection PhpMissingBreakStatementInspection */
                case '69':
                    switch ($vals[0]) {
                        case '2001':
                            return 'No Rain or Lasswell (Companion included)';

                        case '2002':
                        case '2004':
                        case '2006':
                        case '2008':
                        case '2010':
                            return 'Reach the goal';

                        case '2003': // 10
                        case '2005': // 30
                        case '2007': // 30
                        case '2009': // 30
                        case '2011': // 30
                            return 'Answer all questions correctly';

                        case '2012':
                            return 'Set up a tent';

                        case '2013':
                        case '2014':
                        case '2015':
                        case '2016':
                        case '2017':
                        case '2018':
                            return "Collect from 3 harvest points";

                        case '2019':
                            return 'Defeat all shadow bahamuts and bahamut';

                        case '2020':
                        case '2028':
                            return 'Defeat the boss';

                        default:
                            return "Unknown rule {$vals[0]}: " . Strings::getString('MST_RULE_COND', $vals[0]);
                    }

                // GE timed dungeons
                case 1000:
                    $time = $vals[0] / 60;

                    return "Finish mission in {$time} mins";

                case 1001:
                    $time = $vals[0] / 60;
                    $name = Strings::getString('MST_MONSTER_NAME', $vals[1]) ?? $vals[1];

                    return "Find {$name} in {$time} mins";

                case 1002:
                    $time = $vals[0] / 60;
                    $name = Strings::getString('MST_MONSTER_NAME', $vals[1]) ?? $vals[1];

                    return "Defeat the {$name} in {$time} mins";

                case 1003:
                    $time = $vals[0] / 60;
                    $num  = $vals[1];

                    return "Collect from {$num} or more harvest points in {$time} mins";

                default:
                    return "Unknown type {$type}";
            }

        }
    }