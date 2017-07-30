<?php

namespace Tests\Functional;

class CssGeneratorTest extends BaseTestCase
{
    /**
     * Test that the route returns a CSS file response
     */
    public function testStatusAndContentType()
    {
        $response = $this->runApp('GET', '/css?family=Open+Sans:400,700');

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue((bool)preg_match('~^text/css(;|$)~', $response->getHeaderLine('Content-Type')));
    }

    /**
     * Tests that the route returns an error status with bad requests
     */
    public function testBadRequests()
    {
        $this->assertEquals(422, $this->runApp('GET', '/css?family=')->getStatusCode());
        $this->assertEquals(422, $this->runApp('GET', '/css?family=|Open+Sans:400,700')->getStatusCode());
        $this->assertEquals(422, $this->runApp('GET', '/css?family=:400,700')->getStatusCode());
        $this->assertEquals(422, $this->runApp('GET', '/css?family=Open+Sans:foo')->getStatusCode());
    }
}
