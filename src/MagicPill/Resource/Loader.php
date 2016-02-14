<?php

namespace MagicPill\Resource;

use MagicPill\Collection\Collection;
use MagicPill\Collection\Dictionary;
use MagicPill\Exception\ExceptionFactory;


class Loader
{
    /**
     * @var \MagicPill\Collection\Collection
     */
    protected $namespaceCollection = null;

    /**
     * @var \MagicPill\Collection\Dictionary
     */
    protected $resourceDictionary = null;

    /**
     * @var string
     */
    protected $objectType = '';

    /**
     * Constructor
     * @param \Traversable|array $namespaceList
     */
    public function __construct($namespaceList = [])
    {
        if (!empty($namespaceList)) {
            $this->setNamespaces($namespaceList);
        }
    }

    /**
     * Define the allowed resource instance type
     * @param $className
     * @return $this
     */
    public function setInstanceOf($className)
    {
        $this->objectType = $className;
        return $this;
    }

    /**
     * Retrieve the allowed resource instance type
     * @return string
     */
    public function getInstanceOf()
    {
        return $this->objectType;
    }

    /**
     * Allow all instance types
     * @return $this
     */
    public function clearInstanceType()
    {
        $this->objectType = '';
        return $this;
    }

    /**
     * Set the namespace collection content
     * @param \Traversable|array $namespaceList
     * @return $this
     */
    public function setNamespaces($namespaceList)
    {
        $this->getNamespaceCollection()->fromArray($namespaceList);
        return $this;
    }

    /**
     * Adds a namespace to the beginning of the namespace collection
     * @param string $namespace
     * @return $this
     */
    public function addNamespace($namespace)
    {
        $this->getNamespaceCollection()->unshift($namespace);
        return $this;
    }

    /**
     * Adds a namespace to the end of the namespace collection
     * @param string $namespace
     * @return $this
     */
    public function appendNamespace($namespace)
    {
        $this->getNamespaceCollection()->add($namespace);
        return $this;
    }

    /**
     * Checks if the specified namespace exists in the namespace collection
     * @param string $namespace
     * @return bool
     */
    public function containsNamespace($namespace)
    {
        return $this->getNamespaceCollection()->containsValue($namespace);
    }

    /**
     * Retrieve namespace collection
     * @return Collection
     */
    public function getNamespaceCollection()
    {
        if (null == $this->namespaceCollection) {
            $this->namespaceCollection = new Collection();
        }
        return $this->namespaceCollection;
    }

    /**
     * tries to find a class by name
     * @param string $resourceName
     * @return string
     */
    public function findResource($resourceName)
    {
        $allowedInstance = $this->getInstanceOf();
        foreach ($this->getNamespaceCollection() as $ns) {
            $className = $ns . '\\' . $resourceName;
            if (class_exists($className)) {
                if (empty($allowedInstance) || (!empty($allowedInstance) && is_subclass_of($className, $allowedInstance, true))) {
                    return $className;
                }
            }
        }
        return '';
    }

    /**
     * Returns true if the specified resource exists
     * @param string $resourceName
     * @return bool
     */
    public function resourceExists($resourceName)
    {
        return !empty($this->findResource($resourceName));
    }

    /**
     * Loads a resource and returns the object
     * @param string $resourceName
     * @param mixed|null $constructorOptions
     * @return Object
     */
    public function loadResource($resourceName, $constructorOptions = null)
    {
        $className = $this->findResource($resourceName);
        if (empty($className)) {
            ExceptionFactory::ResourceLoaderNotFoundException(sprintf('Resource %s not found', $resourceName));
        }
        return new $className($constructorOptions);
    }

    /**
     * Magic hook to retrieve resources as properties
     * @param string $name
     * @return mixed|Object
     */
    public function __get($name)
    {
        $list = $this->getResourceDictionary();
        if ($list->containsKey($name)) {
            return $list->get($name);
        }
        $resource = $this->loadResource($name);
        $list->add($name, $resource);

        return $resource;
    }

    /**
     * Magic hook to retrieve resources as magic methods using get<ResourceName>()
     * @param string $name
     * @param array $arguments
     * @return Object
     */
    public function __call($name, $arguments)
    {
        $prefix = substr($name, 0, 3);
        if ($prefix == 'get') {
            $name = substr($name, 3);
            return $this->__get(ucfirst($name));
        }
        ExceptionFactory::ResourceLoaderInvalidMethodException(sprintf('Invalid method name %s', $name));
        return null;
    }

    /**
     * Retrieve resource dictionary
     * @return Dictionary
     */
    protected function getResourceDictionary()
    {
        if (null == $this->resourceDictionary) {
            $this->resourceDictionary = new Dictionary();
        }
        return $this->resourceDictionary;
    }
}
