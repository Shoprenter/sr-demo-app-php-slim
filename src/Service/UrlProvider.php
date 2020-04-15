<?php

namespace App\Service;

use function sprintf;

class UrlProvider
{
    /**
     * @var string
     */
    private $url;

    /**
     * UrlProvider constructor.
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * @param string $scheme
     * @param string $host
     * @return string
     */
    public static function getRedirectUri($scheme, $host)
    {
        return sprintf('%s://%s/auth', $scheme, $host);
    }

    /**
     * @param string $shopName
     * @return string
     */
    public function getAccessCredentialUrl($shopName)
    {
        return sprintf('https://%s.%s/admin/oauth/access_credential', $shopName, $this->url);
    }

    /**
     * @param string $shopName
     * @param string $clientId
     * @param string $redirectUri
     * @return string
     */
    public function getAuthorizeUrl($shopName, $clientId, $redirectUri)
    {
        return sprintf(
            'https://%s.%s/admin/oauth/authorize?client_id=%s&redirect_uri=%s',
            $shopName,
            $this->url,
            $clientId,
            $redirectUri
        );
    }

    /**
     * @param string $shopName
     * @param int $appId
     * @return string
     */
    public function getAppSiteUrl($shopName, $appId)
    {
        return sprintf('https://%s.%s/admin/app/%d', $shopName, $this->url, $appId);
    }

    /**
     * @param string $shopName
     * @param string $resource
     * @return string
     */
    public function getApiRoute($shopName, $resource)
    {
        return sprintf('http://%s.api.%s/%s', $shopName, $this->url, $resource);
    }
}
