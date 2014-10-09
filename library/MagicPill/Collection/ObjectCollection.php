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
 * @package    Collection
 * @copyright  Copyright (c) 2014 Joao Pinheiro
 * @version    0.9
 */

namespace MagicPill\Collection;

class ObjectCollection extends Collection
{
    /**
     * @var mixed
     */
    protected $objectType = null;

    /**
     * Constructor
     * @param array $data
     * @param string $objectType
     */
    public function __construct($data = array(), $objectType = null)
    {
        if (null !== $objectType) {
            $this->setObjectType($objectType);
        }
        if (is_array($data)) {
            $this->fromArray($data);
        } elseif ($data instanceof Collection) {
            $this->appendFrom($data);
        }
    }

    /**
     * Adds an item to the collection
     * @param mixed $value
     * @return \MagicPill\Collection\ObjectCollection
     */
    public function add($value)
    {
        if (!$this->readOnly) {
            if (is_object($value) && ($this->isValidType($value))) {
                $this->data[] = $value;
                $this->count++;
            }
        }
        return $this;
    }

    /**
     * Checks if type of object is valid
     * @param object $value
     * @return bool
     */
    public function isValidType($value)
    {
        $type = $this->getObjectType();
        return ($type !== null) ? ($value instanceof $type) : true;
    }

    /**
     * Retrieve object type
     * @return string
     */
    public function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * Defines the collection object type
     * @param string $type
     * @param \MagicPill\Collection\ObjectCollection
     */
    public function setObjectType($type)
    {
        $this->objectType = $type;
        return $this;
    }

    /**
     * Appends a collection
     * @param \MagicPill\Collection\Collection $collection
     * @return \MagicPill\Collection\Collection
     */
    public function appendFrom(Collection $collection)
    {
        foreach ($collection as $item) {
            $this->add($item);
        }
        return $this;
    }

    /**
     * Compares 2 collections
     * @param \MagicPill\Collection\Collection $collection
     * @return bool
     */
    public function equals(ObjectCollection $collection)
    {
        if ($this->count === $collection->count()) {
            foreach($collection as $value) {
                if (!in_array($value, $this->data)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}
