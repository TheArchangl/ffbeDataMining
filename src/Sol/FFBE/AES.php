<?php
    /**
     * User: aEnigma
     * Date: 14.03.2017
     * Time: 18:08
     */

    namespace Sol\FFBE;


    class AES {
        public static function encodeGameFile($data, $key) {
            $data = preg_split("~}\s*\r?\n~", $data, -1, PREG_SPLIT_NO_EMPTY);
            $data = array_map(function ($line) use ($key) { return static::encode("{$line}}", $key); }, $data);

            return join("\n", $data);
        }

        public static function encode($s, $key) {
            $key = str_pad($key, 16, "\x00", STR_PAD_RIGHT);
            $key = substr($key, 0, 16);

            $s = static::pkcs5_pad($s, 16);
            $s = openssl_encrypt($s,'aes-128-ecb', $key);
            $s = base64_encode($s);


            return $s;
        }

        public static function decode($s, $key) {
            $key = str_pad($key, 16, "\x00", STR_PAD_RIGHT);
            $key = substr($key, 0, 16);

            $strings = explode("\n", $s);

            $out = [];
            foreach ($strings as $s) {
                if ($s == '')
                    continue;

                $s = base64_decode($s);
                $s = @openssl_decrypt($s,'aes-128-ecb', $key);
                $s = static::pkcs5_unpad($s);

                $out[] = $s;
            }

            return implode("\n", $out);
        }

        protected static function pkcs5_unpad($s) {
            $len = strlen($s);

            return substr($s, 0, $len - ord($s[$len - 1]));
        }

        protected static function pkcs5_pad($text, $blocksize) {
            /** @noinspection PhpMethodParametersCountMismatchInspection */
            $size = 16;
            $pad  = $blocksize - (strlen($text) % $blocksize);

            return $text . str_repeat(chr($pad), $pad);
        }
    }