<?php

namespace Tests\Unit\WebfontCSSGenerator;

use PHPUnit\Framework\TestCase;
use Src\Services\WebfontCSSGenerator\Exceptions\InvalidSettingsException;
use Src\Services\WebfontCSSGenerator\Models\Style;

class StyleTest extends TestCase
{
    public function testIncorrectName()
    {
        $this->expectException(InvalidSettingsException::class);
        Style::createFromSettings('foo', 'font.*');
    }

    public function testIncorrectDirectory()
    {
        $this->expectException(InvalidSettingsException::class);
        Style::createFromSettings('400', [
            'directory' => ['foo', 'bar'],
            'files' => 'font.*'
        ]);
    }

    public function testCreateFromSettings()
    {
        $style = Style::createFromSettings('500i', [
            'forbidLocal' => false,
            'directory' => '\\style\\directory\\',
            'files' => [
                '\\subdir\\font_medium.eot',
                '\\subdir\\font_medium.ttf',
                '\\subdir\\font_medium.woff',
                '\\subdir\\font_medium.woff2',
                '\\subdir\\font_medium.svg'
            ]
        ]);
        $this->assertAttributeEquals(500, 'weight', $style);
        $this->assertAttributeEquals(true, 'isItalic', $style);
        $this->assertAttributeEquals(false, 'forbidLocalSource', $style);
        $this->assertAttributeEquals('style/directory', 'directory', $style);
        $this->assertAttributeEquals([
            'subdir/font_medium.eot',
            'subdir/font_medium.ttf',
            'subdir/font_medium.woff',
            'subdir/font_medium.woff2',
            'subdir/font_medium.svg'
        ], 'filesList', $style);
        $this->assertAttributeEquals(null, 'filesGlob', $style);

        $style = Style::createFromSettings(800, [
            'files' => '\\subdir\\font_heavy.*'
        ]);
        $this->assertAttributeEquals(800, 'weight', $style);
        $this->assertAttributeEquals(false, 'isItalic', $style);
        $this->assertAttributeEquals(null, 'forbidLocalSource', $style);
        $this->assertAttributeEquals(null, 'directory', $style);
        $this->assertAttributeEquals([], 'filesList', $style);
        $this->assertAttributeEquals('subdir/font_heavy.*', 'filesGlob', $style);

        $style = Style::createFromSettings('100i', 'subdir\\font_thin.*');
        $this->assertAttributeEquals(100, 'weight', $style);
        $this->assertAttributeEquals(true, 'isItalic', $style);
        $this->assertAttributeEquals(null, 'forbidLocalSource', $style);
        $this->assertAttributeEquals(null, 'directory', $style);
        $this->assertAttributeEquals([], 'filesList', $style);
        $this->assertAttributeEquals('subdir/font_thin.*', 'filesGlob', $style);
    }

    public function testGetName()
    {
        $style = new Style();
        $style->weight = 300;
        $style->isItalic = true;
        $this->assertEquals('300i', $style->getName());

        $style = new Style();
        $style->weight = 500;
        $style->isItalic = false;
        $this->assertEquals('500', $style->getName());

        $style = new Style();
        $this->assertEquals('400', $style->getName());
    }

    public function testGetFilesInDirectory()
    {
        try {
            $testDirectory = __DIR__.'/../../../public/fonts/__temp-for-test';
            mkdir($testDirectory);
            touch($testDirectory.'/test-font.ttf');
            touch($testDirectory.'/test-font.woff2');
            touch($testDirectory.'/test-font.eot');
            touch($testDirectory.'/test-font.svg');

            $style = new Style();
            $style->filesList[] = 'demo-demo.svg';
            $style->filesList[] = 'fubar.woff';
            $style->filesGlob = 'test-font.*';

            // https://stackoverflow.com/a/28189403/1118709
            $this->assertEquals([
                'demo-demo.svg',
                'fubar.woff',
                'test-font.ttf',
                'test-font.woff2',
                'test-font.eot',
                'test-font.svg'
            ], $style->getFilesInDirectory($testDirectory), "\$canonicalize = true", $delta = 0.0, $maxDepth = 10, $canonicalize = true);
        } finally {
            @unlink($testDirectory.'/test-font.ttf');
            @unlink($testDirectory.'/test-font.woff2');
            @unlink($testDirectory.'/test-font.eot');
            @unlink($testDirectory.'/test-font.svg');
            rmdir($testDirectory);
        }
    }
}
