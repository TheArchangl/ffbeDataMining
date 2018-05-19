<?php
    /**
     * User: Guido Kubach
     * Date: 24.12.2017
     * Time: 15:38
     */

    use Solaris\FFBE\Mst\UnitMstList;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    $entries = \Sol\FFBE\GameFile::loadMst(UnitMstList::getName());
    $entries = array_map(getLocalizedName('MST_UNIT_NAME', 'unit_id'), $entries);
    $entries = arrayGroupValues($entries);
    $entries = toJSON($entries);

    file_put_contents(DATA_OUTPUT_DIR . 'analyze.json', $entries);


    function getLocalizedName($table, $id_name) {
        return function (array $row) use ($table, $id_name) {
            $row['name'] = \Sol\FFBE\Strings::getString($table, $row[$id_name]);

            return $row;
        };
    }