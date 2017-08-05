<?php

namespace Src\Services\WebfontCSSGenerator\Exceptions;

/**
 * Class InvalidSettingsException
 *
 * Error: the given settings data has incorrect format.
 *
 * @author Finesse
 * @package Src\Services\WebfontCSSGenerator\Exceptions
 */
class InvalidSettingsException extends \InvalidArgumentException implements IException {}
