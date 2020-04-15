<?php

namespace App\Controller;

use App\Service\ApiClient;
use App\Service\AuthLoader;
use App\Service\Curl;
use App\Service\HmacHandler;
use App\Service\UrlProvider;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;

class HomeController
{
    /**
     * @var PhpRenderer
     */
    private $renderer;

    /**
     * @var HmacHandler
     */
    private $hmacHandler;

    /**
     * @var array
     */
    private $settings;

    /**
     * @var UrlProvider
     */
    private $urlProvider;

    /**
     * HomeController constructor.
     *
     * @param PhpRenderer $renderer
     * @param HmacHandler $hmacHandler
     * @param array $settings
     * @param UrlProvider $urlProvider
     */
    public function __construct(
        $renderer,
        HmacHandler $hmacHandler,
        array $settings,
        UrlProvider $urlProvider
    ) {
        $this->renderer = $renderer;
        $this->hmacHandler = $hmacHandler;
        $this->settings = $settings;
        $this->urlProvider = $urlProvider;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return ResponseInterface
     */
    public function entryAction(Request $request, Response $response, $args): ResponseInterface
    {
        $clientId = $this->settings['client_id'];
        $clientSecret = $this->settings['client_secret'];
        $redirectUri = UrlProvider::getRedirectUri(
            $request->getUri()->getScheme(),
            $request->getUri()->getHost()
        );

        $shopName = $request->getQueryParam('shopname');
        $code = $request->getQueryParam('code');
        $timestamp = $request->getQueryParam('timestamp');
        $hmac = $request->getQueryParam('hmac');

        $generatedHmac = $this->hmacHandler->generate($clientSecret, $shopName, $code, $timestamp);

        if (!$this->hmacHandler->isValid($generatedHmac, $hmac)) {
            return $response->withRedirect('/failed');
        }

        if (!is_file($shopName.'_auth.txt')) {
            return $response->withRedirect($this->urlProvider->getAuthorizeUrl($shopName, $clientId, $redirectUri));
        }

        return $this->renderer->render($response, 'entry.phtml', [
            'shopName' => $shopName
        ]);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return ResponseInterface
     */
    public function authAction(Request $request, Response $response, $args): ResponseInterface
    {
        $clientId = $this->settings['client_id'];
        $clientSecret = $this->settings['client_secret'];
        $appId = $this->settings['app_id'];

        $shopName = $request->getQueryParam('shopname');
        $code = $request->getQueryParam('code');
        $timestamp = $request->getQueryParam('timestamp');
        $hmac = $request->getQueryParam('hmac');

        $generatedHmac = $this->hmacHandler->generate($clientSecret, $shopName, $code, $timestamp);

        if (!$this->hmacHandler->isValid($generatedHmac, $hmac)) {
            return $response->withRedirect('/failed');
        }

        $options = [
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => '',       // handle all encodings
            CURLOPT_USERAGENT      => 'spider', // who am i
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            CURLOPT_POST           => 1,
            CURLOPT_SSL_VERIFYPEER => false,    // Disabled SSL Cert checks
            CURLOPT_SSL_VERIFYHOST => false,    // Disabled SSL Cert checks
            CURLOPT_POSTFIELDS     => sprintf('client_id=%s&client_secret=%s&code=%s&timestamp=%s&hmac=%s', $clientId, $clientSecret, $code, $timestamp, $hmac)
        ];

        $curl = new Curl($this->urlProvider->getAccessCredentialUrl($shopName));
        $curl->setOptions($options);
        $content = $curl->exec();

        AuthLoader::save($shopName, $content);

        return $response->withRedirect($this->urlProvider->getAppSiteUrl($shopName, $appId));
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return ResponseInterface
     */
    public function failedAction(Request $request, Response $response, $args): ResponseInterface
    {
        return $this->renderer->render($response, 'failed.phtml');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return ResponseInterface
     */
    public function startRecurringChargeAction(Request $request, Response $response, $args): ResponseInterface
    {
        $shopName = $request->getQueryParam('shopName');

        $cred = AuthLoader::get($shopName);

        $client = new ApiClient($cred);

        $get = $client->send(
            $this->urlProvider->getApiRoute($shopName, 'billing/recurringCharges'),
            'POST',
            $this->settings['recurringCharge']
        );

        return $response->withJson($get);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return ResponseInterface
     */
    public function startOneTimeChargeAction(Request $request, Response $response, $args): ResponseInterface
    {
        $shopName = $request->getQueryParam('shopName');

        $cred = AuthLoader::get($shopName);

        $client = new ApiClient($cred);

        $get = $client->send(
            $this->urlProvider->getApiRoute($shopName, 'billing/oneTimeCharges'),
            'POST',
            $this->settings['oneTimeCharge']
        );

        return $response->withJson($get);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return ResponseInterface
     */
    public function paymentSuccessAction(Request $request, Response $response, $args): ResponseInterface
    {
        return $this->renderer->render($response, 'payment_success.phtml');
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @return ResponseInterface
     */
    public function paymentFailedAction(Request $request, Response $response, $args): ResponseInterface
    {
        return $this->renderer->render($response, 'payment_failed.phtml');
    }
}
