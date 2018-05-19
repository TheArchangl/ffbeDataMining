<?php
    /**
     * User: aEnigma
     * Date: 15.07.2017
     * Time: 21:32
     */

    namespace Sol\FFBE\MstList;

    use Sol\FFBE\GameFile;

    abstract class AbstractMstList {
        const MST_LIST = [];

        protected static $rows = [];
        protected static $init = false;

        /**
         *
         */
        public static function init() {
            foreach (static::MST_LIST as $mst)
                foreach (GameFile::loadMst($mst) as $row)
                    static::parseRow($row, $mst);
        }

        /**
         * @param int|string $id
         *
         * @return mixed|null
         */
        public static function getRow($id) {
            return static::$rows[$id] ?? null;
        }

        /**
         * @return array
         */
        public static function getRows() {
            if (!static::$init)
                static::init();

            return static::$rows;
        }

        /**
         * @param array  $row
         * @param string $mst
         *
         * @return mixed
         */
        abstract protected static function parseRow($row, $mst);
    }