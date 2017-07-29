<?php

namespace Tests\Functional;

class CssGeneratorTest extends BaseTestCase
{
    /**
     * Test that the route returns a CSS file response
     */
    public function testStatusAndContentType()
    {
        $response = $this->runApp('GET', '/css');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue((bool)preg_match('~^text/css(;|$)~', $response->getHeaderLine('Content-Type')));
    }
}
