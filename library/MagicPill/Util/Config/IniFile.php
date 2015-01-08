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
 * @package    Resource
 * @copyright  Copyright (c) 2014 Joao Pinheiro
 * @version    0.9
 */

namespace MagicPill\Util\Config;

use MagicPill\Exception\ExceptionFactory;

/**
 * This class is inspired in Zend Framework's Zend_Config_Ini
 */
class IniFile extends ConfigFileAbstract
{
    /**
     * @var string
     */
    protected $errorMessage = '';

    /**
     * Retrieve error message, if exists
     * @return string
     */
    public function getError()
    {
        return $this->errorMessage;
    }

    /**
     * Reads the ini file and returns an array
     * @param string $filename
     * @return array
     * @throws ConfigIniException
     */
    protected function parseFile($filename)
    {
        $data = array();
        set_error_handler(array($this, 'fileErrorHandler'));
        $data = parse_ini_file($filename, true);
        restore_error_handler();

        if ('' !== $this->getError()) {
            ExceptionFactory::ConfigIniFileException($this->getError());
        }
        
        return $data;
    }

    /**
     * Overrides system error handler
     * @param string $code
     * @param string $message
     * @param string $file
     * @param string $line
     */
    protected function fileErrorHandler($code, $message, $file, $line)
    {
        if (empty($this->errorMessage)) {
            $this->errorMessage = $message;
        } else {
            $this->errorMessage .= PHP_EOL . $message;
        }
    }
}
