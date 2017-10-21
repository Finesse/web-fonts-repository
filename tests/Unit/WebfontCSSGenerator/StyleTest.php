<?php

namespace Tests\Unit\WebfontCSSGenerator;

use Src\Services\WebfontCSSGenerator\Exceptions\InvalidSettingsException;
use Src\Services\WebfontCSSGenerator\Models\Style;
use Tests\BaseTestCase;

class StyleTest extends BaseTestCase
{
    public function testIncorrectName()
    {
        $this->expectException(InvalidSettingsException::class);
        $this->expectExceptionMessage('The style name `foo` has invalid format.');
        Style::createFromSettings('foo', 'font.*');
    }

    public function testIncorrectSettingsArgumentType()
    {
        $this->expectException(InvalidSettingsException::class);
        $this->expectExceptionMessage('The $settings argument must be array or string, integer given.');
        Style::createFromSettings('400', 12345);
    }

    public function testIncorrectDirectory()
    {
        $this->expectException(InvalidSettingsException::class);
        $this->expectExceptionMessage('The $settings[directory] argument must be string or null, array given.');
        Style::createFromSettings('400', [
            'directory' => ['foo', 'bar'],
            'files' => 'font.*'
        ]);
    }

    public function testIncorrectFilesSetting()
    {
        $this->expectException(InvalidSettingsException::class);
        $this->expectExceptionMessage('The $settings[files] argument must be array or string, object given.');
        Style::createFromSettings('400', [
            'directory' => 'dir',
            'files' => new \StdClass()
        ]);
    }

    public function testIncorrectFileSettings()
    {
        $this->expectException(InvalidSettingsException::class);
        $this->expectExceptionMessage('The $settings[files][0] argument must be array or string, boolean given.');
        Style::createFromSettings('400', [
            'directory' => 'dir',
            'files' => [true, 'file']
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
