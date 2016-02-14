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
 * @package    Log
 * @copyright  Copyright (c) 2014 Joao Pinheiro
 * @version    1.0
 */

namespace MagicPill\Util\Log\Writer;

use MagicPill\Core\Object;
use MagicPill\Mixin\Inherit;
use MagicPill\Util\Log\LogLevel;
use MagicPill\Util\Log\Formatter\FormatterInterface;
use MagicPill\Exception\ExceptionFactory;

abstract class WriterAbstract extends Object implements WriterInterface
{
    use Inherit;

    /**
     * @var integer 
     */
    protected $logLevel = -1;
    
    /**
     * @var array 
     */
    protected $validLevels = [];
    
    /**
     * @var \MagicPill\Util\Log\Formatter\FormatterInterface
     */
    protected $formatter = null;
    
    /**
     * @var string 
     */
    protected $defaultFormatter = '\MagicPill\Util\Log\Formatter\DefaultFormatter';
    
    /**
     * @var string
     */
    protected $formatterClassPrefix = '\MagicPill\Util\Log\Formatter';
    
    /**
     * Constructor
     * @param array|object $config
     */
    public function __construct($config = array())
    {
        $this->validLevels = LogLevel::getValidLevels();
        $this->configure($config);
    }
    
    /**
     * Perform writer configuration
     * @param \Traversable $config
     * @return $this
     * @throws LogWriterInvalidConfigurationFormatException
     * @throws LogWriterInvalidFormatterException
     */
    public function configure(\Traversable $config = [])
    {
        foreach ($config as $key => $value) {
            switch($key) {
                case 'logLevel':
                    $this->setLogLevel($value);
                    break;
                
                case 'formatter':
                    $formatter = $this->buildFormatterObject($value);
                    if (null === $formatter) {
                        ExceptionFactory::LogWriterInvalidFormatterException(sprintf('Formatter Class %s not found', $value));
                    }
                    $this->setFormatter($formatter);
                    break;
                    
                case 'formatterOptions':
                    if (!is_array($value) && !($value instanceof \Traversable)) {
                       ExceptionFactory::LogWriterInvalidConfigurationFormatException('The configuration format for the Formatter is invalid'); 
                    }
                    $this->getFormatter()->configure($value);
                    break;
                    
                default:
                    $this->configureOptions($key, $value);
            }
        }
        return $this;
    }

    /**
     * Defines the log level
     * @param integer $level
     * @return $this
     * @throws LogWriterInvalidLogLevelException
     */
    public function setLogLevel($level)
    {
        $level = (int) $level;
        if (!in_array($level, $this->validLevels)) {
            ExceptionFactory::LogWriterInvalidLogLevelException(sprintf('Invalid log level %s', $level));
        }
        $this->logLevel = $level;
        return $this;
    }
    
    /**
     * Defines the formatter to use
     * @param \MagicPill\Util\Log\Formatter\FormatterInterface $formatter
     * @return $this
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }
    
    /**
     * Retrieves the available formatter or instantiates a default one if not available
     * @return \MagicPill\Util\Log\Formatter\FormatterInterface
     */
    public function getFormatter()
    {
        if (null == $this->formatter) {
            $this->formatter = new $this->defaultFormatter;
            $this->formatter->setParent($this);
        }
        return $this->formatter;
    }
    
    /**
     * Returns true if writer accepts this loglevel
     * @param int $level
     * @return bool
     */
    public function accept($level)
    {
        return is_int($level) 
            && in_array($level, $this->validLevels)
            && ($level <= $this->logLevel);
    }
    
    /**
     * Prepares message for commit
     * @param array $message
     */
    public function publish(array $message)
    {
        if ($this->accept($message['level'])) {
            $this->commit($this->getFormatter()->format($message));
        }
    }
    
    /**
     * Writer shutdown function
     */
    public function shutdown()
    { 
    }
    
    /**
     * Writes the string message to the output
     * @param string $message
     */
    abstract protected function commit($message);
    
    /**
     * Method to be extended on descendant writers for specific configuration
     * @param string $option
     * @param mixed $value
     * @return \MagicPill\Util\Log\Writer\WriterAbstract
     */
    abstract protected function configureOptions($option, $value);

    /**
     * Retrieves a formatter object based on the classname
     * @param string $className
     * @return \MagicPill\Util\Log\Formatter\FormatterInterface | null
     */
    protected function buildFormatterObject($className)
    {
        if (false === strpos($className, '\\')) {
            $className = $this->$formatterClassPrefix . '\\' . $className;
        }
        return class_exists($className) ? new $className : null;
    }
}
