<?php
/**
 * MagicPill
 *
 * Copyright (c) 2014-2016, Joao Pinheiro
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
 * @copyright  Copyright (c) 2014-2016 Joao Pinheiro
 * @version    1.0
 */

namespace MagicPill\Collection;

class Container extends Dictionary
{
    /**
     * Constructor
     * @param array|\Traversable $data
     */
    public function __construct($data = [])
    {
        $this->fromArray($data);
    }

    /**
     * Adds an item to the tree
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function add($key, $value = null)
    {
        $this->offsetSet($key, $value);
        return $this;
    }

    /**
     * Sets the value for a given key
     * @param string|integer $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (!$this->isReadOnly()) {
            if (is_array($value) || $value instanceof \Traversable) {
                $value = new self($value);
            }
            $this->data[$offset] = $value;
            $this->count = count($this->data);
        }
    }

    /**
     * Removes an entry from the dictionary
     * @param string|integer $offset
     */
    public function offsetUnset($offset)
    {
        if  (array_key_exists($offset, $this->data) && !$this->readOnly) {
            unset($this->data[$offset]);
            $this->count--;
        }
    }

    /**
     * Compares 2 tuple trees
     * @param $this $value
     * @return bool
     */
    public function equals($value)
    {
        if (($value instanceof self) && ($this->count === $value->count())) {
            foreach($value as $key => $item) {
                if (!isset($this->data[$key])) {
                    return false;
                }

                if ($item instanceof self) {
                    if (!$item->equals($this->data[$key])) {
                        return false;
                    }
                } else {
                    if ($this->data[$key] !== $item) {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Append an iterator
     * @param \Traversable|array $collection
     * @return $this
     */
    public function appendFrom($collection)
    {
        if (($collection instanceof \Traversable) || is_array($collection)) {
            foreach($collection as $key => $value) {
                if (isset($this->data[$key]) && ($this->data[$key] instanceof self)) {
                        $this->data[$key]->appendFrom($value);
                } else {
                    $this->add($key, $value);
                }
            }
        }
        return $this;
    }

    /**
     * Returns the container as an associative array
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach($this->data as $key => $value) {
            $result[$key] = ($value instanceof Container)
                ? $value->toArray()
                : $value;
        }
        return $result;
    }

    /**
     * Magic setter
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
        return $this;
    }

    /**
     * Magic getter
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }
}
