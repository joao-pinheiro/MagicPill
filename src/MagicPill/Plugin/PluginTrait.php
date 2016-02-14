<?php

namespace MagicPill\Plugin;

use MagicPIll\Core\Object,
    MagicPill\Collection\Collection,
    MagicPill\Collection\Dictionary;

trait PluginTrait
{
    /**
     * @var Dictionary
     */
    protected $pluginCollection = null;

    /**
     * plugin namespaces
     * @var Collection
     */
    protected $pluginNamespaces = null;

    /**
     * @var PluginValidatorInterface
     */
    protected $pluginValidator = null;

    /**
     * Retrieve plugin collection
     * @return Dictionary
     */
    public function getPluginCollection()
    {
        if (null == $this->pluginCollection) {
            $this->pluginCollection = new Dictionary();
        }
        return $this->pluginCollection;
    }

    /**
     * Retrieve plugin namespace collection
     * @return Collection
     */
    public function getPluginNamespaces()
    {
        if (null == $this->pluginNamespaces) {
            $this->pluginNamespaces = new Collection();
        }
        return $this->pluginNamespaces;
    }

    /**
     * @param $namespace
     * @return $this
     */
    public function addPluginNamespace($namespace)
    {
        $this->getPluginNamespaces()->add($namespace);
        return $this;
    }

    /**
     * Check if plugin exists
     * @param string $name
     * @return bool
     */
    public function pluginExists($name)
    {
        return $this->getPluginCollection()->containsKey($name);
    }

    /**
     * Registers a plugin
     * @param string $name
     * @param Object $content
     * @return $this
     */
    public function addPlugin($name, Object $content)
    {
        $this->getPluginCollection()->add($name, $content);
        return $this;
    }

    /**
     * Retrieve a plugin
     * @param string $name
     * @return mixed
     */
    public function getPlugin($name)
    {
        return $this->getPluginCollection()->get($name);
    }

    public function clearPLuginCollection()
    {
        $this->getPluginCollection()->clear();
        return $this;
    }

    /**
     * Clears all registered namespaces
     * @return $this
     */
    public function clearPluginNamespace()
    {
        $this->getPluginNamespaces()->clear();
        return $this;
    }

    /**
     * Assemble a class name for plugin creation
     * @param string $name
     * @return string
     */
    public function assemblePluginClassName($name)
    {
        $name = ucfirst($name);
        foreach($this->getPluginNamespaces() as $namespace) {
            $className = implode('\\', array($namespace, $name));
            if (class_exists($className)) {
                return $className;
            }
        }
        return '';
    }

    /**
     * Define a plugin validator
     * @param Closure|PluginValidatorInterface $validator
     * @return $this
     */
    public function setPluginValidator($validator)
    {
        $this->pluginValidator = null;
        if (is_object($validator)) {
            if (($validator instanceof PluginValidatorInterface) || ($validator instanceof \Closure)) {
                $this->pluginValidator = $validator;
            }
        }
        return $this;
    }

    /**
     * Retrieve plugin validator
     * @return PluginValidatorInterface
     */
    public function getPluginValidator()
    {
        return $this->pluginValidator;
    }

    /**
     * Validates a plugin name
     * @param string $className
     * @return bool
     */
    public function validatePluginClassName($className)
    {
        if ($this->pluginValidator) {
            if ($this->pluginValidator instanceof \Closure) {
                return $this->pluginValidator($className);
            }
            return $this->pluginValidator->isValid($className);
        }
        return true;
    }
}