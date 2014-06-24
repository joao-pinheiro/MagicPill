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
use MagicPill\Exception\ExceptionFactory;

class Manager extends Load
{
     /**
     * Resource dictionary
     * @var MagicPill\Collection\Dictionary
     */
    protected $resources = null;

    /**
     * User-defined dependencies for createResourceObject
     * @var array
     */
    protected $dependencies = array();

    /**
     * Resource Manager Constructor
     * @param array $options
     */
    public function __construct($options = array())
    {
        if ($options instanceof \Zend_Config) {
            $options = $options->toArray();
        }
        $this->setOptions($options);
    }

    /**
     * Retrieves an resource or loads an existing one
     * @param string $resourceName
     * @return object
     */
    public function getResource($resourceName)
    {
        $dictionary = $this->getResourceDictionary();
        if ($dictionary->containsKey($resourceName)) {
            return $dictionary->get($resourceName);
        }

        $resource = $this->loadResource($resourceName);
        if (!empty($resource)) {
            $dictionary->add($resourceName, $resource);
        }

        return $resource;
    }

    /**
     * Replaces a resource entry with the value
     *
     * @param string $resourceName
     * @param mixed $value
     * @return \MagicPill\Resource\Resource
     */
    public function updateResource($resourceName, $value)
    {
        $dictionary = $this->getResourceDictionary();
        if ($dictionary->containsKey($resourceName)) {
            $dictionary->remove($resourceName);
            $dictionary->add($resourceName, $value);
        }
        return $this;
    }

    /**
     * Adds a custom resource
     * @param string $resourceName
     * @param mixed $value
     * @return \MagicPill\Resource\Manager
     */
    public function addResource($resourceName, $value)
    {
        $dictionary = $this->getResourceDictionary();
        if (!$dictionary->containsKey($resourceName)) {
            $dictionary->add($resourceName, $value);
        }
        return $this;
    }

    /**
     * Removes a resource by name
     * @param string $resourceName
     * @return \MagicPill\Resource\Manager
     */
    public function removeResource($resourceName)
    {
        $this->getResourceDictionary()->remove($resourceName);
        return $this;
    }

    /**
     * Returns true if the given resource exists in the collection
     * @param string $resourceName
     * @return boolean
     */
    public function hasResource($resourceName)
    {
        return $this->getResourceDictionary()->containsKey($resourceName);
    }

    /**
     * Sets the resource manager options
     * @param array $options
     * @return \MagicPill\Resource\Manager
     */
    public function setOptions($options)
    {
        if (is_array($options)) {
            foreach($options as $option => $value) {
                switch($option) {
                    case 'baseClassCheck':
                        $this->setBaseClassCheck($value);
                        break;

                    case 'baseClass':
                        $this->setBaseClass($value);
                        break;

                    case 'raiseException':
                        $this->setRaiseException($value);
                        break;

                    case 'namespaces':
                        $this->setNamespace($value);
                        break;

                    case 'parent':
                        $this->setParent($value);
                        break;

                    case 'parentProxy':
                        $this->setParentProxy($value);
                        break;

                    default:
                        ExceptionFactory::ManagerInvalidOptionException($option);
                }
            }
        }
        return $this;
    }

    /**
     * Retrieve user-defined dependencies
     * @return array
     */
    protected function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Retrieves the resource dictionary
     * @return MagicPill\Collection\Dictionary
     */
    protected function getResourceDictionary()
    {
        if (null == $this->resources) {
            $this->resources = new Collection\Dictionary();
        }
        return $this->resources;
    }
}
