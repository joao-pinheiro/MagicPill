<?php

namespace MagicPill\Application;

use MagicPill\Core\Object;
use MagicPill\Core\Registry;
use MagicPill\Mixin\Options;

abstract class ApplicationAbstract extends Object
{
    use Options;

    /**
     * @var \MagicPill\Core\Registry
     */
    protected $registry = null;

    /**
     * Application configuration parameters
     * @var array
     */
    protected $options = [
        'resourceNamespaces' => [
            '\MagicPill\Application\Resource'
        ],
        'environment' => 'production',
        'configFile' => []
    ];

    /**
     * Constructor
     * @param array $options
     */
    public function __construct($options = [])
    {
        $this->setOptions(array_merge($this->options, $options));
        $this->registerApplication();
        $this->configureResources();
    }

    /**
     * Retrieve current environment
     * @return string
     */
    public function getEnvironment()
    {
        return $this->getOption('environment', 'production');
    }

    /**
     * Bootstraps the application
     * @return void
     */
    abstract public function start();

    /**
     * Executes the application
     * @return void
     */
    abstract public function run();

    /**
     * Retrieve application registry
     * @return \MagicPill\Core\Registry
     */
    public function getRegistry()
    {
        if (null == $this->registry) {
            $this->registry = Registry::getInstance();
        }
        return $this->registry;
    }

    /**
     * Configure Resource Loader
     */
    protected function configureResources()
    {
        $registry = $this->getRegistry();
        $registry->setNamespaces($this->getOption('resourceNamespaces', []));
    }

    /**
     * Registers application in the DI component
     */
    protected function registerApplication()
    {
        if (!$this->getRegistry()->contains(Resources::APPLICATION)) {
            $this->getRegistry()->set(Resources::APPLICATION, $this);
        }
    }
}