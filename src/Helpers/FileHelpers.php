<?php

namespace Src\Helpers;

/**
 * Class FileHelpers
 *
 * Help functions for working with files.
 *
 * @author Finesse
 * @package Src\Helpers
 */
class FileHelpers
{
    /**
     * Concatenates path parts.
     *
     * @param string[]|null[] ...$parts Parts. May contain null values (they are omitted).
     * @return string
     */
    public static function concatPath(...$parts): string
    {
        $parts = self::checkAndFilterPathArray($parts, 'Argument #');
        $result = '';

        foreach ($parts as $part) {
            $result .= $result === '' ? rtrim($part, '/\\') : '/'.trim($part, '/\\');
        }

        return $result;
    }

    /**
     * Checks the parts list for the concatPath method
     *
     * @param string[]|null[] $parts
     * @param string $prefix Invalid argument error message prefix
     * @return string[]
     * @throws \InvalidArgumentException
     */
    protected static function checkAndFilterPathArray(array $parts, string $prefix = 'Part #'): array
    {
        $result = [];

        foreach ($parts as $index => $part) {
            if ($part === null) {
                continue;
            }
            if (!is_string($part)) {
                throw new \InvalidArgumentException($prefix.$index.' expected to be string or null, '.gettype($part).' given.');
            }
            $result[] = $part;
        }

        return $result;
    }
}
