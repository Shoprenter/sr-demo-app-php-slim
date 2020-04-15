<?php

namespace App\Service;

use function file_get_contents;
use function file_put_contents;
use function json_decode;

class AuthLoader
{
    /**
     * @param string $shopName
     * @return array
     */
    public static function get($shopName)
    {
        return json_decode(file_get_contents($shopName . '_auth.txt'), true);
    }

    /**
     * @param string $shopName
     * @param string $content
     * @return void
     */
    public static function save($shopName, $content)
    {
        file_put_contents($shopName . '_auth.txt', $content);
    }
}
