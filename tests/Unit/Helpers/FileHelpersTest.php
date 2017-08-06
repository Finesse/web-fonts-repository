<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;
use Src\Helpers\FileHelpers;

class FileHelpersTest extends TestCase
{
    public function testConcatPath()
    {
        $this->assertEquals('../dir/subdir/foo/bar', FileHelpers::concatPath('../dir/subdir/', '/foo', null, 'bar/'));
        $this->assertEquals('http://google.com/search', FileHelpers::concatPath('http://google.com/', '/search', null));
        $this->assertEquals('C:/windows\\system32', FileHelpers::concatPath(null, 'C:\\', '\\windows\\system32'));
    }
}
