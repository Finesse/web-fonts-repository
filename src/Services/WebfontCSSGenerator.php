<?php

namespace Src\Services;

use Src\Helpers\CSSHelpers;

/**
 * Class WebfontCSSGenerator
 *
 * Generates CSS code for embedding webfonts.
 *
 * @author Finesse
 * @package Src\Services
 */
class WebfontCSSGenerator
{
    /**
     * The name of the fonts directory in the public directory. May contain slashes for subdirectories.
     */
    const FONTS_DIRECTORY = 'fonts';

    /**
     * @var array[] Information about available fonts from settings
     */
    protected $fonts;

    /**
     * @var string The URL of the root fonts directory. Doesn't end with slash.
     */
    protected $fontsDirectoryURL;

    /**
     * @param array[] $availableFonts Information about available fonts from settings
     * @param string $rootURL The site root URL. With or without a domain and a protocol.
     */
    public function __construct(array $availableFonts = [], string $rootURL = '')
    {
        $this->fonts = $availableFonts;
        $this->fontsDirectoryURL = rtrim($rootURL, '/').'/'.static::FONTS_DIRECTORY;
    }

    /**
     * Makes CSS code for the given families.
     *
     * @param string[][] $requestedFamilies The list of families. The indexes are families names, the values are lists
     *     of family styles. The styles must have format `[0-9]+i?`. Example:
     * <pre>
     *  [
     *      'Open Sans' => ['400', '700'],
     *      'Roboto'    => ['100', '100i', '400', '400i']
     *  ]
     * </pre>
     * @return string
     * @throws \InvalidArgumentException
     */
    public function makeCSS(array $requestedFamilies): string
    {
        $cssCode = '';

        foreach ($requestedFamilies as $fontName => $styles) {
            $cssCode .= $this->makeFontFamilyCSS($fontName, $styles);
        }

        return $cssCode;
    }

    /**
     * Makes CSS code for the given font family.
     *
     * @param string $name Family name
     * @param string[] $styles Font styles. The styles must have format `[0-9]+i?`.
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function makeFontFamilyCSS(string $name, array $styles = ['400']): string
    {
        $cssCode = '';
        $readyStyles = [];

        foreach ($styles as $style) {
            if (isset($readyStyles[$style])) {
                continue;
            }

            $styleCssCode = $this->makeFontStyleCSS($name, $style);
            if ($styleCssCode !== '') {
                $cssCode .= $styleCssCode."\n";
            }

            $readyStyles[$style] = true;
        }

        return $cssCode;
    }

    /**
     * Makes CSS code for the given font style.
     *
     * @param string $familyName Font family name
     * @param string $styleName Font style. The styles must have format `[0-9]+i?`.
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function makeFontStyleCSS(string $familyName, string $styleName): string
    {
        // Check the $styleName argument
        if (!preg_match('/^([0-9]+)(i?)$/', $styleName, $matches)) {
            throw new \InvalidArgumentException("The font style name `$styleName` can't be recognized");
        }
        $weight = (int)$matches[1];
        $isItalic = !empty($matches[2]);

        // Does the style exist in the configuration?
        if (!isset($this->fonts[$familyName]['styles'][$styleName])) {
            return '';
        }
        $familyInfo = $this->fonts[$familyName];
        $styleInfo = $familyInfo['styles'][$styleName];

        // Does the style has any font files?
        $files = $this->getFontFilesURLs($familyName, $styleName);
        if (empty($files)) {
            return '';
        }

        // Building CSS code
        $sources = [];
        if (!($styleInfo['forbidLocal'] ?? $familyInfo['forbidLocal'] ?? false)) {
            $sources[] = "local(".CSSHelpers::formatString($familyName).")";
        }
        if (isset($files['eot'])) {
            $sources[] = "url(".CSSHelpers::formatString($files['eot'])."?#iefix) format('embedded-opentype')";
        }
        if (isset($files['woff2'])) {
            $sources[] = "url(".CSSHelpers::formatString($files['woff2']).") format('woff2')";
        }
        if (isset($files['woff'])) {
            $sources[] = "url(".CSSHelpers::formatString($files['woff']).") format('woff')";
        }
        if (isset($files['ttf'])) {
            $sources[] = "url(".CSSHelpers::formatString($files['ttf']).") format('truetype')";
        }
        if (isset($files['svg'])) {
            $sources[] = "url(".CSSHelpers::formatString($files['svg'])."#webfontregular) format('svg')";
        }

        return "@font-face {\n"
            . "\tfont-family: ".CSSHelpers::formatString($familyName).";\n"
            . "\tfont-weight: $weight;\n"
            . "\tfont-style: ".($isItalic ? 'italic' : 'normal').";\n"
            . (isset($files['eot']) ? "\tsrc: url(".CSSHelpers::formatString($files['oet']).");\n" : '')
            . "\tsrc: ".implode(', ', $sources).";\n"
            . "}";
    }

    protected function getFontFilesURLs(string $fontName, string $style)
    {
        // todo: Implement
        return ['woff2' => $this->fontsDirectoryURL.'/test.woff2'];

        if (!isset($this->fonts[$fontName]['styles'][$style])) {
            return [];
        }

        $path = [$this->fontsDirectoryURL];

        if (isset($this->fonts[$fontName]['directory'])) {
            $pathComponents[] = $this->fonts[$fontName]['directory'];
        }
    }
}
