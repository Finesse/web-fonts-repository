<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

/**
 * Class BaseTestCase
 *
 * Base TestCase class for application tests.
 *
 * @author Finesse
 * @package Tests
 */
class BaseTestCase extends TestCase
{
    /**
     * Removes unnecessary characters from CSS text for more convenient comparison.
     *
     * @param string $css
     * @return string
     */
    protected static function removeUnnecessaryCharsFromCSS(string $css): string
    {
        return preg_replace('/[\s]+/', '', $css);
    }
}
