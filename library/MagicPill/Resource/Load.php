<?php
/**
 * MagicPill
 *
 * Copyright (c) 2014, Joao Pinheiro
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * 1. Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation
 * and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF
 * THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   MagicPill
 * @package    Resource
 * @copyright  Copyright (c) 2014 Joao Pinheiro
 * @version    0.9
 */

namespace MagicPill\Resource;

use MagicPill\Collection;

class Load extends \MagicPill\Core\Object
{
   /**
     * Base class name to check inheritance
     * @var string
     */
    protected $baseClass = '';

    /**
     * @var boolean
     */
    protected $baseClassCheck = true;

    /**
     * @var boolean
     */
    protected $raiseException = true;

    /**
     * @var boolean
     */
    protected $parentProxy = false;

    /**
     * List of namespaces to use
     * @var array
     */
    protected $namespaces = array();


    /**
     * Enables or disables checking of base class for resources
     * @param boolean $status
     * @return \MagicPill\Resource\Resource
     */
    public function setBaseClassCheck($status)
    {
        $this->baseClassCheck = (bool) $status;
        return $this;
    }

    /**
     * Defines the base class for inheritance checking
     * @param string $name
     * @return \MagicPill\Resource\Resource
     */
    public function setBaseClass($name)
    {
        $this->baseClass = $name;
        return $this;
    }

    /**
     * Enables/disables triggering of exceptions
     * @param boolean $status
     * @return \MagicPill\Resource\Resource
     */
    public function setRaiseException($status)
    {
        $this->raiseException = (bool) $status;
        return $this;
    }

    /**
     * Enables/disables proxying of parent when creating resources
     * If parent proxy is enabled, the parent object for resources will be the
     * parent object of the resource manager
     * @param boolean $status
     * @return \MagicPill\Resource\Resource
     */
    public function setParentProxy($value)
    {
        $this->parentProxy = (bool) $value;
        return $this;
    }

    /**
     * Retrieve the list of available namespaces
     * @return array
     */
    public function getNamespaceList()
    {
        return $this->namespaces;
    }

    /**
     * Add a single namespace to the list of namespaces
     * @param string $namespace
     * @return \MagicPill\Resource\Resource
     */
    public function addNamespace($namespace)
    {
        if (!in_array($namespace, $this->namespaces)) {
            array_unshift($this->namespaces, $namespace);
        }
        return $this;
    }

    /**
     * Defines the list of namespaces to use
     * @param array|string $namespaces
     * @return \MagicPill\Resource\Resource
     */
    public function setNamespace($namespaces)
    {
        if (is_array($namespaces)) {
            $this->namespaces = $namespaces;
        } elseif (is_string($namespaces)) {
            $this->namespaces = array($namespaces);
        }
        return $this;
    }

   /**
     * Loads the resource with the given name
     * @param sttring $name
     * @return object
     * @throws ManagerException
     */
    public function loadResource($name)
    {
        $name = ucfirst($name);
        $className = '';
        foreach ($this->namespaces as $namespace) {
            $tempName = $namespace . '\\' . $name;
            if (class_exists($tempName)) {
                $className = $tempName;
               break;
            }
        }

        if (empty($className)) {
            if ($this->raiseException) {
                throw new ManagerException('Could not locate resource with name ' . $name);
            }
            return;
        }

        $result = $this->createResourceObject($className);
        if ($this->baseClassCheck) {
            if (!($result instanceof $this->baseClass)) {
                if ($this->raiseException) {
                    throw new ManagerException(get_class($result) . ' does not extend/implement ' . $this->baseClass);
                }
                return null;
            }
        }
        return $result;
    }

    /**
     * Initializes a resource object
     * Should be overriden in implementation specific usages to inject dependencies
     * @param string $className
     * @return object
     */
    protected function createResourceObject($className)
    {
        $object = new $className();
        $this->setResourceObjectParent($object);

        return $object;
    }

    /**
     * Sets the parent attribute in the specified object
     * @param object $object
     */
    protected function setResourceObjectParent($object)
    {
        if (method_exists($object, 'setParent')) {
            if ($this->parentProxy) {
                $object->setParent($this->getParent());
            } else {
                $object->setParent($this);
            }
        }
    }

    /**
     * Retrieves the defined base class
     * @return string
     */
    protected function getBaseClass()
    {
        return $this->baseClass;
    }
}