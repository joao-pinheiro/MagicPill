<?php

namespace MagicPill\Core;

use MagicPill\Collection\Dictionary;
use MagicPill\Exception\ExceptionFactory;
use MagicPill\Mixin\Singleton;
use MagicPill\Resource\Loader;

/**
 * Class Registry
 * @package MagicPill\Core
 *
 * @method setInstanceOf($className)
 * @method getInstanceOf()
 * @method clearInstanceType()
 * @method setNamespaces($namespaceList)
 * @method addNamespace($namespace)
 * @method appendNamespace($namespace)
 * @method containsNamespace($namespace)
 * @method getNamespaceCollection()
 * @method resourceExists($resourceName)
 * @method loadResource($resourceName, $constructorOptions = null)
 */
class Registry
{
    use Singleton;

    /**
     * @var string
     */
    protected $resourceInterface = '\MagicPill\Core\Registry\ResourceInterface';

    /**
     * @var \MagicPill\Resource\Loader
     */
    protected $resourceLoader = null;

    /**
     * @var \MagicPill\Collection\Dictionary
     */
    protected $data = null;

    /**
     * @var bool
     */
    protected $resourceLoaderEnabled = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->data = new Dictionary();
    }

    /**
     * Disables the internal resource loader
     * @return $this
     */
    public function disableResourceLoader()
    {
        $this->resourceLoaderEnabled = false;
        return $this;
    }

    /**
     * Enables the internal resource loader
     * @return $this
     */
    public function enableResourceLoader()
    {
        $this->resourceLoaderEnabled = true;
        return $this;
    }

    /**
     * Adds an entry to the local registry
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value)
    {
        if ($this->data->containsKey($name)) {
            ExceptionFactory::RegistryException(sprintf('Duplicate entry for identifier %s', $name));
        }
        $this->data->add($name, $value);
        return $this;
    }

    /**
     * Removes a registered resource
     * @param $name
     * @return $this
     */
    public function remove($name)
    {
        $this->data->remove($name);
        return $this;
    }

    /**
     * Returns true if $name exists in the registry
     * @param string $name
     * @return bool
     */
    public function contains($name)
    {
        return $this->data->containsKey($name);
    }

    /**
     * Retrieve a registed item by name
     * @param string $name
     * @return Object|mixed
     */
    public function get($name)
    {
        return $this->__get($name);
    }

    /**
     * Retrieve a registed item by name
     * @param string $name
     * @return Object|mixed
     */
    public function __get($name)
    {
        if ($this->data->containsKey($name)) {
            $value = $this->data->get($name);
            if ($value instanceof \Closure) {
                $value = $value();
                $this->data->add($name, $value);
            }
            return $value;
        }

        if ($this->resourceLoaderEnabled) {
            return $this->findResource($name);
        } else {
            ExceptionFactory::RegistryException(sprintf('Resource %s not found', $name));
        }
        return null;
    }

    /**
     * Magic method to support method-based getters and expose the resource loader
     * @param string $name
     * @param array $arguments
     * @return mixed|Object
     */
    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);
        if ($prefix == 'get') {
            $name = substr($name, 3);
            return $this->__get($name);
        }
        $loader = $this->getResourceLoader();
        if (method_exists($loader, $name)) {
            return call_user_func_array([$loader, $name], $arguments);
        }
        ExceptionFactory::RegistryException(sprintf('Invalid method name %s', $name));
        return null;
    }

    /**
     * Retrieve resource loader
     * @return Loader
     */
    protected function getResourceLoader()
    {
        if (null == $this->resourceLoader) {
            $this->resourceLoader = new Loader();
            $this->resourceLoader->setInstanceOf($this->resourceInterface);
        }
        return $this->resourceLoader;
    }

    /**
     * Finds and initializes a resource
     * If the resource initialization returns a value, store it on the collection
     * @param string $resourceName
     * @return Object|null
     */
    protected function findResource($resourceName)
    {
        /** @var \MagicPill\Core\Registry\ResourceInterface $resource */
        $resource = $this->getResourceLoader()->loadResource($resourceName);
        $resource = call_user_func([$resource, 'init'], $this);
        if (!empty($resource)) {
            $this->data->add($resourceName, $resource);
        }
        return $resource;
    }
}