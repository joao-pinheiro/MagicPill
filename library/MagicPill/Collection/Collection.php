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

class Collection implements ListInterface
{
    /**
     * @var integer
     */
    protected $count = 0;

    /**
     *
     * @var integer
     */
    protected $cursor = -1;

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
        }
    }

    /**
     * Loads collection from array
     * @param array $array
     * @return \MagicPill\Collection\Collection
     */
    public function fromArray(array $array)
    {
        foreach($array as $value) {
            $this->add($value);
        }
        return $this;
    }

    /**
     * Adds an item to the collection
     * @param mixed $value
     */
    public function add($value)
    {
        if (!$this->readOnly) {
            $this->data[] = $value;
            $this->count++;
        }
    }

    /**
     * Clears the collection an internal counters
     * @return \MagicPill\Collection\Collection
     */
    public function clear()
    {
        $this->data = array();
        $this->count = 0;
        $this->readOnly = false;
        return $this;
    }

    /**
     * Checks if a given value exists
     *
     * @param mixed $value
     * @return boolean
     */
    public function containsValue($value)
    {
        return in_array($value, $this->data);
    }

    /**
     * Returns the collection hash
     * @return bool
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
     * Makes the current collection read-only
     * @return \MagicPill\Collection\Collection
     */
    public function protect()
    {
        $this->readOnly = true;
        return $this;
    }

    /**
     * Returns true if the collection is empty
     * @return boolean
     */
    public function isEmpty()
    {
        return 0 == $this->count;
    }

    /**
     * Seeks to specified offset
     *
     * @param integer $position
     * @return \MagicPill\Collection\Collection
     */
    public function seek($position)
    {
        if (($position < $this->count) && ($position >= 0)) {
            $this->cursor = $position;
        }
        return $this;
    }

    /**
     * Returns the current value
     *
     * @return mixed
     */
    public function current()
    {
        if ($this->valid()) {
            return $this->data[$this->cursor];
        }
        return null;
    }

    /**
     * Moves cursor to next item
     * @return \MagicPill\Collection\Collection
     */
    public function next()
    {
        if ($this->cursor < $this->count) {
            $this->cursor++;
        }
        return $this;
    }

    /**
     * Returns current offset
     * @return integer
     */
    public function key()
    {
        if ($this->valid()) {
            return $this->cursor;
        }
        return null;
    }

    /**
     * Checks if current offset is valid
     * @return bool
     */
    public function valid()
    {
        return ($this->cursor >= 0) && ($this->cursor < $this->count);
    }

    /**
     * Resets the internal pointer to the first element
     * @return \MagicPill\Collection\Collection
     */
    public function rewind()
    {
        if ($this->count > 0) {
            $this->cursor = 0;
        } else {
            $this->cursor = -1;
        }
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
     * @param integer $index
     * @return mixed
     */
    public function get($index)
    {
        return $this->offsetGet($index);
    }

    /**
     * Returns true if the given index exists in the collection
     *
     * @param integer $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        $offset = (int) $offset;
        return ($offset >= 0) && ($this->count > 0) && ($offset < $this->count);
    }

    /**
     * Returns the value stored on the given index
     * @param integer $offset
     * @return null
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->data[(int) $offset];
        }
        return null;
    }

    /**
     * Sets the value for a given index
     *
     * @param integer $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if ($this->offsetExists($offset) && !$this->readOnly) {
            $this->data[(int) $offset] = $value;
        }
    }

    /**
     * Removes an index from the list
     * @param integer $offset
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset) && !$this->readOnly) {
            unset($this->data[$offset]);
            $this->count--;
        }
    }

    /**
     * Alias for Add - Adds an item to the end of the collection
     *
     * @param mixed $value
     * @return \MagicPill\Collection\Collection
     */
    public function push($value)
    {
        $this->add($value);
        return $this;
    }

    /**
     * Removes and returns an item from the end of the collection
     *
     * @return mixed
     */
    public function pop()
    {
        if (!$this->readOnly && ($this->count() > 0)) {
            $this->count--;
            return array_pop($this->data);
        }
        return null;
    }

    /**
     * Remove and return an item from the beginning of the collection
     * @return mixed|null
     */
    public function shift()
    {
        if (!$this->readOnly && ($this->count() > 0)) {
            $this->count--;
            return array_shift($this->data);
        }
        return null;
    }

    /**
     * Add an element to the beginning of the collection
     * @param mixed $value
     * @return \MagicPill\Collection\Collection
     */
    public function unshift($value)
    {
        if (!$this->readOnly) {
            $this->count++;
            array_unshift($this->data, $value);
        }
        return $this;
    }

    /**
     * Returns the collection as an array
     * @return array
     */
    public function toArray()
    {
        return array_values($this->data);
    }

    /**
     * Appends a list
     * @param \MagicPill\Collection\Collection $collection
     * @return \MagicPill\Collection\Collection
     */
    public function appendFrom(Collection $collection)
    {
        if(!$this->readOnly) {
            foreach ($collection as $item) {
                $this->data[] = $item;
                $this->count++;
            }
        }

        return $this;
    }

    /**
     * Compares 2 lists
     * @param \MagicPill\Collection\Collection $list
     * @return bool
     */
    public function equals(Collection $list)
    {
        if ($this->count === $list->count()) {
            foreach($list as $value) {
                if (!in_array($value, $this->data)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Alias for add()
     * @param mixed $value
     * @return \MagicPill\Collection\Collection
     */
    public function append($value)
    {
        $this->add($value);
        return $this;
    }

    /**
     * Alias for unshift()
     * @param mixed $value
     * @return \MagicPill\Collection\Collection
     */
    public function prepend($value)
    {
        $this->unshift($value);
        return $this;
    }
}
