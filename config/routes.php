<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/*
 * Routes
 *
 * Docs: https://www.slimframework.com/docs/objects/router.html
 */

// CSS generator
$app->get('/css', \Src\Controllers\CSSGeneratorController::class)->setName('cssGenerator');

// Index page
$app->get('/', function (RequestInterface $request, ResponseInterface $response) use ($app) {
    return $this->renderer->render($response, 'index.phtml', [
        'cssApiUrl' => $app->getContainer()->get('router')->pathFor('cssGenerator')
    ]);
});
