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
 * @package    Log
 * @copyright  Copyright (c) 2014 Joao Pinheiro
 * @version    0.9
 */

namespace MagicPill\Util\Log;

use MagicPill\Core\Object;
use MagicPill\Exception\ExceptionFactory;
use MagicPill\Mixin\Inherit;

class LogManager extends Object
{
    use Inherit;

    /**
     * @var array
     */
    protected $loggerList = array();
    
    /**
     * @var string 
     */
    protected $defaultLoggerName = 'default';
    
    /**
     * Constructor
     * @param \MagicPill\Util\Config|array $config
     */
    public function __construct($config = array())
    {
        if ($config instanceOf \MagicPill\Util\Config) {
            $config = $config->toArray();
        }
        foreach ($config as $name => $config) {
            $this->addLog($name, $this->createLog($config));
        }
    }
  
    /**
     * Creates a new Logger instance
     * @param \MagicPill\Util\Config|array $config
     * @return \MagicPill\Util\Log\Logger
     */
    static public function createLog($config = array())
    {
        if ($config instanceOf \MagicPill\Util\Config) {
            $config = $config->toArray();
        }
        return new Logger($config);
    }
    
    /**
     * Defines the default logger name
     * @param string $to
     * @return \MagicPill\Util\Log\LogManager
     */
    public function setDefaultLoggerName($to)
    {
        $this->defaultLoggerName = $to;
        return $this;
    }
    
    /**
     * Retrieve the default logger name
     * @return string
     */
    public function getDefaultLoggerName()
    {
        return $this->defaultLoggerName;
    }
    
    /**
     * Registers a new log instance with the specified namespace
     * @param string $name
     * @param \MagicPill\Util\Log\Logger $log
     * @return \MagicPill\Util\Log\LogManager
     */
    public function addLog($name, Logger $log)
    {
        if (isset($this->loggerList[$name])) {
           ExceptionFactory::LogManagerDuplicateLoggerNameException('Log with name ' . $name . ' already exists');
        }
        $this->loggerList[$name] = $log;
        $log->setParent($this);
        
        return $this;
    }
    
    /**
     * Retrieves a registered logger instance by namespace
     * @param string $name
     * @return \MagicPill\Log\Logger
     */
    public function getLog($name)
    {
        return isset($this->loggerList[$name])
            ? $this->loggerList[$name] 
            : null;
    }
    
    /**
     * 
     * @return \MagicPill\Log\Logger
     */
    public function getDefaultLog()
    {
        return $this->getLog($this->defaultLoggerName);
    }
    
    /**
     * Shutdown loggers
     */
    public function shutdown()
    {
        foreach($this->loggerList as $logger) {
            $logger->shutdown();
        }
        $this->loggerList = array();
    }
    
    /**
     * Magically redirects method calls to the default log handler
     * @param string $name
     * @param mixed $arguments
     */
    public function __call($name, $arguments)
    {
        $logger = $this->getDefaultLog();        
        if (null == $logger) {
            ExceptionFactory::LogManagerNamespaceNotFoundException('Logger with name ' . $name . ' not found');
        }
        call_user_func_array(array($logger, $name), $arguments);
    }
    
    /**
     * Magic method for property retrieval
     * @param string $name
     * @return Log
     */
    public function __get($name)
    {
        $log = $this->getLog($name);
        if (null == $log) {
            ExceptionFactory::LogManagerLoggerNotFoundException('Logger with name ' . $name . ' not found');
        }
        return $log;
    }
}
