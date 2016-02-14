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
 * @copyright  Copyright (c) 2014-2016 Joao Pinheiro
 * @version    1.0
 */

namespace MagicPill\Util\Log\Formatter;

use MagicPill\Core\Object;

class DefaultFormatter extends Object implements FormatterInterface
{
    /**
     * @var string
     */
    protected $dateFormat = \DateTime::ISO8601;

    /**
     * @var string
     */
    protected $messageString = "{timestamp} {label} ({level}): {message}\n";

    /**
     * Builds the string to be logged
     * @param array $message
     * @return string
     */
    public function format(array $message)
    {
        return str_replace(
            array(
                '{timestamp}',
                '{label}',
                '{level}',
                '{message}'
            ),
            array(
                $message['timestamp']->format($this->dateFormat),
                $message['label'],
                $message['level'],
                $message['message']
            ), $this->messageString);
    }
    
    /**
     * Configure the formatter
     * @param \Traversable $config
     */    
    public function configure(\Traversable $config)
    {
        foreach($config as $key => $value) {
            switch($key) {
                case 'dateFormat':
                    $this->setDateFormat($value);
                    break;
            }
        }
    }
    
    /**
     * Sets the date format
     * @param string $format
     * @return $this
     */
    public function setDateFormat($format)
    {
        $this->dateFormat = $format;
        return $this;
    }

    public function getDateFormat()
    {
        return $this->dateFormat;
    }

}
