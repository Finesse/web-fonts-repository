<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;
use Src\Helpers\CSSHelpers;

class CSSHelpersTest extends TestCase
{
    public function testFormatString()
    {
        $this->assertEquals("'foo\\'bar'", CSSHelpers::formatString("foo'bar"));
        $this->assertEquals("'foo\\\\bar'", CSSHelpers::formatString("foo\\bar"));
        $this->assertEquals("'foo\\\nbar'", CSSHelpers::formatString("foo\nbar"));
        $this->assertEquals("'foo\\'bar\\\\baq\\\nbaz'", CSSHelpers::formatString("foo'bar\\baq\nbaz"));
    }
}
