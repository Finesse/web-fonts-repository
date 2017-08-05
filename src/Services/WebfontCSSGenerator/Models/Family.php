<?php

namespace Src\Services\WebfontCSSGenerator\Models;

use Src\Services\WebfontCSSGenerator\Exceptions\InvalidSettingsException;

/**
 * Class Family
 *
 * Font family description.
 *
 * Only stores family data, doesn't provide specific logic.
 *
 * @author Finesse
 * @package Src\Services\WebfontCSSGenerator\Models
 */
class Family
{
    /**
     * @var string The family name
     */
    public $name;

    /**
     * @var bool|null Should local font source be omitted in a CSS code. Null means that the value should be inherited
     * from global settings.
     */
    public $forbidLocalSource = null;

    /**
     * @var string|null Directory of the family font files relative to the fonts directory. Doesn't have slashes at the
     * begin or at the end. Reverse slashes are replaced with direct slashes.
     */
    public $directory = null;

    /**
     * @var Style[] List of available family styles. The array keys are the style names.
     */
    public $styles = [];

    /**
     * Creates the class instance from a font family settings (see an example in the readme).
     *
     * @param mixed[] $settings Family settings data
     * @return static
     * @throws InvalidSettingsException
     */
    public static function createFromSettings(string $name, array $settings): self
    {
        if (isset($settings['directory']) && !is_string($settings['directory'])) {
            throw new InvalidSettingsException('The $settings[directory] argument must be string or null, '.gettype($settings['directory']).' given.');
        }
        if (!is_array($settings['styles'] ?? null)) {
            throw new InvalidSettingsException('The $settings[styles] argument must be array, '.gettype($settings['directory']).' given.');
        }

        $family = new static();
        $family->name = $name;
        $family->forbidLocalSource = isset($settings['forbidLocal']) ? (bool)$settings['forbidLocal'] : null;
        $family->directory = isset($settings['directory']) ? static::preparePath($settings['directory']) : null;
        foreach ($settings['styles'] as $styleName => $styleSettings) {
            $style = Style::createFromSettings($styleName, $styleSettings);
            $family->styles[$style->getName()] = $style;
        }

        return $family;
    }

    /**
     * Finds the font style of this family.
     *
     * @param string $styleName The style name
     * @return Style|null
     */
    public function getStyle(string $styleName)
    {
        return $this->styles[$styleName] ?? null;
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
