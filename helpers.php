<?php
    /**
     * User: aEnigma
     * Date: 24.01.2017
     * Time: 17:40
     */

    use Solaris\FFBE\GameHelper;

    function readArray($input, $delim, $force) {
        if (is_int($input) || is_string($input))
            if (strpos($input, $delim) === false)
                if ($force)
                    return [$input];
                else
                    return $input;
            else
                return explode($delim, $input);

        if (! is_array($input))
            throw new \LogicException();

        foreach ($input as $k => $val)
            $input[$k] = readArray($val, $delim, $force);

        return $input;
    }

    function toInt($val) {
        if (is_array($val))
            return array_map('toInt', $val);

        if (is_string($val) && ctype_digit($val))
            return intval($val);

        return $val;
    }

    function readParameters($string, $force_delim = ',') {
        $values = $string;

        foreach (['@', ',', '&', ':'] as $k => $delim) {
            $values = readArray($values, $delim, strpos($force_delim, $delim) !== false);
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
    function arrayGroupValues(array $array, $array_fields = [], $use_names = true) {
        $vals = [];

        $addVal = static function ($path, $name, $val) use (&$vals, &$addVal, $array_fields) {
            if (is_array($val) && in_array($path, $array_fields))
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
            if (count($arr) == 1)
                unset($vals[$key]);

        $vals = array_map(static function ($arr) {
            ksort($arr);

            return $arr;
        }, $vals);
        ksort($vals);

        return $vals;
    }

    function groupValues(array $array) {
        $vals = [];

        foreach ($array as $k => $v) {
            if (is_scalar($v))
                $vals[(string) $v][] = $k;
        }

        return $vals;
    }

    function readTM($string) {
        if ($string == '')
            return null;

        [$type, $id] = GameHelper::parseMstItem($string);
        return [$type, (int) $id];
    }

    function formatStats($entry) {
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

    function readIntArray($str, $delim = ',') {
        if (trim($str) == '')
            return [];

        $str = explode($delim, $str);
        $str = array_map('intval', $str);

        return $str;
    }

    /**
     * @param string[] $data
     * @param string   $char
     *
     * @return string[][]
     */
    function parseListStep(array $data, $char) {
        foreach ($data as $k => $val)
            if (is_array($val))
                $data[$k] = parseListStep($val, $char);

            else
                $data[$k] = explode($char, $val);

        return $data;
    }

    function parseList($string, $chars) {
        $string = [$string];

        $chars = str_split($chars);
        foreach ($chars as $char)
            $string = parseListStep($string, $char);

        return $string[0];
    }

    /**
     * @param array $effects
     *
     * @return array
     */
    function flattenFrames(array $effects) {
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
     * @return mixed
     */
    function parseFrames($data, $entry) {
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

    function readEquip($str) {
        $str = explode(',', $str);
        $str = array_map('intval', $str);
        //        $str = array_map(function ($id) use ($equipment_id) {
        //            return EQUIPMENT_TYPE[$id] ?? $id;
        //        }, $str);

        return $str;
    }

    function recursiveUTF(array $input) {
        foreach ($input as $k => $val)
            if (is_array($val))
                $input[$k] = mb_convert_encoding($val, 'UTF-8', 'UTF-8');

            elseif (is_string($val))
                $input[$k] = utf8_encode($val);

        return $input;
    }

    function utf8($data) {
        if (is_array($data)) {
            foreach ($data as $k => $v)
                $data[$k] = utf8($v);

            return $data;
        }

        if (is_string($data))
            return mb_convert_encoding($data, 'utf-8');

        return $data;
    }

    function arrayRecursiveDiff($aArray1, $aArray2) {
        $aReturn = [];

        foreach ($aArray1 as $mKey => $mValue) {
            if (array_key_exists($mKey, $aArray2)) {
                if (is_array($mValue)) {
                    $aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[$mKey]);
                    if (count($aRecursiveDiff)) {
                        $aReturn[$mKey] = $aRecursiveDiff;
                    }
                }
                else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            }
            else {
                $aReturn[$mKey] = $mValue;
            }
        }

        return $aReturn;
    }

    /**
     * @param array $entries
     * @param bool  $trimStringArrays
     * @param bool  $sort
     *
     * @return string
     */
    function toJSON(array $entries, bool $trimStringArrays = true, bool $sort = true): string {
        if ($sort)
            ksort($entries);

        $entries = utf8($entries);
        $data    = json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR, 1024);

        $data = preg_replace_callback('/\[\s*([^{\]\[:]+)\s*]/u', static function ($match) {
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