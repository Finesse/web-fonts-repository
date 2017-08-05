<?php

namespace Src\Helpers;

/**
 * Class CSSHelpers
 *
 * Help functions for working with CSS code.
 *
 * @author Finesse
 * @package Src\Helpers
 */
class CSSHelpers
{
    /**
     * Format string for putting to CSS code as a string value. Add required quotation marks at the begin and at the end.
     *
     * Examples of usage:
     *  - making values for `before` and `after` properties: `"before: ".CSSHelpers::formatString($text)`;
     *  - formatting background URLs: `"background-image: url(".CSSHelpers::formatString($url).")"`;
     *
     * @param string $text
     * @return string
     */
    public static function formatString(string $text): string
    {
        return "'".str_replace(
            [  "'",   "\\",   "\n"],
            ["\\'", "\\\\", "\\\n"],
            $text
        )."'";
    }
}
