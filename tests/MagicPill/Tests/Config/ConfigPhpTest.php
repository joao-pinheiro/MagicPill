<?php

namespace MagicPill\Test\Config;

use MagicPill\Config\Php as Config;

class ConfigPhpTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    protected $config;

    /**
     * @var string
     */
    protected $resourceFile = APPLICATION_PATH
    . DIRECTORY_SEPARATOR
    . 'tests'
    . DIRECTORY_SEPARATOR
    . 'MagicPill'
    . DIRECTORY_SEPARATOR
    . 'Tests'
    . DIRECTORY_SEPARATOR
    . 'Config'
    . DIRECTORY_SEPARATOR
    . 'Resources'
    . DIRECTORY_SEPARATOR
    . 'PhpTestData.php';

    /**
     * setup tests
     */
    public function setup()
    {
        $this->config = new Config($this->resourceFile);
    }

    /**
     * Test if config file is not empty
     */
    public function testNotEmpty()
    {
        $this->assertFalse($this->config->isEmpty());
    }

    /**
     * Test if config file contents are correct
     */
    public function testConfigContents()
    {
        $source = require $this->resourceFile;
        $config = $this->config->toArray();
        $this->assertSame(ksort($source), ksort($config));
    }
}