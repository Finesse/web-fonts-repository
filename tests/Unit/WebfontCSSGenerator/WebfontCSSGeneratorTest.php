<?php

namespace Tests\Unit\WebfontCSSGenerator;

use Src\Services\WebfontCSSGenerator\WebfontCSSGenerator;
use Tests\BaseTestCase;

class WebfontCSSGeneratorTest extends BaseTestCase
{
    public function testConstructor()
    {
        // Invalid families
        $this->assertException(\InvalidArgumentException::class, function () {
            new WebfontCSSGenerator(['Open Sans', 'Roboto']);
        }, function (\Throwable $exception) {
            $this->assertEquals(
                'Argument $families[0] expected to be a Family instance, string given.',
                $exception->getMessage()
            );
        });
    }

    public function testMakeCSS()
    {
        $generator = WebfontCSSGenerator::createFromSettings([
            'Open Sans' => [
                'forbidLocal' => false,
                'directory' => 'OpenSans',
                'styles' => [
                    '400i' => [
                        'forbidLocal' => false,
                        'directory' => null,
                        'files' => [
                            'opensans_italic.eot',
                            'opensans_italic.ttf',
                            'opensans_italic.woff',
                            'opensans_italic.woff2',
                            'opensans_italic.svg',
                            'opensans_italic.otf'
                        ]
                    ],
                    '700' => [
                        'forbidLocal' => true,
                        'files' => [
                            'opensans_bold.eot',
                            'opensans_bold.ttf',
                            'opensans_bold.woff',
                            'opensans_bold.woff2',
                            'opensans_bold.svg',
                            'opensans_bold.otf'
                        ]
                    ],
                    '500' => [
                        'name' => 'DemiBold',
                        'files' => [
                            'opensans_demi.eot',
                            'opensans_demi.ttf',
                            'opensans_demi.woff',
                            'opensans_demi.woff2',
                            'opensans_demi.svg',
                            'opensans_demi.otf'
                        ]
                    ]
                ]
            ],
            'Roboto' => [
                'forbidLocal' => true,
                'directory' => 'Roboto',
                'styles' => [
                    '400' => [
                        'forbidLocal' => false,
                        'directory' => null,
                        'files' => [
                            'roboto.woff2'
                        ]
                    ],
                    '700' => [
                        'files' => [
                            'roboto_bold.woff2'
                        ]
                    ],
                    '900' => [
                        'files' => [
                            'roboto_extra_black' // A file without extension
                        ]
                    ]
                ]
            ]
        ], '/generator');

        $this->assertCSSEquals("
@font-face {
	font-family: 'Open Sans';
	font-weight: 400;
	font-style: italic;
	src: url('/generator/fonts/OpenSans/opensans_italic.eot');
	src: local('Open Sans Italic'), local('OpenSans-Italic'), url('/generator/fonts/OpenSans/opensans_italic.eot?#iefix') format('embedded-opentype'), url('/generator/fonts/OpenSans/opensans_italic.woff2') format('woff2'), url('/generator/fonts/OpenSans/opensans_italic.woff') format('woff'), url('/generator/fonts/OpenSans/opensans_italic.ttf') format('truetype'), url('/generator/fonts/OpenSans/opensans_italic.otf') format('opentype'), url('/generator/fonts/OpenSans/opensans_italic.svg#webfontregular') format('svg');
}
@font-face {
	font-family: 'Open Sans';
	font-weight: 700;
	font-style: normal;
	src: url('/generator/fonts/OpenSans/opensans_bold.eot');
	src: url('/generator/fonts/OpenSans/opensans_bold.eot?#iefix') format('embedded-opentype'), url('/generator/fonts/OpenSans/opensans_bold.woff2') format('woff2'), url('/generator/fonts/OpenSans/opensans_bold.woff') format('woff'), url('/generator/fonts/OpenSans/opensans_bold.ttf') format('truetype'), url('/generator/fonts/OpenSans/opensans_bold.otf') format('opentype'), url('/generator/fonts/OpenSans/opensans_bold.svg#webfontregular') format('svg');
}
@font-face {
	font-family: 'Roboto';
	font-weight: 400;
	font-style: normal;
	src: local('Roboto Regular'), local('Roboto-Regular'), url('/generator/fonts/Roboto/roboto.woff2') format('woff2');
}
@font-face {
	font-family: 'Roboto';
	font-weight: 700;
	font-style: normal;
	src: url('/generator/fonts/Roboto/roboto_bold.woff2') format('woff2');
}
        ", $generator->makeCSS([
            'Open Sans' => ['400', '400i', '700', '700i', '100', '200', '300', '800'],
            'Roboto' => ['400', '400i', '700', '700i']
        ]));

        $this->assertCSSEquals("
@font-face {
	font-family: 'Open Sans';
	font-weight: 400;
	font-style: italic;
	src: url('/generator/fonts/OpenSans/opensans_italic.eot');
	src: local('Open Sans Italic'), local('OpenSans-Italic'), url('/generator/fonts/OpenSans/opensans_italic.eot?#iefix') format('embedded-opentype'), url('/generator/fonts/OpenSans/opensans_italic.woff2') format('woff2'), url('/generator/fonts/OpenSans/opensans_italic.woff') format('woff'), url('/generator/fonts/OpenSans/opensans_italic.ttf') format('truetype'), url('/generator/fonts/OpenSans/opensans_italic.otf') format('opentype'), url('/generator/fonts/OpenSans/opensans_italic.svg#webfontregular') format('svg');
}
@font-face {
	font-family: 'Open Sans';
	font-weight: 500;
	font-style: normal;
	src: url('/generator/fonts/OpenSans/opensans_demi.eot');
	src: local('Open Sans DemiBold'), local('OpenSans-DemiBold'), url('/generator/fonts/OpenSans/opensans_demi.eot?#iefix') format('embedded-opentype'), url('/generator/fonts/OpenSans/opensans_demi.woff2') format('woff2'), url('/generator/fonts/OpenSans/opensans_demi.woff') format('woff'), url('/generator/fonts/OpenSans/opensans_demi.ttf') format('truetype'), url('/generator/fonts/OpenSans/opensans_demi.otf') format('opentype'), url('/generator/fonts/OpenSans/opensans_demi.svg#webfontregular') format('svg');
}
@font-face {
	font-family: 'Roboto';
	font-weight: 400;
	font-style: normal;
	src: local('Roboto Regular'), local('Roboto-Regular'), url('/generator/fonts/Roboto/roboto.woff2') format('woff2');
}
        ", $generator->makeCSS([
            'Open Sans' => ['400i', '500'],
            'Roboto' => ['400', '400', '900'] // Regular style two times
        ]));
    }
}
