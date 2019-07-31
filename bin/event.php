<?php
    /**
     * User: aEnigma
     * Date: 03.02.2017
     * Time: 22:53
     */


    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;

    require_once dirname(__DIR__) . "/bootstrap.php";
    require_once dirname(__DIR__) . "/helpers.php";

    if ($region == 'jp')
        require_once dirname(__DIR__) . "/bin/generate_strings.php";


    $entries = [];
    foreach (GameFile::loadMst('F_MEDAL_EXCHANGE_MST') as $row) {
        $mog_id      = (int) $row['medal_exchange_id'];
        $currency_id = (int) $row['ak2dhKm3'];
        $currency    = Strings::getString('MST_ITEM_NAME', $currency_id) ?? $currency_id;
        //        $item_id           = (int)$row['ak2dhKm3'];

        [$reward_type, $reward_id, $name, $num, $rest] = parseReward($row['target_info']);


        if ($reward_type == 'UNIT' && isset($rest[3])) {
            $tminfo  = $rest[3] ?? "100000000";
            $stminfo = $rest[5] ?? 0;
            $uname   = getUnitName($tminfo, $stminfo);
            $tmp     = getTmProgress($reward_id, $tminfo, $stminfo);

            $name .= " ({$tmp}% {$uname})";

            #if ($currency == "ふゆうそう")
            # var_dump([$currency, $name, $row['target_info']]);
        }

        $entry = [
            'name'        => $name ?: null,
            'reward_type' => $reward_type,
            'reward_id'   => (int) $reward_id,
            'price'       => (int) $row['count'],
            'amount'      => ((int) $row['x0NDnEC9']),
        ];

        $entries[$currency][] = $entry;
    }

    /*
        $e2 = [];
        foreach (readGameFile("03cY9vXe") as $row) {
            $mog_id     = (int)$row['6uIYE15X'];
            $comment_id = (int)$row['Z20mNDvL'];


            if (!isset($entries[$mog_id]))
                continue;

            $entry = $entries[$mog_id];

            $row['names']    = $entry['names'];
            $row['names'][0] = $entry['currency'];


            $row['test1'] = Sol\FFBE\Strings::getStrings('MST_TOWNSTORE_COMMENT', $comment_id . '_1')[0];
            $row['test2'] = Sol\FFBE\Strings::getStrings('MST_TOWNSTORE_COMMENT', $comment_id . '_2')[0];
            $row['test3'] = Sol\FFBE\Strings::getStrings('MST_TOWNSTORE_COMMENT', $comment_id . '_3')[0];


            $e2[] = $row + $entry;
            continue;
            $currency_id = (int)$row['ak2dhKm3'];
    //        $item_id           = (int)$row['ak2dhKm3'];

            $reward = parseReward($row['7iJpH5zZ']);
            list($reward_type, $reward_id, $reward_str_type, $rest) = $reward;
            $row['names']       = Sol\FFBE\Strings::getStrings("{$reward_str_type}_NAME", $reward_id);
            $row['reward_type'] = $reward_type;
            $row['reward_id']   = $reward_id;
            $row['price']       = (int)$row['Qy5EvcK1'];
            $row['amount']      = (int)$row['x0NDnEC9'];
            $row['currency']    = Sol\FFBE\Strings::getStrings('MST_ITEM_NAME', $currency_id)[0];
            $row['amount']      = (int)$row['x0NDnEC9'];

            unset($row['ak2dhKm3']);
            unset($row['7iJpH5zZ']);
            unset($row['Qy5EvcK1']);
            unset($row['x0NDnEC9']);

            $entries[] = $row;

        }
    */
    //    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/analyze.json", toJSON(arrayGroupValues($e2)));

    // event bonus units
    // I0SUr3WY


    // output
    $data = toJSON($entries, true, false);
    $data = preg_replace_callback('~{\s+([^{]+)\s+}~', function ($match) {
        return '{' . preg_replace('~\s+~', ' ', $match[1] . '}');
    }, $data);
    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/currency_exchange.json", $data);


    function getUnitName($tm_info, $stm_info) {
        if ($stm_info != 0)
            $tm_info = substr($stm_info, 1);

        elseif ($tm_info < 100000100)
            return "ALL";

        $str = Strings::getString("MST_UNIT_NAME", $tm_info);
        if ($str !== null)
            return $str;

        $base = substr($tm_info, 0, -1);
        for ($i = 5; $i > 2; $i--)
            if (($str = Strings::getString("MST_UNIT_NAME", "{$base}{$i}")) !== null)
                return $str;

        return $tm_info;
    }

    function getTmProgress($moogle_unit_id, $tm_info, $stm_info) {
        if ($stm_info > 0)
            return "STMR " . [5, 25, 50, 100][$stm_info[-1]];

        // ALL %
        switch ($tm_info) {
            case 100000001:
                return 5;
            case 100000002:
                return 10;

            case 100000005:
                return 1;

            case 100000008:
                return 50;
        }

        // Specific %
        switch ($tm_info[-1]) {
            case 201000501:
                return 5;
            case 201000502:
                return 25;
            case 201000503:
                return 50;
        }

        // Fallback to moogle rarity
        switch ($moogle_unit_id[-1]) {
            case 1:
                return 1;

            case 3:
                return 5;

            case 5:
                return 10;

            default:
                throw new UnexpectedValueException($tm_info . "_" . $moogle_unit_id);
        }
    }