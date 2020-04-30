<?php

    use Solaris\FFBE\GameHelper;

    require "../bootstrap.php";
    //require "../../ffbe-data/vendor/autoload.php";
    require "../../ffbe-discord/tmp/init_strings.php";

    ini_set('memory_limit', '2G');

    // import all requests
    //    $files = glob(__DIR__ . "/requests/gacha/raid_nier/*_GachaExeResponse.json");
    $files = glob(CLIENT_DIR . "gacha\\*\\*CNY 2019*\\*.json");

    $sum     = 0;
    $rewards = [];
    foreach ($files as $file) {
        $data = file_get_contents($file);
        if (empty($data))
            continue;

        $data = json_decode($data, true);
        $data = \Sol\FFBE\GameFile::replaceKeysRecursive($data);

        $units = $data['body']['data']['UserUnitInfo'] ?? [];
        $keys  = array_map(function ($unit) { return $unit['unit_real_id']; }, $units);
        $units = array_combine($keys, $units);

        $data = $data['body']['data']['UserGachaResult'][0];
        $list = explode(',', $data['result']);
        $sum  += $data['GachaActCnt'];
        assert(count($list) == $data['GachaActCnt']);
        foreach ($list as $reward) {
            $reward = explode(':', $reward);
            list($type, $id) = $reward;

            if ($type == 10) {
                $unit = $units[$reward[2]];

                if ($unit['trustmaster'] !== '') {
                    $tm = substr($unit['trustmaster'], 0, strpos($unit['trustmaster'], ':', 4));
                    $id .= ":{$tm}";
                }
            }

            @$rewards["{$type}:{$id}"]++;
        }
    }

    ksort($rewards);

    $keys = array_keys($rewards);
    foreach ($keys as $k => $key) {
        $name = GameHelper::formatMstItem($key);

        $keys[$k] = $name;
    }

    $total = array_sum($rewards);
    var_dump([$total, $sum]);
    assert($total == $sum);
    $rewards = array_combine_merge($keys, $rewards);

    foreach ($rewards as $reward => $count)
        printf("%-40s\t%5d\t%7s%%\n", $reward, $count, number_format($count / $total * 100, 2));
    printf("%-40s\t%5d\t%7s%%\n", "Total", $total, number_format($total / $total * 100, 2));


    function array_combine_merge(array $keys, array $vals) {
        $i   = 0;
        $arr = [];
        foreach ($vals as $k => $val) {
            $k = $keys[$i++] ?? $k;

            if (isset($arr[$k]))
                $arr[$k] += $val;

            else
                $arr[$k] = $val;
        }

        return $arr;
    }