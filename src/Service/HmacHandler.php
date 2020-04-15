<?php

namespace App\Service;

use function hash_hmac;
use function sprintf;

class HmacHandler
{
    /**
     * @param string $clientSecret
     * @param string $shopName
     * @param int $code
     * @param int $timestamp
     * @return string
     */
    public function generate($clientSecret, $shopName, $code, $timestamp)
    {
        $queryString = sprintf('shopname=%s&code=%s&timestamp=%s', $shopName, $code, $timestamp);

        return hash_hmac('sha256', $queryString, $clientSecret);
    }

    /**
     * @param string $generatedHmac
     * @param string $hmac
     * @return bool
     */
    public function isValid($generatedHmac, $hmac)
    {
        return $generatedHmac === $hmac;
    }
}
