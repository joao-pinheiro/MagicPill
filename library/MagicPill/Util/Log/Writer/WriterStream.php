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

namespace MagicPill\Util\Log\Writer;

use MagicPill\IO\File\FileWriter;
use MagicPill\Exception\ExceptionFactory;

class WriterStream extends WriterAbstract
{
    /**
     * @var \MagicPill\IO\File\FileWriter 
     */
    protected $stream = null;
   
    /**
     *
     * @var string 
     */
    protected $streamName = '';
    
    /**
     * @var string
     */
    protected $streamMode = 'a';
    
    /**
     * Writes the string message to the output
     * @param string $message
     */
    protected function commit($message)
    {
        $this->getStream()->write($message);
    }
    
    /**
     * Method to be extended on descendant writers for specific configuration
     * @param string $option
     * @param mixed $value
     * @return \MagicPill\Util\Log\Writer\WriterAbstract
     */
    protected function configureOptions($option, $value)
    {
        switch($option) {
            case 'streamName':
                $this->streamName = $value;
                break;
            
            case 'streamMode':
                $this->streamMode = $value;
                break;            
        }
    }
    
    /**
     * Retrieve the stream writer
     * @return \MagicPill\IO\File\FileWriter
     */
    public function getStream()
    {
        if (null == $this->stream) {
            if (empty($this->streamName)) {
                ExceptionFactory::LogWriterStreamInvalidNameException('Invalid name for stram log');
            }
            $this->stream = new FileWriter($this->streamName);
            $this->stream->open($this->streamMode);
        }
        return $this->stream;
    }
    
    /**
     * Destructor
     */
    public function shutdown()
    {
        if ((null !== $this->stream) && ($this->stream->isOpened())) {
            $this->stream->close();
        }
    }
}
