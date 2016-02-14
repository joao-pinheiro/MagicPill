<?php

namespace MagicPill\Config;

use MagicPill\Collection\Container;
use MagicPill\Exception\ExceptionFactory;
use MagicPill\Mixin\ErrorStack;

class Ini extends Container
{
    use ErrorStack;

    /**
     * Constructor
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        if (empty($fileName) || !file_exists($fileName)) {
            ExceptionFactory::ConfigIniFileNotFoundException(sprintf('Configuration file %s not found', $fileName));
        }
        parent::__construct($this->parseIniFile($fileName));
    }

    /**
     * Parse an ini file and returns the result as array
     * @param $fileName
     * @return array
     */
    protected function parseIniFile($fileName)
    {
        set_error_handler(array($this, 'fileErrorHandler'));
        $result = parse_ini_file($fileName, true);
        restore_error_handler();

        if ($this->hasErrors()) {
            ExceptionFactory::ConfigIniParseException(implode(PHP_EOL, $this->getErrors()));
        }
        return $result;
    }

    /**
     * Error handler for ini parsing
     * @param int $code
     * @param string $message
     * @param string $file
     * @param string $line
     */
    protected function fileErrorHandler($code, $message, $file, $line)
    {
        $this->addError(implode(' ', ['Error', $code, ':', $message, 'in', $file, '@ line', $line]));
    }
}
