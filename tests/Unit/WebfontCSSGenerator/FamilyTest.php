<?php

namespace Tests\Unit\WebfontCSSGenerator;

use Src\Services\WebfontCSSGenerator\Exceptions\InvalidSettingsException;
use Src\Services\WebfontCSSGenerator\Models\Family;
use Src\Services\WebfontCSSGenerator\Models\Style;
use Tests\BaseTestCase;

class FamilyTest extends BaseTestCase
{
    public function testCreateFromSettings()
    {
        $family = Family::createFromSettings('Foo bar', [
            'forbidLocal' => true,
            'directory' => '\\example\\dir\\',
            'styles' => [
                '400' => 'font.*',
                '400I' => 'font_italic.*',
                '600' => 'font_semibold.*'
            ]
        ]);
        $this->assertAttributes([
            'name' => 'Foo bar',
            'forbidLocalSource' => true,
            'directory' => 'example/dir'
        ], $family);;
        $this->assertAttributeInternalType('array', 'styles', $family);
        $this->assertAttributeCount(3, 'styles', $family);
        $this->assertArrayHasKey('400i', $family->styles);
        $this->assertInstanceOf(Style::class, $family->styles['400i']);

        $family = Family::createFromSettings('Foo bar 2', [
            'styles' => [
                '300' => 'font_light.*',
                '300i' => 'font_light_italic.*',
                '700' => 'font_bold.*'
            ]
        ]);
        $this->assertAttributes([
            'name' => 'Foo bar 2',
            'forbidLocalSource' => null,
            'directory' => null,
        ], $family);
        $this->assertAttributeInternalType('array', 'styles', $family);
        $this->assertAttributeCount(3, 'styles', $family);
        $this->assertArrayHasKey('700', $family->styles);
        $this->assertInstanceOf(Style::class, $family->styles['700']);

        // Incorrect directory
        $this->assertException(InvalidSettingsException::class, function () {
            Family::createFromSettings('test', [
                'directory' => ['foo', 'bar']
            ]);
        });

        // Incorrect styles
        $this->assertException(InvalidSettingsException::class, function () {
            Family::createFromSettings('test', [
                'styles' => '1234'
            ]);
        });
    }

    public function testGetStyle()
    {
        $family = Family::createFromSettings('Foo bar', [
            'styles' => [
                '400' => 'font.*',
                '400I' => 'font_italic.*',
                '600' => 'font_semibold.*'
            ]
        ]);
        $this->assertInstanceOf(Style::class, $family->getStyle('600'));
        $this->assertEquals(null, $family->getStyle('300'));
        $this->assertEquals('400i', $family->getStyle('400i')->getId());
    }
}
