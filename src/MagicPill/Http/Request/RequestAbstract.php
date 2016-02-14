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

namespace MagicPill\MVC\Request;

use MagicPill\Core\Object;
use MagicPill\Collection\Dictionary;
use MagicPill\Exception\ExceptionFactory;

abstract class RequestAbstract extends Object
{ 
    /**
     * @var MagicPill\Collection\Dictionary 
     */
    protected $parameters = null;
        
    /**
     * Retrieve parameter list
     * @return MagicPill\Collection\Dictionary
     */
    public function getParameters()
    {
        if (null === $this->parameters) {
            $this->parameters = new Dictionary();
        }
        return $this->parameters;
    }
    
    /**
     * Bulk adding of parameters
     * @param array $parameters
     * @return \MagicPill\MVC\Request\RequestAbstract
     */
    public function setParameters(array $parameters)
    {
        $this->getParameters()->fromArray($parameters);
        return $this;
    }
    
    /**
     * Define a parameter
     * @param string $name
     * @param mixed $value
     * @return \MagicPill\MVC\Request\RequestAbstract
     */
    public function setParam($name, $value)
    {
        $this->getParameters()->add($name, $value);
        return $this;
    }
    
    /**
     * Retrieve a parameter
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getParam($name, $defaultValue = null)
    {
        return $this->getParameters()->offsetExists($name)
                ? $this->getParameters()->get($name)
                : $defaultValue;
    }
    
    /**
     * Returns true if it is CLI request
     * @return boolean
     */
    public function isCli()
    {
        return (('cli' === php_sapi_name()) || defined('STDIN'));
    }
    
    /**
     * Retrieve a parameter from the $_SERVER superglobal
     * @param string $key
     * @return array|mixed|null
     */
    public function getServer($key = null)
    {
        return (null === $key) 
            ? $_SERVER
            : isset($_SERVER[$key]) ? $_SERVER[$key] : null;
    } 
}