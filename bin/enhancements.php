<?php
    /**
     * User: aEnigma
     * Date: 12.04.2017
     * Time: 12:17
     */

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    $entries = [];
    foreach (GameFile::loadMst('SublimationMstList') as $row) {
        $enhancement_id = (int) $row['enhancement_id'];
        // $names    = Strings::getStrings('MST_BEAST_NAME', $enhancement_id);

        if ($region == 'jp') {
            $names        = [$container[\Solaris\FFBE\Mst\SkillMstList::class]->getEntry($row['skill_id_old'])->name];
            $descriptions = [$row['47s39AqP']];
        } else {
            $names = Strings::hasStrings('MST_MAGIC_NAME', $row['skill_id_old'])
                ? Strings::getStrings('MST_MAGIC_NAME', $row['skill_id_old'])
                : Strings::getStrings('MST_ABILITY_NAME', $row['skill_id_old']);

            $descriptions = Strings::getStrings('MST_SUBLIMATION_AFTER_EXPLAIN', $enhancement_id);
        }

        $descriptions = array_map(
            function ($val) {
                return preg_replace("~^.*?<color=0:255:0>(?:<br>)?(.*?)</color>.*?$~", "$1", $val) ?: $val;
            },
            $descriptions
        );

        $mats = [];
        foreach (readParameters($row['mats'], ',:') as list($type, $item_id, $count)) {
            assert($type == 20);
            $mats[$item_id] = $count;
        }

        $units = readIntArray($row['unit']);

        $entries[$enhancement_id] = [
            'name'         => $names[0],
            'skill_id_old' => (int) $row['skill_id_old'],
            'skill_id_new' => (int) $row['skill_id_new'],

            "cost" => [
                "gil"       => (int) $row['gil'],
                "materials" => $mats,
            ],

            'units'   => $units,
            'strings' => [
                'names'       => $names,
                'description' => $descriptions,
            ],
        ];

        if ($region == 'jp') {
            unset($entries[$enhancement_id]['strings']['names']);
            $entries[$enhancement_id]['strings']['description'] = $entries[$enhancement_id]['strings']['description'][0];
        }
    }

    ksort($entries);
    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/enhancements.json", toJSON($entries, false));
    file_put_contents(DATA_OUTPUT_DIR . "/analyze.json", toJSON(arrayGroupValues(GameFile::loadMst('SublimationMstList'))));