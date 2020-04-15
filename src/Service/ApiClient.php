<?php

namespace App\Service;

use function json_encode;
use const CURLAUTH_BASIC;
use const CURLOPT_AUTOREFERER;
use const CURLOPT_CONNECTTIMEOUT;
use const CURLOPT_CUSTOMREQUEST;
use const CURLOPT_ENCODING;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_HEADER;
use const CURLOPT_HTTPAUTH;
use const CURLOPT_HTTPHEADER;
use const CURLOPT_MAXREDIRS;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_SSL_VERIFYHOST;
use const CURLOPT_SSL_VERIFYPEER;
use const CURLOPT_TIMEOUT;
use const CURLOPT_USERAGENT;
use const CURLOPT_USERPWD;

class ApiClient
{
    /**
     * @var array
     */
    private $cred;

    /**
     * ApiClient constructor.
     * @param array $cred
     */
    public function __construct(array $cred)
    {
        $this->cred = $cred;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $data
     * @return array
     */
    public function send($url, $method, $data = [])
    {
        $curl = new Curl($url);

        $payload = json_encode(['data' => $data]);

        $curl->setOptions([
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => '',       // handle all encodings
            CURLOPT_USERAGENT      => 'spider', // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false,    // Disabled SSL Cert checks
            CURLOPT_SSL_VERIFYHOST => false,    // Disabled SSL Cert checks
            CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
            CURLOPT_USERPWD        => $this->getUserPwd(),
            CURLOPT_HTTPHEADER     => ['Accept: application/json', 'Content-Type: application/json'],
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $payload
        ]);

        return json_decode($curl->exec(), true);
    }

    /**
     * @return string
     */
    private function getUserPwd()
    {
        return $this->cred['username'] . ':' . $this->cred['password'];
    }
}
