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
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();
        \Mockery::close();
    }

    /**
     * Asserts that one CSS code is semantically equal to the other CSS code.
     *
     * @param string $expected
     * @param string $actual
     * @param string $message
     */
    protected function assertCSSEquals(string $expected, string $actual, string $message = '')
    {
        $this->assertEquals(
            $this->removeUnnecessaryCharsFromCSS($expected),
            $this->removeUnnecessaryCharsFromCSS($actual),
            $message
        );
    }

    /**
     * Removes unnecessary characters from CSS text for more convenient comparison.
     *
     * @param string $css
     * @return string
     */
    protected function removeUnnecessaryCharsFromCSS(string $css): string
    {
        return preg_replace('/[\s]+/', '', $css);
    }

    /**
     * Asserts that the given callback throws the given exception.
     *
     * @param string $expectClass The name of the expected exception class
     * @param callable $callback A callback which should throw the exception
     * @param callable|null $onException A function to call after exception check. It may be used to test the exception.
     */
    protected function assertException(string $expectClass, callable $callback, callable $onException = null)
    {
        try {
            $callback();
        } catch (\Throwable $exception) {
            $this->assertInstanceOf($expectClass, $exception);
            if ($onException) {
                $onException($exception);
            }
            return;
        }

        $this->fail('No exception has been thrown');
    }

    /**
     * Asserts that the given object has the given attributes with the given values.
     *
     * @param array $expectedAttributes Attributes. The indexes are the attributes names, the values are the attributes
     *    values.
     * @param mixed $actualObject Object
     */
    protected function assertAttributes(array $expectedAttributes, $actualObject)
    {
        foreach ($expectedAttributes as $property => $value) {
            $this->assertObjectHasAttribute($property, $actualObject);
            $this->assertAttributeEquals($value, $property, $actualObject);
        }
    }
}
