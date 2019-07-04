<?php
    /**
     * User: aEnigma
     * Date: 21.02.2017
     * Time: 01:22
     */

    namespace Sol\FFBE\MstList;

    class IconMstList extends AbstractMstList {
        const MST_LIST = ['F_ICON_MST'];
        protected static $rows = [];

        public static function getFilename($icon_id) {
            $row = static::getRow($icon_id);
            if ($row == null)
                return null;

            return $row['file_name'];
        }

        protected static function parseRow($row, $mst) {
            $icon_id = $row['icon_id'];

            static::$rows[$icon_id] = $row;
        }
    }