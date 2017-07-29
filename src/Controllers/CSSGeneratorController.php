<?php

namespace Src\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class CSSGeneratorController
 *
 * The controller for generating webfonts CSS files.
 *
 * @author Finesse
 * @package Src\Controllers
 */
class CSSGeneratorController
{
    /**
     * @var ContainerInterface Dependencies container
     */
    protected $container;

    /**
     * @param ContainerInterface $container Dependencies container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Runs the controller action.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function __invoke(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response = $response->withHeader('Content-Type', 'text/css; charset=UTF-8');

        $body = $response->getBody();
        $body->write('body {color: #555;}');

        return $response;
    }
}
