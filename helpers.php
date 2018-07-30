<?php
    /**
     * User: aEnigma
     * Date: 24.01.2017
     * Time: 17:40
     */

    use Sol\FFBE\GameFile;
    use Sol\FFBE\Strings;

    function readArray($input, $delim, $force) {
        if (is_int($input) || is_string($input))
            if (strpos($input, $delim) === false)
                if ($force)
                    return [$input];
                else
                    return $input;
            else
                return explode($delim, $input);

        if (!is_array($input))
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

        $addVal = function ($path, $name, $val) use (&$vals, &$addVal, $array_fields) {
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

        $vals = array_map(function ($arr) {
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

    /**
     * @param $string
     *
     * @return array
     */
    function parseReward($string) {
        $rest = explode(':', $string);
        $type = array_shift($rest);
        $id   = array_shift($rest);
        $num  = array_shift($rest);

        switch ($type) {
            case 10:
                // Unit
                $mst_name = 'MST_UNIT';
                $type     = 'UNIT';
                break;

            case 23:
                // key item
                $mst_name = 'MST_IMPORTANT_ITEM';
                $type     = 'KEYITEM';
                break;

            case 20:
                $mst_name = 'MST_ITEM';
                $type     = 'ITEM';
                break;

            case 21:
                // equipment
                $mst_name = 'MST_EQUIP_ITEM';
                $type     = 'EQUIP';
                break;

            case 22:
                // materia
                $mst_name = 'MST_MATERIA';
                $type     = 'MATERIA';
                break;

            case 50:
                // Lapis? currency?
                $name     = 'Lapis';
                $mst_name = 'CURRENCY';
                $type     = 'LAPIS';
                break;

            case 51:
                // Gil?
                $name     = 'Gil';
                $mst_name = 'CURRENCY';
                $type     = 'GIL';
                break;

            case 60:
                // recipe
                $mst_name = 'MST_RECIPE_BOOK';
                $str_db   = 'MST_RECIPEBOOK_NAME';
                $type     = 'RECIPE';
                break;

            default:
                return ['ERR1', $type, $string, $id];
                var_dump(['ERR1', $type, $id, $string]);
                die();
        }

        if (!isset($str_db))
            $str_db = "{$mst_name}_NAME";

        if (!isset($name))
            $name = Strings::getString($str_db, $id, 0);

        if ($name == null) {
            $items = GameFile::loadMst('F_' . substr($mst_name, 4) . '_MST');
            $items = array_combine(
                array_map("current", $items),
                $items
            );
            $name  = $items[$id]['name'] ?? null;
        }

        return [$type, $id, $name, $num, $rest];
    }

    function readTM($string) {
        if ($string == '')
            return null;

        list($type, $id) = parseReward($string);
        //        $strings = [];
        //        foreach (['NAME', 'SHORTDESCRIPTION', 'LONGDESCRIPTION'] as $k => $str_type)
        //            $strings[] = Sol\FFBE\Strings::getString("{$str_db}_{$str_type}", $id, 0);
        //            foreach (Sol\FFBE\Strings::readStrings("{$str_db}_{$str_type}", $id) as $j => $str)
        //                $strings[$j][$k] = $str;

        return [$type, (int) $id];
        //
        //        return [
        //            'type' => $type,
        //            'id'   => (int)$id,
        //            'name' => Sol\FFBE\Strings::getString("{$str_db}_NAME", $id, 0),
        //            'desc' => Sol\FFBE\Strings::getString("{$str_db}_LONGDESCRIPTION", $id, 0) ??
        //                      Sol\FFBE\Strings::getString("{$str_db}_SHORTDESCRIPTION", $id, 0),
        //
        //        ];
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

        $entry['attack_count']  = array_map(function ($frames) { return count($frames); }, $frames);
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
                } else {
                    if ($mValue != $aArray2[$mKey]) {
                        $aReturn[$mKey] = $mValue;
                    }
                }
            } else {
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
    function toJSON(array $entries, $trimStringArrays = true, $sort = true) {
        if ($sort)
            ksort($entries);

        $entries = utf8($entries);
        $data    = json_encode($entries, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, 1024);
        if ($data === false) {
            // var_dump(arrayRecursiveDiff($entries, $test));
            throw new \RuntimeException(json_last_error() . ": " . json_last_error_msg());
        }

        if ($trimStringArrays)
            $data = preg_replace_callback('/\[([^{[]+?)\]/sm', function ($match) {
                $string = $match[1];
                $string = explode(",\n", $string);
                $string = array_map('trim', $string);
                $string = implode(', ', $string);

                return "[{$string}]";
            }, $data);
        else
            // trim only single string arrays
            $data = preg_replace_callback('/\[\s*("?[^"{[]+?"?)\s*\]/sm', function ($match) {
                $string = $match[1];
                $string = explode(",\n", $string);
                $string = array_map('trim', $string);
                $string = implode(', ', $string);

                return "[{$string}]";
            }, $data);
        //        $data = preg_replace('/([\[\{])\s+([\[\{])/sm', '$1$2', $data);
        //        $data = preg_replace('/([\]\}])\s+([\]\}])/sm', '$1$2', $data);
        //        $data = preg_replace('/([\]\}]),\s+([\[\{])/sm', '$1,$2', $data);

        //        $data = preg_replace_callback('/^.+$/m', function ($match) {
        //            $string = $match[0];
        //
        //            if (strlen($string) < 20)
        //                return $string;
        //
        //            $delim_count = substr_count($string, '[')
        //                           + substr_count($string, '{')
        //                           + substr_count($string, '}')
        //                           + substr_count($string, ']');
        //
        //
        //            if (strlen($string) < 120 && $delim_count < 4)
        //                return $string;
        //
        //            $lines = preg_split('~((?<!\\\\)"?[\]\}]+,)~', $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        //            if (count($lines) == 1)
        //                return $string;
        //
        //            $lines   = array_chunk($lines, 2);
        //            $string  = $lines[0][0] . $lines[0][1];
        //            $pos     = strrpos($lines[0][0], "[") + 1;
        //            $padding = str_pad("\n", $pos, ' ', STR_PAD_RIGHT);
        //            unset($lines[0]);
        //
        //
        //            foreach ($lines as $line) {
        //                $delim = $line[1] ?? '';
        //                $line  = $line[0];
        //
        //                if (strlen($delim) > 3)
        //                    $delim = $delim[0] . $padding . substr($delim, 1);
        //
        //                $string .= $padding . $line . $delim;
        //
        //            }
        //
        //            return $string;
        //        }, $data);
        //
        //        $data = preg_replace('~},(\s+)([{"])~', '$1},$1$2', $data);

        // trim inner padding of int only arrays

        return $data;
    }