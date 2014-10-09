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

class HashDictionary extends HashTable
{
    /**
     * Adds an item to the hash Dictionary
     * @param string $hash
     * @param string|array $key
     * @param mixed $value
     * @return \MagicPill\Collection\HashDictionary
     */
    public function add($hash, $key, $value = null)
    {
        if (!$this->readOnly && (!empty($key)) && (null !== $hash)) {
            // to allow short notation on add($hash, array($key => $value));
            if (is_array($key)) {
                $values = array_values($key);
                $keys = array_keys($key);
                $value = array_shift($values);
                $key = array_shift($keys);
            }

            if (array_key_exists($hash, $this->data)) {
                $this->data[$hash]->add($key, $value);
            } else {
                $this->data[$hash] = new Dictionary(array($key => $value));
                $this->count++;
            }
        }
        return $this;
    }

    /**
     * Compares 2 hash dictionaries
     * @param \MagicPill\Collection\HashDictionary $dictionary
     * @return bool
     */
    public function equals($value)
    {
        if (($value instanceof HashDictionary) && ($this->count === $value->count())) {
            foreach($value as $key => $obj) {
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
     * Append a hash dictionary
     * @param \MagicPill\Collection\HashDictionary $collection
     * @return \MagicPill\Collection\HashDictionary
     */
    public function appendFrom($collection)
    {
        if ($collection instanceof HashDictionary) {
            foreach($collection as $key => $value) {
                if (!array_key_exists($key, $this->data)) {
                    $this->data[$key] = $value;
                } else {
                    $this->data[$key]->appendFrom($value);
                }
            }
            $this->count = count($this->data);
        }
        return $this;
    }
}

