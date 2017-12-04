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
                    '400' => [
                        'forbidLocal' => false,
                        'directory' => null,
                        'files' => [
                            'opensans.eot',
                            'opensans.ttf',
                            'opensans.woff',
                            'opensans.woff2',
                            'opensans.svg'
                        ]
                    ],
                    '700' => [
                        'forbidLocal' => true,
                        'files' => [
                            'opensans_bold.eot',
                            'opensans_bold.ttf',
                            'opensans_bold.woff',
                            'opensans_bold.woff2',
                            'opensans_bold.svg'
                        ]
                    ],
                    '500' => [
                        'files' => [
                            'opensans_medium.eot',
                            'opensans_medium.ttf',
                            'opensans_medium.woff',
                            'opensans_medium.woff2',
                            'opensans_medium.svg'
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
	font-style: normal;
	src: url('/generator/fonts/OpenSans/opensans.eot');
	src: local('Open Sans'), url('/generator/fonts/OpenSans/opensans.eot?#iefix') format('embedded-opentype'), url('/generator/fonts/OpenSans/opensans.woff2') format('woff2'), url('/generator/fonts/OpenSans/opensans.woff') format('woff'), url('/generator/fonts/OpenSans/opensans.ttf') format('truetype'), url('/generator/fonts/OpenSans/opensans.svg#webfontregular') format('svg');
}
@font-face {
	font-family: 'Open Sans';
	font-weight: 700;
	font-style: normal;
	src: url('/generator/fonts/OpenSans/opensans_bold.eot');
	src: url('/generator/fonts/OpenSans/opensans_bold.eot?#iefix') format('embedded-opentype'), url('/generator/fonts/OpenSans/opensans_bold.woff2') format('woff2'), url('/generator/fonts/OpenSans/opensans_bold.woff') format('woff'), url('/generator/fonts/OpenSans/opensans_bold.ttf') format('truetype'), url('/generator/fonts/OpenSans/opensans_bold.svg#webfontregular') format('svg');
}
@font-face {
	font-family: 'Roboto';
	font-weight: 400;
	font-style: normal;
	src: local('Roboto'), url('/generator/fonts/Roboto/roboto.woff2') format('woff2');
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
	font-style: normal;
	src: url('/generator/fonts/OpenSans/opensans.eot');
	src: local('Open Sans'), url('/generator/fonts/OpenSans/opensans.eot?#iefix') format('embedded-opentype'), url('/generator/fonts/OpenSans/opensans.woff2') format('woff2'), url('/generator/fonts/OpenSans/opensans.woff') format('woff'), url('/generator/fonts/OpenSans/opensans.ttf') format('truetype'), url('/generator/fonts/OpenSans/opensans.svg#webfontregular') format('svg');
}
@font-face {
	font-family: 'Open Sans';
	font-weight: 500;
	font-style: normal;
	src: url('/generator/fonts/OpenSans/opensans_medium.eot');
	src: local('Open Sans'), url('/generator/fonts/OpenSans/opensans_medium.eot?#iefix') format('embedded-opentype'), url('/generator/fonts/OpenSans/opensans_medium.woff2') format('woff2'), url('/generator/fonts/OpenSans/opensans_medium.woff') format('woff'), url('/generator/fonts/OpenSans/opensans_medium.ttf') format('truetype'), url('/generator/fonts/OpenSans/opensans_medium.svg#webfontregular') format('svg');
}
@font-face {
	font-family: 'Roboto';
	font-weight: 400;
	font-style: normal;
	src: local('Roboto'), url('/generator/fonts/Roboto/roboto.woff2') format('woff2');
}
        ", $generator->makeCSS([
            'Open Sans' => ['400', '500'],
            'Roboto' => ['400', '400', '900'] // Regular style two times
        ]));
    }
}
