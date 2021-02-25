<?php
    /**
     * User: aEnigma
     * Date: 03.02.2017
     * Time: 22:53
     */

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;
    use Solaris\FFBE\GameHelper;

    require_once dirname(__DIR__) . '/bootstrap.php';
    require_once dirname(__DIR__) . '/helpers.php';
    require_once __DIR__ . '/read_strings.php';

    $entries = [];
    foreach (GameFile::loadMst('F_MEDAL_EXCHANGE_MST') as $row) {
        $mog_id      = (int) $row['medal_exchange_id'];
        $currency_id = (int) $row['ak2dhKm3'];
        $currency    = Strings::getString('MST_ITEM_NAME', $currency_id) ?? $currency_id;
        [$reward_type, $reward_id, $name, $num, $rest] = GameHelper::parseMstItem($row['target_info']);

        if ($reward_type === 'UNIT' && isset($rest[3])) {
            $tminfo  = $rest[3] ?? '100000000';
            $stminfo = $rest[5] ?? 0;
            $uname   = getUnitName($tminfo, $stminfo);
            $tmp     = getTmProgress($reward_id, $tminfo, $stminfo);
            $name    .= " ({$tmp}% {$uname})";
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

    // output
    $data = toJSON($entries, true, false);
    $data = preg_replace_callback('~{\s+([^{]+)\s+}~', static function ($match) {
        return '{' . preg_replace('~\s+~', ' ', $match[1] . '}');
    }, $data);
    file_put_contents(DATA_OUTPUT_DIR . "/{$region}/currency_exchange.json", $data);


    function getUnitName(string $tm_info, string $stm_info): string {
        if ((int) $stm_info > 0)
            $tm_info = substr($stm_info, 1);

        elseif ((int) $tm_info < 100000100)
            return 'ALL';

        $str = Strings::getString('MST_UNIT_NAME', $tm_info);
        if ($str !== null)
            return $str;

        $base = substr($tm_info, 0, -1);
        for ($i = 5; $i > 2; $i--)
            if (($str = Strings::getString('MST_UNIT_NAME', "{$base}{$i}")) !== null)
                return $str;

        return $tm_info;
    }

    function lastDigit(int|string $value): int {
        return (int) $value[-1];
    }

    function getTmProgress(string $moogle_unit_id, int $tm_info, string $stm_info): string {
        if ($stm_info > 0)
            return 'STMR ' . ([5, 25, 50, 100, '??', '??', '??'][lastDigit($stm_info)] ?? '??');

        // ALL %
        return match ($tm_info) {
            100000005 => 1,

            100000001,
            201000501 => 5,

            100000002 => 10,
            201000502 => 25,

            100000008,
            201000503 => 50,

            default => match (lastDigit($moogle_unit_id)) {
                1 => 1,
                3 => 5,
                5 => 10,
                default => -1,
            },
        };
    }