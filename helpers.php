<?php
    /**
     * User: aEnigma
     * Date: 24.01.2017
     * Time: 17:40
     */

    use Solaris\FFBE\GameHelper;

    function readArray(string|array $input, string $delim, bool $force = true): array|string|int {
        if (is_scalar($input))
            if (str_contains($input, $delim))
                return explode($delim, $input);

            elseif ($force)
                return [$input];

            else
                return $input;

        if (! is_array($input))
            throw new \LogicException('Input type must be a string or an array of strings');

        foreach ($input as $k => $val)
            $input[$k] = readArray($val, $delim, $force);

        return $input;
    }

    /**
     * @param string|int|string[]|int[] $val
     *
     * @return array|int|string
     */
    function toInt(array|int|string $val): array|int|string {
        if (is_array($val))
            return array_map('toInt', $val);

        elseif (is_string($val) && ctype_digit($val))
            return (int) $val;

        else
            return $val;
    }

    function readParameters(string $string, string $force_delim = ','): array|int {
        $values = $string;

        foreach (['@', ',', '&', ':'] as $delim) {
            $values = readArray($values, $delim, str_contains($force_delim, $delim));
        }

        return toInt($values);
    }

    /**
     * @param array $array
     * @param array $array_fields
     * @param bool  $use_names
     *
     * @return array
     */
    function arrayGroupValues(array $array, array $array_fields = [], bool $use_names = true): array {
        $vals = [];

        $addVal = static function ($path, $name, $val) use (&$vals, &$addVal, $array_fields) {
            if (is_array($val) && in_array($path, $array_fields, true))
                foreach ($val as $k => $v)
                    $addVal($path . '_' . $k, $name, $v);

            elseif (is_scalar($val))
                $vals[$path][$val][] = $name;
        };

        foreach ($array as $entry) {
            $name = $use_names
                ? $entry['names'][0] ?? $entry['name'] ?? current($entry)
                : current($entry);

            foreach ($entry as $k => $v)
                $addVal($k, $name, $v);
        }

        // remove fields with no info
        foreach ($vals as $key => $arr)
            if (count($arr) === 1)
                unset($vals[$key]);

        $vals = array_map(static function ($arr) {
            ksort($arr);

            return $arr;
        }, $vals);
        ksort($vals);

        return $vals;
    }

    function groupValues(array $array): array {
        $vals = [];

        foreach ($array as $k => $v) {
            if (is_scalar($v))
                $vals[(string) $v][] = $k;
        }

        return $vals;
    }

    function readTM(string $string): ?array {
        if ($string === '')
            return null;

        [$type, $id] = GameHelper::parseMstItem($string);
        return [$type, (int) $id];
    }

    function formatStats($entry): array {
        $vals = [];
        $keys = [
            'hp',
            'mp',
            'atk',
            'def',
            'mag',
            'spr',
        ];

        foreach ($keys as $key)
            $vals[strtoupper($key)] = readIntArray($entry[$key]);

        return $vals;
    }

    function readIntArray(string $str, string $delim = ','): array {
        if (trim($str) === '')
            return [];

        $str = explode($delim, $str);
        $str = array_map('intval', $str);

        return $str;
    }

    /**
     * @param mixed  $val
     * @param string $char
     *
     * @return string[][]
     */
    function parseListStep(mixed $val, string $char): array {
        return is_array($val)
            ? array_map(static fn($v) => parseListStep($v, $char), $val)
            : explode($char, $val);
    }

    /**
     * @param string $string
     * @param string $chars
     *
     * @return string[]|string
     */
    function parseList(string $string, string $chars): array|string {
        foreach (str_split($chars) as $char)
            $string = parseListStep($string, $char);

        return $string;
    }

    /**
     * @param array $effects
     *
     * @return array
     */
    function flattenFrames(array $effects): array {
        $frames = [];

        foreach ($effects as $effect) {
            $array = [];

            foreach ($effect as $hit)
                $array[] = (int) $hit[0];

            $frames[] = $array;
        }

        return $frames;
    }

    /**
     * @param array $data
     * @param array $entry
     *
     * @return array
     */
    function parseFrames(array $data, array $entry): array {
        $frames = parseList($data['attack_frames'], '@-:');
        $frames = flattenFrames($frames);

        $entry['attack_count']  = array_map(static function ($frames) { return count($frames); }, $frames);
        $entry['attack_frames'] = $frames;

        $frames = parseList($data['effect_frames'], '@&:');
        $frames = flattenFrames($frames);
        // $frames   = $data['effect_frames'];
        $entry['effect_frames'] = $frames;

        return $entry;
    }

    function readEquip(string $str): array {
        $str = explode(',', $str);
        $str = array_map('intval', $str);
        //        $str = array_map(function ($id) use ($equipment_id) {
        //            return EQUIPMENT_TYPE[$id] ?? $id;
        //        }, $str);

        return $str;
    }

    function recursiveUTF(array $input): array {
        foreach ($input as $k => $val)
            if (is_array($val))
                $input[$k] = mb_convert_encoding($val, 'UTF-8', 'UTF-8');

            elseif (is_string($val))
                $input[$k] = utf8_encode($val);

        return $input;
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    function utf8(mixed $data): mixed {
        if (is_string($data))
            return mb_convert_encoding($data, 'utf-8');

        if (is_array($data))
            return array_map('utf8', $data);

        return $data;
    }

    /**
     * @param array $entries
     * @param bool  $sort
     *
     * @return string
     */
    function toJSON(array $entries, bool $sort = true): string {
        if ($sort)
            ksort($entries);

        try {
            $data = json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR, 1024);
        }
        catch (\JsonException) {
            echo "JsonException !\n";
            $entries = utf8($entries);
            $data    = json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR, 1024);
        }

        $data = preg_replace_callback('/\[\s++("[^"]*+"|[^{\]\[:]+)\s++]/u', static function ($match) {
            $string = $match[1];
            $string = explode(",\n", $string);
            $string = array_map('trim', $string);
            $string = implode(', ', $string);

            return "[{$string}]";
        }, $data);

        if ($data === null)
            throw new RuntimeException("Regex failed with '" . preg_last_error() . "': " . preg_last_error_msg());

        return $data;
    }