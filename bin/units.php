<?php
    /**
     * User: aEnigma
     * Date: 24.01.2017
     * Time: 17:39
     */

    // $region = 'jp';

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Reader\UnitReader;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\GameHelper;

    require_once dirname(__DIR__) . '/bootstrap.php';
    require_once dirname(__DIR__) . '/helpers.php';

    // unit data
    $units    = [];
    $unit_map = [];

    echo "Units\n";
    $reader = new UnitReader($region);
    $reader->save(join('/', [DATA_OUTPUT_DIR, $region, 'units.json',]));

    if ($region === 'jp') {
        echo "Brave shift\n";
        $entries = [];
        foreach (GameFile::loadMst('F_NV_SHIFT_MST') as $row) {
            ['rZhG3M4b' => $brave_shift_id, 'f7yISEz8' => $info, '4fbM3gWc' => $unk1] = $row;
            assert($unk1 === '0');

            $info = GameHelper::readIntArray($info, ':');
            #$frames = parseList($row['effect_frames'], '@&:');
            #$frames = SkillReader::flattenFrames($frames, 0);

            $entries[$brave_shift_id] = [
                'ready'    => $info[0] + 1,
                'duration' => $info[1],
                'cooldown' => $info[2],
                'turnback' => (bool) (1 - $info[3]),
                #'frames'   => $frames,
            ];
        }
    }

    file_put_contents(join('/', [DATA_OUTPUT_DIR, $region, 'unit_brave_shift.json']), toJSON($entries));

    //
    echo "UoL\n";
    $entries = [];
    foreach (GameFile::loadMst('F_GACHA_SELECT_UNIT_MST') as $row) {
        assert($row['e4mG8jTc'] == 10) or die ('UoC: cost');
        assert($row['date_end'] == '2030-12-31 23:59:59') or die ('UoC: end date');

        //
        $id           = (int) $row['unit_evo_id'];
        $entries[$id] = [
            'name' => Strings::getString('MST_UNIT_NAME', $id) ?? $reader->getUnit($id)['name'],
            'date' => $row['date_start'],
            //            'date_end'   => $row['date_end'],
        ];
    }

    uksort($entries, static function ($a, $b) use ($entries) {
        return $entries[$a]['date'] <=> $entries[$b]['date'] ?: $a <=> $b;
    });

    $file = join('/', [DATA_OUTPUT_DIR, $region, 'unit_selection.json']);
    file_put_contents($file, json_encode($entries, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, 512));