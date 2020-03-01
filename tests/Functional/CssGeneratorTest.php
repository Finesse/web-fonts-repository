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
        $response = $this->runApp('GET', '/css?family=Open+Sans');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue((bool)preg_match('~^text/css(;|$)~', $response->getHeaderLine('Content-Type')));
        $this->assertTrue($response->hasHeader('Cache-Control'));
        $this->assertTrue($response->hasHeader('Pragma'));
    }

    /**
     * Test the controller `family` parameter parsing
     */
    public function testFamilyParsing()
    {
        $app = $this->makeApp();
        $container = $app->getContainer();
        $container['webfontCSSGenerator'] = function () {
            $generator = \Mockery::mock(WebfontCSSGenerator::class);
            $generator->shouldReceive('makeCSS')
                ->once()
                ->with(['Roboto' => ['400']], '')
                ->andReturn('');
            $generator->shouldReceive('makeCSS')
                ->once()
                ->with([
                    'Open Sans' => ['400'],
                    'Roboto' => ['400', '400i', '700i'],
                ], '')
                ->andReturn('');
            return $generator;
        };

        $this->assertEquals(200, $this->runSpecificApp($app, 'GET', '/css?family=Roboto')->getStatusCode());
        $this->assertEquals(200, $this->runSpecificApp($app, 'GET', '/css?family=Open+Sans|Roboto:400,400i,700i')->getStatusCode());

        $this->assertEquals(422, $this->runSpecificApp($app, 'GET', '/css?family=')->getStatusCode());
        $this->assertEquals(422, $this->runSpecificApp($app, 'GET', '/css?family=|Open+Sans:400,700')->getStatusCode());
        $this->assertEquals(422, $this->runSpecificApp($app, 'GET', '/css?family=:400,700')->getStatusCode());
        $this->assertEquals(422, $this->runSpecificApp($app, 'GET', '/css')->getStatusCode());
    }

    /**
     * Test the controller `display` parameter parsing
     */
    public function testDisplayParsing()
    {
        $app = $this->makeApp();
        $container = $app->getContainer();
        $container['webfontCSSGenerator'] = function () {
            $generator = \Mockery::mock(WebfontCSSGenerator::class);
            $generator->shouldReceive('makeCSS')
                ->once()
                ->with(['Open Sans' => ['400']], '')
                ->andReturn('');
            $generator->shouldReceive('makeCSS')
                ->once()
                ->with(['Open Sans' => ['400']], '')
                ->andReturn('');
            $generator->shouldReceive('makeCSS')
                ->once()
                ->with(['Open Sans' => ['400']], 'swap')
                ->andReturn('');
            return $generator;
        };

        $this->assertEquals(200, $this->runSpecificApp($app, 'GET', '/css?family=Open+Sans')->getStatusCode());
        $this->assertEquals(200, $this->runSpecificApp($app, 'GET', '/css?family=Open+Sans&display=')->getStatusCode());
        $this->assertEquals(200, $this->runSpecificApp($app, 'GET', '/css?family=Open+Sans&display=swap')->getStatusCode());
        $this->assertEquals(422, $this->runSpecificApp($app, 'GET', '/css?family=Open+Sans&display[]=bad')->getStatusCode());
    }

    /**
     * Test handling errors from webfontCSSGenerator
     */
    public function testGeneratorInputError()
    {
        $app = $this->makeApp();
        $container = $app->getContainer();
        $container['webfontCSSGenerator'] = function () {
            $generator = \Mockery::mock(WebfontCSSGenerator::class);
            $generator->shouldReceive('makeCSS')->once()->andThrow(new \InvalidArgumentException('test'));
            return $generator;
        };

        $this->assertEquals(422, $this->runSpecificApp($app, 'GET', '/css?family=Open+Sans')->getStatusCode());
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
