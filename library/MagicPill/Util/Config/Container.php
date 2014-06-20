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
 * @package    Util\Config
 * @copyright  Copyright (c) 2014 Joao Pinheiro
 * @version    0.9
 */

namespace MagicPill\Util\Config;

use MagicPill\Collection\Dictionary;

class Container extends Dictionary
{
    /**
     * @var boolean
     */
    protected $ignoreSeparator = false;

    /**
     * @var string
     */
    protected $separator = '.';

    /**
     * Constructor
     * @param array|iterator list of segments to parse
     */
    public function __construct()
    {
        foreach(func_get_args() as $segment) {
            $this->parse($segment);
        }
    }

    /**
     * Parses a segment and sets appropriate properties
     * @param array|iterator $segment
     * @return \MagicPill\Util\Config
     */
    public function parse($segment)
    {
        if (is_array($segment) || $this->isIterator($segment)) {
            foreach($segment as $key => $value) {
                if (!$this->ignoreSeparator && (false !== strpos($key, $this->separator))) {
                    $map = explode($this->separator, $key);
                    $key = array_shift($map);
                    $map = array_reverse($map);
                    foreach ($map as $token) {
                        $value = array($token => $value);
                    }
                }
                $this->addLeaf($key, $value);
            }
        }
        return $this;
    }

    /**
     * Converts existing tree into array
     * @return array
     */
    public function toArray()
    {
        $result = array();
        foreach(parent::toArray() as $key => $value) {
            if ($value instanceof self) {
                $value = $value->toArray();
            }
            $result[$key] = $value;
        }
        return $result;
    }

    /**
     * Returns the tree as an array as default function call
     * @return array
     */
    public function __invoke()
    {
        return $this->toArray();
    }
    
    /**
     * Checks if parameter is an iterator
     * @return bool
     */
    protected function isIterator($segment)
    {
        return is_object($segment) && ($segment instanceof \Iterator);
    }

    /**
     * Adds a leaf to the tree
     * @param string $key
     * @param mixed $value
     */
    protected function addLeaf($key, $value)
    {
        if (is_array($value) || $this->isIterator($value)) {
            if ($this->offsetExists($key)) {
                if ($this->$key instanceof self) {
                    $this->$key->parse($value);
                }
            } else {
                $value = new self($value);
                $this->offsetSet($key, $value);
            }
        } else {
            $this->offsetSet($key, $value);
        }
    }
}

