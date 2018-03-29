<?php

namespace Simple\Support;

class Crypt
{
    /**
     * 加密方法
     */
    const METHOD = 'aes-256-cbc';

    /**
     * 分割符
     */
    const SEA = '@@';

    /**
     * 数据加密
     * @param $data
     * @return string
     */
    public static function encrypt($data)
    {
        $key = $_ENV['app_key'];
        $decodeKey = base64_decode($key);

        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::METHOD));
        $encrypted = openssl_encrypt($data, self::METHOD, $decodeKey, 0, $iv);
        return base64_encode($encrypted . self::SEA . $iv);
    }

    /**
     * 数据加密
     * @param $data
     * @return string
     */
    public static function decrypt($data)
    {
        $key = $_ENV['app_key'];
        $encodeKey = base64_decode($key);

        list($data, $iv) = explode(self::SEA, base64_decode($data), 2);
        return openssl_decrypt($data, self::METHOD, $encodeKey, 0, $iv);
    }
}

