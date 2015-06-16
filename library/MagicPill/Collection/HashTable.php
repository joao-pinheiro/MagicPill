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

class HashTable extends Dictionary
{
    /**
     * Adds a scalar item to the hash table
     * @param string $key
     * @param mixed $value
     * @return \MagicPill\Collection\HashTable
     */
    public function addScalar($key, $value)
    {
        if (!$this->readOnly && (null !== $key)) {
            if (array_key_exists($key, $this->data)) {
                $this->data[$key]->add($value);
            } else {
                $this->data[$key] = new Collection(array($value));
                $this->count++;
            }
        }
        return $this;
    }
    
    /**
     * Adds an item to the hash table
     * If value is an array or a collection, child items are treated as leafs
     * @param string $key
     * @param mixed $value
     * @return \MagicPill\Collection\HashTable
     */
    public function add($key, $value)
    {
        if ($value instanceof Collection) {
            $value = $value->toArray();
        }
        
        if (!$this->readOnly && (null !== $key)) {
            if (array_key_exists($key, $this->data)) {
                if (!is_array($value)) {
                    $this->data[$key]->add($value);
                } else {
                    $this->data[$key]->fromArray($value);
                }
            } else {
                if (is_array($value)) {
                    $this->data[$key] = new Collection($value);
                } else {
                    $this->data[$key] = new Collection(array($value));
                }
                $this->count++;
            }
        }
        return $this;
    }

    /**
     * Converts the hashtable to an array
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach($this->data as $key => $collection) {
            $result[$key] = $collection->toArray();
        }
        return $result;
    }

    /**
     * Loads hashtable from associative array
     * Existing keys are rewritten
     * @param array $array
     * @return \MagicPill\Collection\HashTable
     */
    public function fromArray(array $array)
    {
        foreach($array as $key => $value) {
            if (is_array($value)) {
                $value = new Collection($value);
            }
            $this->add($key, $value);
        }
        return $this;
    }

    /**
     * Checks if a given value exists
     * @param mixed $value
     * @return boolean
     */
    public function containsValue($value)
    {
        foreach($this->data as $key => $list) {
            if ($list->containsValue($value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Compares 2 hashtables and return true if exactly equal
     * @param \MagicPill\Collection\HashTable $value
     * @return bool
     */
    public function equals($value)
    {
        if (($value instanceof HashTable) && ($this->count === $value->count())) {
            foreach($value as $key => $obj) {
                if (!is_object($obj)) {
                    return false;
                }
                if (!isset($this->data[$key])) {
                    return false;
                }
                if (!$this->data[$key]->equals($obj)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Append a hashtable
     * No intersection of leafs is done
     * @param \MagicPill\Collection\HashTable $collection
     * @return \MagicPill\Collection\HashDictionary
     */
    public function appendFrom($collection)
    {
        if ($collection instanceof HashTable) {
            foreach($collection as $key => $value) {
                if (!key_exists($key, $this->data)) {
                    $this->data[$key] = $value;
                } else {
                    $this->data[$key]->appendFrom($value);
                }
            }
        }
        $this->count = count($this->data);
        return $this;
    }
}

