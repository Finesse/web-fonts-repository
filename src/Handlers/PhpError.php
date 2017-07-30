<?php

namespace Src\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Handlers\PhpError as BasePhpError;

/**
 * Class Error
 *
 * Custom error handler that writes error messages to logger.
 *
 * @author Finesse
 * @package Src\Handlers
 */
class PhpError extends BasePhpError
{
    /**
     * @var LoggerInterface Logger for writing error messages
     */
    protected $logger;

    /**
     * {@inheritDoc}
     * @param LoggerInterface $logger A logger for writing error messages
     */
    public function __construct(bool $displayErrorDetails = false, LoggerInterface $logger)
    {
        parent::__construct($displayErrorDetails);
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \Throwable $error)
    {
        // Log the message
        $this->logger->error($error);
        return parent::__invoke($request, $response, $error);
    }
}
