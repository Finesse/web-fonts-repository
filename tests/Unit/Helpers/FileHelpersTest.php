<?php

namespace Tests\Unit\Helpers;

use Src\Helpers\FileHelpers;
use Tests\BaseTestCase;

class FileHelpersTest extends BaseTestCase
{
    public function testConcatPath()
    {
        $this->assertEquals('../dir/subdir/foo/bar', FileHelpers::concatPath('../dir/subdir/', '/foo', null, 'bar/'));
        $this->assertEquals('http://google.com/search', FileHelpers::concatPath('http://google.com/', '/search', null));
        $this->assertEquals('C:/windows\\system32', FileHelpers::concatPath(null, 'C:\\', '\\windows\\system32'));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Argument #1 expected to be string or null, double given.');
        FileHelpers::concatPath('../fir/subdir/', 1.5, '/bar');
    }
}
