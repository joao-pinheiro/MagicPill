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
 * @package    MVC
 * @copyright  Copyright (c) 2014 Joao Pinheiro
 * @version    0.9
 */

namespace MagicPill\MVC\Response;

use MagicPill\Core\Object;
use MagicPill\Collection\Dictionary;
use MagicPill\Exception\ExceptionFactory;

class Http extends ResponseAbstract
{
    /**
     * @var MagicPill\Collection\Dictionary 
     */    
    protected $properties = null;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Retrieve header collection
     * @return MagicPill\Collection\Dictionary 
     */
    protected function getHeaders()
    {
        $prop = $this->getProperties();
        if (!$prop->offsetExists('headers')) {
            $prop->add(
                    'headers',
                    new Dictionary($this->detectHeaders())
                    );
        }
        return $prop->get('headers');
    }
    
    /**
     * Retrieve a header value
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getHeader($name, $defaultValue = null)
    {
        return $this->getHeaders()->offsetExists($name)
                ? $this->getHeaders()->get($name)
                : $defaultValue;
    }
    
    /**
     * Defines a header value
     * @param string $name
     * @param mixed $value
     * @return \MagicPill\MVC\Request\Http
     */
    public function setHeader($name, $value)
    {
        $this->getHeaders()->add($name, $value);
        return $this;
    }
        
    /**
     * Retrieve the request property list
     * @return \MagicPill\Collection\Dictionary
     */
    protected function getProperties()
    {
        if (null == $this->properties) {
            $this->properties = new Dictionary();
        }
        return $this->properties;
    }
    
    public function setBody($body)
    {
        
    }
    
    public function setHeaders(array $headers)
    {
        
    }
    
    public function setHttpVersion($version)
    {
        
    }
    
    public function setResponseCode($code)
    {
        
    }
    
    public function setResponseStatus($status)
    {
        
    }
    
    public function setType($type)
    {
        
    }
    
}
