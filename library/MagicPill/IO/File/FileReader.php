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

class FileReader extends FileAbstract
{
    /**
     * Constructor
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        $this->setName($fileName);
    }

    /**
     * Opens a file for reading
     * @param string $mode
     * @return \MagicPill\IO\File\FileReader
     * @throws FileNotFoundException
     * @throws FileOpenedException
     */
    public function open($mode = 'r')
    {
        if (!$this->isOpened()) {
            if ($this->exists()) {
                $this->setHandle(fopen($this->getName(), $mode));
            } else {
                ExceptionFactory::FileNotFoundException('File ' . $this->getName() . ' does not exist');
            }
        } else {
            ExceptionFactory::FileOpenedException('File is already opened');
        }
        return $this;
    }

    /**
     * Reads $length bytes from file
     * @param integer $length
     * @return string
     */
    public function read($length = null)
    {
        if (null == $length) {
            $length = $this->getSize();
        }
        return fread($this->getHandle(), $length);
    }

    /**
     * Retrieves all the contents of the file as a string
     * This function does not require open()
     * @return string
     * @throws FileNotFoundException
     */
    public function readAll()
    {
        if ($this->exists()) {
            return file_get_contents($this->getName());
        }
        ExceptionFactory::FileNotFoundException('File '. $this->getName() . ' does not exist');
    }

    /**
     * Reads a textfile into an array
     * This function does not require open()
     * @return array
     * @throws FileNotFoundException
     */
    public function readToArray()
    {
        if ($this->exists()) {
            return file($this->getName());
        }
        ExceptionFactory::FileNotFoundException('File '. $this->getName() . ' does not exist');
    }

    /**
     * Seeks to an offset
     * @param integer $offset
     * @return \MagicPill\IO\File\FileReader
     * @throws FileNotOpenException
     */
    public function seek($offset)
    {
        if ($this->isOpened()) {
            fseek($this->getHandle(), $offset);
        } else {
            ExceptionFactory::FileNotOpenException('File not opened');
        }
        return $this;
    }
}
