<?php

namespace Tests\Unit\Helpers;

use Src\Helpers\CSSHelpers;
use Tests\BaseTestCase;

class CSSHelpersTest extends BaseTestCase
{
    public function testFormatString()
    {
        $this->assertEquals("'foo\\'bar'", CSSHelpers::formatString("foo'bar"));
        $this->assertEquals("'foo\\\\bar'", CSSHelpers::formatString("foo\\bar"));
        $this->assertEquals("'foo\\\nbar'", CSSHelpers::formatString("foo\nbar"));
        $this->assertEquals("'foo\\'bar\\\\baq\\\nbaz'", CSSHelpers::formatString("foo'bar\\baq\nbaz"));
    }
}
