<?php

namespace MagicPill\Application;

use MagicPill\Core\Object;
use MagicPill\Core\Container;
use MagicPill\Mixin\DI;
use MagicPill\Mixin\Options;

abstract class ApplicationAbstract extends Object
{
    use Options, DI;

    /**
     * Default application resource namespaces
     * @var array
     */
    protected $resourceNamespaces = [
        '\MagicPill\Application\Resource'
    ];

    /**
     * Application configuration parameters
     * @var array
     */
    protected $options = [
        'resourceNamespaces' => [],
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
     * @param string $defaultEnv
     * @return string
     */
    public function getEnvironment($defaultEnv = 'production')
    {
        return $this->getOption('environment', $defaultEnv);
    }

    /**
     * Bootstraps the application
     * @return void
     */
    public function start()
    {
        $registry = $this->getDi();
        $registry->getPhpSettings();
        $registry->getHandlers();
    }

    /**
     * Executes the application
     * @return void
     */
    abstract public function run();

    /**
     * Configure Resource Loader
     */
    protected function configureResources()
    {
        $registry = $this->getDi();
        $namespaces = array_merge($this->getOption('resourceNamespaces', []), $this->resourceNamespaces);
        $registry->setNamespaces($namespaces);
    }

    /**
     * Registers application in the DI component
     */
    protected function registerApplication()
    {
        if (!$this->getDi()->has(Resources::APPLICATION)) {
            $this->getDi()->set(Resources::APPLICATION, $this);
        }
    }
}