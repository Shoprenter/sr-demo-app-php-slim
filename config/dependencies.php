<?php

// DIC configuration

use App\Controller\HomeController;
use App\Service\HmacHandler;
use App\Service\UrlProvider;
use Psr\Container\ContainerInterface;

$container = $app->getContainer();

// view renderer
$container['renderer'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

// monolog
$container['logger'] = function (ContainerInterface $c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

# -----------------------------------------------------------------------------
# Controllers
# -----------------------------------------------------------------------------
$container[HomeController::class] = function (ContainerInterface $c) {
    return new HomeController(
        $c->get('renderer'),
        $c->get(HmacHandler::class),
        $c->get('settings')['app'],
        $c->get(UrlProvider::class)
    );
};

# -----------------------------------------------------------------------------
# Services
# -----------------------------------------------------------------------------
$container[HmacHandler::class] = function (ContainerInterface $c) {
    return new HmacHandler();
};

$container[UrlProvider::class] = function (ContainerInterface $c) {
    return new UrlProvider($c->get('settings')['app']['shoprenter_url']);
};
