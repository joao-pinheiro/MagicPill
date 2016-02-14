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
use MagicPill\Collection\ObjectCollection;
use MagicPill\Exception\ExceptionFactory;
use MagicPill\Mixin\Inherit;

class Logger extends Object
{
    use Inherit;

    /**
     * @var array
     */
    protected $writerList = array();
    
    /**
     * @var string
     */
    protected $writerClassPrefix = '\MagicPill\Util\Log\Writer';
    
    /**
     * Constructor
     * @param array|object $config
     */
    public function __construct($config = array()) 
    {
        $this->configure($config);
    }
    
    /**
     * Configures the logger instance
     * @param array|object $config
     * @throws LoggerInvalidConfigurationFormatException
     * @throws LoggerUnknownWriterException
     * @throws 
     */
    public function configure($config = array())
    {
        if (is_object($config)) {
            if ($config instanceof \MagicPill\Util\Config\Container) {
                $config = $config->toArray();
            }
        } 
        
        if (!is_array($config)) {
            ExceptionFactory::LoggerInvalidConfigurationFormatException('The configuration format is invalid');
        }
        
        if (isset($config['writers'])) {
            foreach($config['writers'] as $writerConfig) {
                if (is_array($writerConfig) && isset($writerConfig['writerName'])) {
                    $className = $writerConfig['writerName'];
                    $writer = $this->buildWriterObject($className, $writerConfig);
                    if (null == $writer) {
                        ExceptionFactory::LoggerUnknownWriterException('The Writer class named ' . $className . 'could not be found');
                    }
                    $this->addWriter($writer);
                } else {
                     ExceptionFactory::LoggerInvalidConfigurationFormatException('The Writer configuration format is invalid');
                }
            }
        }
    }
    
    /**
     * adds a writer to the list
     * @param \MagicPill\Util\Log\Writer\WriterAbstract $writer
     * @return \MagicPill\Util\Log\Logger
     */
    public function addWriter(\MagicPill\Util\Log\Writer\WriterAbstract $writer)
    {
        $writer->setParent($this);
        $this->writerList[] = $writer;
        return $this;
    }
    
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     */    
    public function emergency($message, array $context = array())
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = array())
    {
        $this->log(LogLevel::ALERT, $message, $context);        
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = array())
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array())
    {
        $this->log(LogLevel::ERROR, $message, $context);        
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array())
    {
        $this->log(LogLevel::WARNING, $message, $context);        
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = array())
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = array())
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = array())
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        if (!empty($context)) {
            $message = str_replace(array_keys($context), array_values($context), $message);
        }
        
        $now = new \DateTime();
        $messageParams = array(
            'timestamp' => $now,
            'message' => $message,
            'level' => $level,
            'label' => LogLevel::getLogLevelLabel($level)
        );

        foreach($this->writerList as $writer) {
            $writer->publish($messageParams);
        }        
    }
    
    /**
     * Shutdown writers
     */
    public function shutdown()
    {
        foreach($this->writerList as $writer) {
            $writer->shutdown();
        }
        $this->writerList = array();
    }
    
    /**
     * Retrieves a writer object based on the classname
     * @param string $className
     * @param array|object $config
     * @return \MagicPill\Util\Log\Formatter\FormatterInterface | null
     */
    protected function buildWriterObject($className, $config = array())
    {
        if (false === strpos($className, '\\')) {
            $className = $this->writerClassPrefix . '\\' . $className;
        }
        return class_exists($className) ? new $className($config) : null;
    }    
}