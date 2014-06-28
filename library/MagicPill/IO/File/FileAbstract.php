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

use MagicPill\Core\Object;
abstract class FileAbstract extends Object
{
    /**
     * @var resource
     */
    protected $fileHandle = null;

    /**
     * @var string
     */
    protected $fileName = '';

    /**
     * @var array 
     */
    protected $validModes = array();
    
    /**
     * Defines file name
     * @param string $fileName
     * @return \MagicPill\Io\File\FileAbstract
     */
    public function setName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Retrieve file name
     * @return string
     */
    public function getName()
    {
        return $this->fileName;
    }

    /**
     * Defines file handle
     * @param mixed $handle
     * @return \MagicPill\Io\File\FileAbstract
     */
    public function setHandle($handle = null)
    {
        $this->fileHandle = $handle;
        return $this;
    }

    /**
     * Retrieve file handle
     * @return resource
     */
    public function getHandle()
    {
        return $this->fileHandle;
    }

    /**
     * Closes a file, if opened
     * @return \MagicPill\Io\File\FileAbstract
     */
    public function close()
    {
        if (null !== $this->getHandle()) {
            fclose($this->getHandle());
        }
        $this->setHandle(null);
        return $this;
    }

    /**
     * Resets the internal class state
     * @return \MagicPill\Io\File\FileAbstract
     */
    public function reset()
    {
        if ($this->isOpened()) {
            $this->close();
        }
        $this->setName()
             ->setHandle();
        return $this;

    }

    /**
     * Check if file is opened
     * @return boolean
     */
    public function isOpened()
    {
        return $this->getHandle() !== null;
    }

    /**
     * Deletes a file
     * @param string $fileName
     * @return \MagicPill\Io\File\FileAbstract
     */
    public function delete($fileName = '')
    {
        if (empty($fileName)) {
            unlink($this->getName());
        } else {
            unlink($fileName);
        }
        return $this;
    }

    /**
     * Checks if a file exists
     * @param string $fileName
     * @return boolean
     */
    public function exists($fileName = '')
    {
        if (empty($fileName)) {
            return file_exists($this->getName());
        }
        return file_exists($fileName);
    }

    /**
     * Retrieve file size
     * @param string $fileName
     * @return integer
     */
    public function getSize($fileName = '')
    {
        if (empty($fileName)) {
            return filesize($this->getName());
        }
        return filesize($fileName);
    }
    
    /**
     * Checks if given mode is valid
     * @param string $mode
     * @return boolean
     */
    protected function isValidMode($mode)
    {
        return in_array($mode, $this->validModes);
    }    
}
