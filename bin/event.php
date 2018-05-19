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


    $entries = [];
    foreach (GameFile::loadMst('2qDEnLF9') as $row) {
        $mog_id      = (int)$row['medal_exchange_id'];
        $currency_id = (int)$row['ak2dhKm3'];
        $currency    = Strings::getString('MST_ITEM_NAME', $currency_id);
//        $item_id           = (int)$row['ak2dhKm3'];

        $reward = parseReward($row['target_info']);
        list($reward_type, $reward_id, $name, $rest) = $reward;

        $entry = [
            'name'       => $name ?: null,
            'reward_type' => $reward_type,
            'reward_id'   => (int)$reward_id,
            'price'       => (int)$row['count'],
            'amount'      => ((int)$row['x0NDnEC9']),
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
    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/mog_king.json", $data);