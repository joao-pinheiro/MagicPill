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
 * @package    IO
 * @copyright  Copyright (c) 2014 Joao Pinheiro
 * @version    0.9
 */

namespace MagicPill\IO\File;

use MagicPill\Exception\ExceptionFactory;

class FileWriter extends FileAbstract
{
    /**
     * @var array 
     */
    protected $validModes = array('r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+');
    
    /**
     * Constructor
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        $this->setName($fileName);
    }
    
    /**
     * Opens a file for writing
     * @param string $mode
     * @return \MagicPill\IO\File\FileReader
     * @throws FileInvalidModeException
     * @throws FileOpenedException
     */
    public function open($mode = 'w')
    {
        if (!$this->isValidMode($mode)) {
            ExceptionFactory::FileInvalidModeException('Invalid mode ' . $mode . ' specified');
        }
        
        if (!$this->isOpened()) {
            $this->setHandle(fopen($this->getName(), $mode));
        } else {
            ExceptionFactory::FileOpenedException('File is already opened');
        }
        return $this;
    }    
    
    /**
     * Write to File
     * @param string|mixed $content
     * @param integer|null $length
     * @return integer
     */
    public function write($content, $length = null)
    {
        if (!$this->isOpened()) {
            ExceptionFactory::FileNotOpenedException('File is not opened');
        }
        if ((null === $length) || (is_int($length))) {
            return fwrite($this->getHandle(), $content);
        }
        return fwrite($this->getHandle(), $content, $length);
    }
}