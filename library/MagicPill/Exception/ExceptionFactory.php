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
 * @package    Core
 * @copyright  Copyright (c) 2014 Joao Pinheiro
 * @version    0.9
 */

namespace MagicPill\Exception;

class ExceptionFactory extends \MagicPill\Core\Object
{
    /**
     * @var string
     */
    protected static $token = '{CLASSNAME}';

    /**
     * @var string
     */
    protected static $evalPayload = 'class {CLASSNAME} extends \MagicPill\Exception\CoreException {}';

    /**
     * Builds a new Exception
     * @param string $className
     * @param string $message
     * @param integer $code
     * @param \Exception $previous
     * @param mixed $parameters
     * @return \MagicPill\Exception\CoreException
     */
    public static function build($className, $message, $code = null, $previous = null, $parameters = null)
    {
        eval(str_replace(self::$token, $className, self::$evalPayload));
        return new $className($message, $code, $previous);
    }

    /**
     * Allows invoking syntax as ExceptionFactory::ExceptionName('Message')
     * @param string $name
     * @param string $arguments
     * @return \MagicPill\Exception\CoreException
     */
    public static function __callStatic($name, $arguments)
    {
        return self::build($name, array_shift($arguments), array_shift($arguments), array_shift($arguments));
    }
}