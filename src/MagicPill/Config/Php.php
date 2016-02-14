<?php

namespace MagicPill\Config;

use MagicPill\Collection\Container;
use MagicPill\Exception\ExceptionFactory;

class Php extends Container
{
    /**
     * Constructor
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        if (empty($fileName) || !file_exists($fileName)) {
            ExceptionFactory::ConfigPhpFileNotFoundException(sprintf('Configuration file %s not found', $fileName));
        }
        parent::__construct(require $fileName);
    }
}