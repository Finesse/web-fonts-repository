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
        $result = null;

        foreach ($parts as $index => $part) {
            if ($part === null) {
                continue;
            }
            if (!is_string($part)) {
                throw new \InvalidArgumentException('Argument '.$index.' expected to be string or null, '.gettype($part).' given.');
            }

            if ($result === null) {
                $result = rtrim($part, '/\\');
            } else {
                $result .= '/'.trim($part, '/\\');
            }
        }

        return $result ?? '';
    }
}
