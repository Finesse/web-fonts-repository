<?php

namespace Tests\Functional;

use Psr\Log\NullLogger;
use Src\Services\WebfontCSSGenerator\Exceptions\InvalidSettingsException;
use Src\Services\WebfontCSSGenerator\WebfontCSSGenerator;

class CssGeneratorTest extends FunctionalTestCase
{
    /**
     * Test that the route returns a CSS file response
     */
    public function testHeaders()
    {
        $response = $this->runApp('GET', '/css?family=Open+Sans:400,700');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue((bool)preg_match('~^text/css(;|$)~', $response->getHeaderLine('Content-Type')));
        $this->assertTrue($response->hasHeader('Cache-Control'));
        $this->assertTrue($response->hasHeader('Pragma'));

        $this->assertEquals(200, $this->runApp('GET', '/css?family=Open+Sans')->getStatusCode());
    }

    /**
     * Tests that the route returns an client error status with bad requests
     */
    public function testBadRequests()
    {
        $this->assertEquals(422, $this->runApp('GET', '/css?family=')->getStatusCode());
        $this->assertEquals(422, $this->runApp('GET', '/css?family=|Open+Sans:400,700')->getStatusCode());
        $this->assertEquals(422, $this->runApp('GET', '/css?family=:400,700')->getStatusCode());
        $this->assertEquals(422, $this->runApp('GET', '/css')->getStatusCode());
        $this->assertEquals(422, $this->runApp('GET', '/css?family=Open+Sans:400,700&display[]=bad')->getStatusCode());

        $app = $this->makeApp();
        $container = $app->getContainer();
        $container['webfontCSSGenerator'] = function () {
            $generator = \Mockery::mock(WebfontCSSGenerator::class);
            $generator->shouldReceive('makeCSS')->once()->andThrow(new \InvalidArgumentException('test'));
            return $generator;
        };

        $this->assertEquals(422, $this->runSpecificApp($app, 'GET', '/css?family=Open+Sans:400,700')->getStatusCode());
    }

    /**
     * Tests that the route returns an server error status with bad configuration
     */
    public function testServerError()
    {
        $app = $this->makeApp();
        $container = $app->getContainer();
        $container['webfontCSSGenerator'] = function () {
            throw new InvalidSettingsException('The fonts settings have been FUBAR');
        };
        $container['logger'] = function () {
            return new NullLogger();
        };

        $this->assertEquals(500, $this->runSpecificApp($app, 'GET', '/css?family=Open+Sans:400,700')->getStatusCode());
    }
}
