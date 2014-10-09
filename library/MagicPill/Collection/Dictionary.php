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

class Dictionary implements DictionaryInterface
{
    /**
     * @var integer
     */
    protected $count = 0;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * var bool
     */
    protected $readOnly = false;

    /**
     * Constructor
     * @param array $data
     */
    public function __construct($data = array())
    {
        if (is_array($data) && (!empty($data))) {
            $this->fromArray($data);
        } elseif ($data instanceof Dictionary) {
            $this->appendFrom($data);
        }
    }

    /**
     * Adds an item to the dictionary
     * @param string $key
     * @param mixed $value
     * @return \MagicPill\Collection\Dictionary
     */
    public function add($key, $value = null)
    {
        if (!$this->readOnly && (null !== $key)) {
            $this->data[$key] = $value;
            $this->count = count($this->data);
        }
        return $this;
    }

    /**
     * Loads dictionary from associative array
     * Existing keys are rewritten
     * @param array $array
     * @return \MagicPill\Collection\Dictionary
     */
    public function fromArray(array $array)
    {
        foreach($array as $key => $value) {
            $this->add($key, $value);
        }
        return $this;
    }

    /**
     * Clears the dictionary and internal counters
     * @return \MagicPill\Collection\Dictionary
     */
    public function clear()
    {
        $this->data = array();
        $this->count = 0;
        $this->readOnly = false;
        reset($this->data);
        return $this;
    }

    /**
     * Returns the collection hash
     * @return string
     */
    public function getHashCode()
    {
        return spl_object_hash($this);
    }

    /**
     * Returns the readOnly status
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * Makes the current dictionary read-only
     * @return \MagicPill\Collection\Dictionary
     */
    public function protect()
    {
        $this->readOnly = true;
        return $this;
    }
    
    /**
     * Makes the current dictionary read-write
     * @return \MagicPill\Collection\Dictionary
     */
    public function unprotect()
    {
        $this->readOnly = false;
        return $this;
    }
    
    /**
     * Returns true if the collection is empty
     * @return boolean
     */
    public function isEmpty()
    {
        return (0 == $this->count);
    }

    /**
     * Returns the current value
     * @return mixed
     */
    public function current()
    {
        if ($this->valid()) {
            return current($this->data);
        }
        return null;
    }

    /**
     * Moves cursor to next item
     * @return \MagicPill\Collection\Dictionary
     */
    public function next()
    {
        next($this->data);
        return $this;
    }

    /**
     * Returns current key
     * @return integer
     */
    public function key()
    {
        if ($this->valid()) {
            return key($this->data);
        }
        return null;
    }

    /**
     * Seeks internal pointer to a given position
     * @param string $position
     * @return \MagicPill\Collection\Dictionary
     */
    public function seek($position)
    {
        reset($this->data);
        while(key($this->data) !== $position && $this->valid()) {
            next($this->data);
        }
        return $this;
    }

    /**
     * Checks if current offset is valid
     * @return bool
     */
    public function valid()
    {
        $key = key($this->data);
        return null !== $key;
    }

    /**
     * Resets the internal pointer to the first element
     * @return \MagicPill\Collection\Dictionary
     */
    public function rewind()
    {
        reset($this->data);
        return $this;
    }

    /**
     * Returns the number of elements
     * @return integer
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * Alias for offsetGet
     * @param mixed $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * Returns true if the given key exists in the dictionary
     *
     * @param integer $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data);
    }

    /**
     * Returns the value stored for the given key
     * @param string|integer $offset
     * @return mixed|null
     */
    public function offsetGet($offset)
    {
        if (array_key_exists($offset, $this->data)) {
            return $this->data[$offset];
        }
        return null;
    }

    /**
     * Sets the value for a given key
     * @param string|integer $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (!$this->readOnly) {
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
     * Returns true if the key exists in the dictionary
     * @param mixed $key
     * @return bool
     */
    public function containsKey($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Checks if a given value exists
     * @param mixed $value
     * @return boolean
     */
    public function containsValue($value)
    {
        return (false !== array_search($value, $this->data));
    }

    /**
     * Compares 2 dictionaries
     * @param \MagicPill\Collection\Dictionary $value
     * @return bool
     */
    public function equals(DictionaryInterface $value)
    {
        if (($value instanceof Dictionary) && ($this->count === $value->count())) {
            foreach($value as $key => $item) {
                if (!isset($this->data[$key])) {
                    return false;
                }
                if ($this->data[$key] !== $item) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Returns all the dictionary keys
     * @return array
     */
    public function keys()
    {
        return array_keys($this->data);
    }

    /**
     * Returns all the dictionary values
     * @return array
     */
    public function values()
    {
        return array_values($this->data);
    }

    /**
     * Removes an item from the dictionary
     * @param string $key
     * @return \MagicPill\Collection\Dictionary
     */
    public function remove($key)
    {
        if (!$this->readOnly && array_key_exists($key, $this->data)) {
            unset($this->data[$key]);
            $this->count--;
        }
        return $this;
    }

    /**
     * Append a dictionary
     * Only non-existing keys are added
     * @param \MagicPill\Collection\Dictionary|array $collection
     * @return \MagicPill\Collection\Dictionary
     */
    public function appendFrom($collection)
    {
        if (($collection instanceof Dictionary) || is_array($collection)) {
            foreach($collection as $key => $value) {
                $this->add($key, $value);
            }
        }
        return $this;
    }

    /**
     * Returns the dictionary as an associative array
     * @return array
     */
    public function toArray()
    {
        return $this->data;
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
