<?php

namespace Src\Services\WebfontCSSGenerator\Models;

use Src\Helpers\FileHelpers;
use Src\Services\WebfontCSSGenerator\Exceptions\InvalidSettingsException;

/**
 * Class Style
 *
 * Font style description.
 *
 * Only stores style data, doesn't provide specific logic.
 *
 * @author Finesse
 * @package Src\Services\WebfontCSSGenerator\Models
 */
class Style
{
    /**
     * @var int Style weight.
     */
    public $weight = 400;

    /**
     * @var bool Whether the style is italic.
     */
    public $isItalic = true;

    /**
     * @var bool|null Should local font source be omitted in a CSS code. Null means that the value should be inherited
     * from the style.
     */
    public $forbidLocalSource = null;

    /**
     * @var string|null Directory of the style font files relative to the fonts directory. Doesn't have slashes at the
     * begin or at the end. Reverse slashes are replaced with direct slashes.
     */
    public $directory = null;

    /**
     * @var string[] List of font files relative to the given directory. Without slash at the begin.
     */
    public $filesList = [];

    /**
     * @var string|null Font files glob pattern relative to the given directory.
     */
    public $filesGlob = null;

    /**
     * Creates the class instance from a font style settings (see an example in the readme).
     *
     * @param string $name Style name. Must have format `[0-9]+i?`.
     * @param string|mixed[] $settings Style settings data
     * @return static
     * @throws InvalidSettingsException
     */
    public static function createFromSettings(string $name, $settings): self
    {
        if (!preg_match('/^([0-9]+)(i?)$/i', $name, $matches)) {
            throw new InvalidSettingsException("The style name `$name` has invalid format.");
        }
        if (isset($settings['directory']) && !is_string($settings['directory'])) {
            throw new InvalidSettingsException('The $settings[directory] argument must be string or null, '.gettype($settings['directory']).' given.');
        }

        $style = new static();
        $style->weight = (int)$matches[1];
        $style->isItalic = (bool)$matches[2];
        $style->forbidLocalSource = isset($settings['forbidLocal']) ? (bool)$settings['forbidLocal'] : null;
        $style->directory = isset($settings['directory']) ? static::preparePath($settings['directory']) : null;

        if (is_string($settings)) {
            $style->filesGlob = static::preparePath($settings);
        } elseif (is_array($settings)) {
            if (is_string($settings['files'] ?? null)) {
                $style->filesGlob = static::preparePath($settings['files']);
            } elseif (is_array($settings['files'] ?? null)) {
                foreach($settings['files'] as $index => $file) {
                    if (!is_string($file)) {
                        throw new InvalidSettingsException('The $settings[files]['.$index.'] argument must be array or string, '.gettype($file).' given.');
                    }

                    $style->filesList[] = static::preparePath($file);
                }
            } else {
                throw new InvalidSettingsException('The $settings[files] argument must be array or string, '.gettype($settings['files']).' given.');
            }
        } else {
            throw new InvalidSettingsException('The $settings argument must be array or string, '.gettype($settings).' given.');
        }

        return $style;
    }

    /**
     * @return string The style name in format `[0-9]+i?`
     */
    public function getName(): string
    {
        return $this->weight . ($this->isItalic ? 'i' : '');
    }

    /**
     * Collects a list of the style font files.
     *
     * @param string|null $path A path in which the glob pattern should be applied
     * @return string[] List of files paths relative to the style directory. Without beginning slash. Reverse slashes
     *     are replaced with direct slashes.
     */
    public function getFilesInDirectory(string $path = null): array
    {
        $result = $this->filesList;

        if (isset($path) && isset($this->filesGlob)) {
            $pattern = FileHelpers::concatPath($path, $this->filesGlob);
            $files = glob($pattern, GLOB_BRACE);
            if ($files !== false) {
                foreach ($files as $file) {
                    $result[] = static::preparePath(substr($file, strlen($path)));
                }
            }
        }

        return $result;
    }

    /**
     * Prepares the path to be stored. Removes slashes from the begin and the end and replaces slashes are replaced with
     * direct slashes
     *
     * @param string $path
     * @return string
     */
    protected static function preparePath(string $path): string
    {
        return trim(str_replace('\\', '/', $path), '/');
    }
}
