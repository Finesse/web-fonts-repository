<?php

namespace Tests\Unit\WebfontCSSGenerator;

use Src\Services\WebfontCSSGenerator\Exceptions\InvalidSettingsException;
use Src\Services\WebfontCSSGenerator\Models\Style;
use Tests\BaseTestCase;

class StyleTest extends BaseTestCase
{
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
        $this->assertAttributes([
            'weight' => 500,
            'isItalic' => true,
            'forbidLocalSource' => false,
            'directory' => 'style/directory',
            'filesList' => [
                'subdir/font_medium.eot',
                'subdir/font_medium.ttf',
                'subdir/font_medium.woff',
                'subdir/font_medium.woff2',
                'subdir/font_medium.svg'
            ],
            'filesGlob' => null
        ], $style);

        $style = Style::createFromSettings(800, [
            'files' => '\\subdir\\font_heavy.*'
        ]);
        $this->assertAttributes([
            'weight' => 800,
            'isItalic' => false,
            'forbidLocalSource' => null,
            'directory' => null,
            'filesList' => [],
            'filesGlob' => 'subdir/font_heavy.*'
        ], $style);

        $style = Style::createFromSettings('100i', 'subdir\\font_thin.*');
        $this->assertAttributes([
            'weight' => 100,
            'isItalic' => true,
            'forbidLocalSource' => null,
            'directory' => null,
            'filesList' => [],
            'filesGlob' => 'subdir/font_thin.*'
        ], $style);

        // Incorrect name
        $this->assertException(InvalidSettingsException::class, function () {
            Style::createFromSettings('foo', 'font.*');
        }, function (\Throwable $exception) {
            $this->assertEquals('The style name `foo` has invalid format.', $exception->getMessage());
        });

        // Incorrect settings argument type
        $this->assertException(InvalidSettingsException::class, function () {
            Style::createFromSettings('400', 12345);
        }, function (\Throwable $exception) {
            $this->assertEquals(
                'The $settings argument must be array or string, integer given.',
                $exception->getMessage()
            );
        });

        // Incorrect directory
        $this->assertException(InvalidSettingsException::class, function () {
            Style::createFromSettings('400', [
                'directory' => ['foo', 'bar'],
                'files' => 'font.*'
            ]);
        }, function (\Throwable $exception) {
            $this->assertEquals(
                'The $settings[directory] argument must be string or null, array given.',
                $exception->getMessage()
            );
        });

        // Incorrect files setting
        $this->assertException(InvalidSettingsException::class, function () {
            Style::createFromSettings('400', [
                'directory' => 'dir',
                'files' => new \StdClass()
            ]);
        }, function (\Throwable $exception) {
            $this->assertEquals(
                'The $settings[files] argument must be array or string, object given.',
                $exception->getMessage()
            );
        });

        // Incorrect file settings
        $this->assertException(InvalidSettingsException::class, function () {
            Style::createFromSettings('400', [
                'directory' => 'dir',
                'files' => [true, 'file']
            ]);
        }, function (\Throwable $exception) {
            $this->assertEquals(
                'The $settings[files][0] argument must be array or string, boolean given.',
                $exception->getMessage()
            );
        });
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
            ], $style->getFilesInDirectory($testDirectory), '', 0.0, 10, true);
        } finally {
            @unlink($testDirectory.'/test-font.ttf');
            @unlink($testDirectory.'/test-font.woff2');
            @unlink($testDirectory.'/test-font.eot');
            @unlink($testDirectory.'/test-font.svg');
            @rmdir($testDirectory);
        }
    }
}
