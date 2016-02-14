<?php

namespace MagicPill\Test\Config;

use MagicPill\Config\Json as Config;

class ConfigJsonTest extends \PHPUnit_Framework_TestCase
{
    /** @var Config */
    protected $config;

    protected $contents = '{"option1":{"parameter1":"value1","parameter2":"value2"},"option2":{"parameter3":"value3","parameter4":"value4"},"option3":{"parameter5":{"option1":"optionvalue1","option2":"optionvalue2"},"parameter6":"value6"}}';

    /**
     * setup tests
     */
    public function setup()
    {
        $this->config = new Config($this->contents);
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
        $source = json_decode($this->contents, true);
        $config = $this->config->toArray();
        $this->assertSame(ksort($source), ksort($config));
    }
}