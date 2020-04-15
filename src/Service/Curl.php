<?php

namespace App\Service;

use RuntimeException;
use function curl_close;
use function curl_errno;
use function curl_error;
use function curl_exec;
use function curl_getinfo;
use function curl_init;
use function curl_setopt_array;
use function sprintf;

class Curl
{
    /**
     * @var resource
     */
    protected $ch;

    /**
     * @var mixed
     */
    protected $header;

    /**
     * @var string
     */
    protected $error;

    /**
     * Curl constructor.
     * @param string $url
     */
    public function __construct($url)
    {
        $ch = curl_init($url);

        if ($ch === false) {
            throw new RuntimeException('cURL init error: '.$url);
        }

        $this->ch = $ch;
    }

    /**
     * @param array $options
     * @return bool
     */
    public function setOptions(array $options)
    {
        return curl_setopt_array($this->ch, $options);
    }

    /**
     * @return string
     */
    public function exec()
    {
        $this->cleanUp();

        $content = curl_exec($this->ch);
        $this->header = curl_getinfo($this->ch);
        $errorNumber = curl_errno($this->ch);

        if ($errorNumber !== 0) {
            $this->error = sprintf('cURL error: [%s] %s', $errorNumber, curl_error($this->ch));
        }

        curl_close($this->ch);

        if ($content === false) {
            throw new RuntimeException('cURL error: '.$this->error);
        }

        return $content;
    }

    /**
     * @return mixed
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->header['content_type'] ?? null;
    }

    /**
     * @return string
     */
    public function getHttpCode()
    {
        return $this->header['http_code'] ?? 500;
    }

    /**
     * @param int $code
     * @return string
     */
    public function isHttpCode($code)
    {
        return $this->getHttpCode() === $code;
    }

    /**
     * @return void
     */
    public function cleanUp()
    {
        $this->error = null;
        $this->header = null;
    }
}
