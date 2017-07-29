<?php

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/*
 * Routes
 */

// CSS generator
$app->get('/css', function (RequestInterface $request, ResponseInterface $response) {

})->setName('cssGenerator');

// Index page
$app->get('/', function (RequestInterface $request, ResponseInterface $response) {
    return $this->renderer->render($response, 'index.phtml', [
        'cssApiUrl' => $app->get('router')
    ]);
});
