<?php

namespace MagicPill\Application;

use MagicPill\Core\Object;
use MagicPill\Core\Registry;
use MagicPill\Mixin\Options;
use \MagicPill\Mixin\Registry as TraitRegistry;

abstract class ApplicationAbstract extends Object
{
    use Options, TraitRegistry;

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
        $registry = $this->getRegistry();
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
        $registry = $this->getRegistry();
        $namespaces = array_merge($this->getOption('resourceNamespaces', []), $this->resourceNamespaces);
        $registry->setNamespaces($namespaces);
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