<?php

use Psr\Container\ContainerInterface;

/*
 * DIC configuration
 *
 * Docs: https://www.slimframework.com/docs/concepts/di.html
 */

$container = $app->getContainer();

// error handlers
$container['phpErrorHandler'] = function (ContainerInterface $c) {
    return new \Src\Handlers\PhpError($c->get('settings')['displayErrorDetails'], $c->get('logger'));
};
$container['errorHandler'] = function (ContainerInterface $c) {
    return $c->get('phpErrorHandler');
};

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

// webfonts css code generator
$container['webfontCSSGenerator'] = function (ContainerInterface $c) {
    $fonts = $c->get('settings')['fonts'];
    $request = $c->get('request');
    return new \Src\Services\WebfontCSSGenerator($fonts, $request->getUri()->getBasePath());
};
