<?php

namespace Tests\Functional;

use Mockery\MockInterface;
use Src\Handlers\PhpError;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Tests\BaseTestCase;

class PhpErrorTest extends BaseTestCase
{
    /**
     * Tests that errors are logged
     */
    public function testLogging()
    {
        /**
         * @var LoggerInterface|MockInterface $logger
         * @var ServerRequestInterface|MockInterface $request
         * @var ResponseInterface|MockInterface $response
         */

        $logger = \Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('error')->twice()->withArgs(function(\Throwable $error) {
            return $error->getMessage() === 'The fonts have been FUBAR';
        });

        $request = \Mockery::mock(ServerRequestInterface::class);
        $request->shouldReceive('getHeaderLine')->andReturn('text/html');

        $response = \Mockery::mock(ResponseInterface::class);
        $response->shouldReceive('withStatus', 'withHeader', 'withBody')->andReturn($response);

        $handler = new PhpError(false, $logger);
        $handler($request, $response, new \Exception('The fonts have been FUBAR'));

        $handler = new PhpError(true, $logger);
        $handler($request, $response, new \Error('The fonts have been FUBAR'));

        $this->assertTrue(true);
    }
}
