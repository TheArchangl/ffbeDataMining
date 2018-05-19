<?php
    /**
     * User: aEnigma
     * Date: 15.07.2017
     * Time: 21:32
     */

    namespace Sol\FFBE\MstList;

    class ChallengeMstList extends AbstractMstList {
        const MST_LIST = ['F_CHALLENGE_MST'];

        protected static $mission_map = [];

        protected static function parseRow($row, $mst) {
            $challenge_id = $row['challenge_id'];
            $mission_id   = $row['mission_id'];

            static::$rows[$challenge_id]        = $row;
            static::$mission_map[$mission_id][] = $challenge_id;
        }

        /**
         * @return array
         */
        public static function getMissionMap(): array {
            return self::$mission_map;
        }

        /**
         * @param int $mission_id
         *
         * @return array
         */
        public static function byMission($mission_id) {
            return self::$mission_map[$mission_id] ?? null;
        }
    }