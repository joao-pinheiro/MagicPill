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
 * @package    Container
 * @copyright  Copyright (c) 2014 Joao Pinheiro
 * @version    0.9
 */

namespace MagicPill\Container;

use MagicPill\Collection\Dictionary;
use MagicPill\Exception\ExceptionFactory;

class EntityContainer extends Dictionary
{
    /**
     * Internal list of properties and default values
     * @var array
     */
    protected $entityProperties = array();

    /**
     * Constructor
     * @param mixed $options
     */
    public function __construct($options = array())
    {
        $this->reset();
    }

    /**
     * Batch assign of values
     * @param array $properties
     * @return \MagicPill\Container\EntityContainer
     */
    public function setValues(array $properties)
    {
        foreach ($properties as $field => $value) {
            $this->$field = $value;
        }
        return $this;
    }

    /**
     * Defines the entity properties
     * @param array $properties
     * @return \MagicPill\Container\EntityContainer
     */
    public function setProperties(array $properties)
    {
        $this->entityProperties = array();
        foreach($properties as $property) {
            $this->entityProperties[$property] = null;
        }
        return $this;
    }

    /**
     * Defines properties and assigns value automatically
     * @param array $properties
     * @return \MagicPill\Container\EntityContainer
     */
    public function buildEntity(array $properties)
    {
        $this->setProperties($properties);
        $this->setValues($properties);
        return $this;
    }

    /**
     * Defaults all properties
     * @return \MagicPill\Container\EntityContainer
     */
    public function reset()
    {
        $this->setProperties($this->properties);
        return $this;
    }

    /**
     * Inflates an EntityContainer from an array
     * @param array $array
     * @param boolean $ignoreUnknownProperties
     * @return \MagicPill\Container\EntityContainer
     * @throws EntityContainerException
     */
    public function fromArray(array $array, $ignoreUnknownProperties = false)
    {
        foreach($array as $propery => $value) {
            if (!array_key_exists($property, $this->properties)) {
                if (!$ignoreUnknownProperties) {
                    throw ExceptionFactory::EntityContainerException($this, 'Unknown property ' . $property);
                }
            } else {
                $this->$property = $value;
            }
        }
        return $this;
    }

    /**
     * Converts entity into array
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach($this->properties as $name => $value) {
            $value = $this->$name;
            if (is_object($value) && ($value instanceof EntityContainer)) {
                $value = $value->toArray();
            }
            $result[$name] = $value;
        }
        return $result;
    }
}
