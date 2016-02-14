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
 * @package    Application
 * @copyright  Copyright (c) 2016 Joao Pinheiro
 * @version    1.0
 */

namespace MagicPill\Application\Resource;

use MagicPill\Application\Resources;
use MagicPill\Core\Registry\ResourceInterface;
use MagicPill\Exception\ExceptionFactory;
use MagicPill\Resource\Loader;

class Handlers implements ResourceInterface
{
    const ERROR_HANDLER = 'errorHandler';
    const EXCEPTION_HANDLER = 'exceptionHandler';
    const SHUTDOWN_HANDLER = 'shutdownHandler';

    /**
     * @var \MagicPill\Resource\Loader
     */
    protected $handlerLoader = null;

    /**
     * @var array
     */
    protected $parameters = [
        'errorHandler' => 'initErrorHandler',
        'exceptionHandler' => 'initExceptionHandler',
        'shutdownHandler' => 'initShutdownHandler'
    ];

    /**
     * @var \MagicPill\Application\Handler\ErrorInterface
     */
    protected $errorHandler = null;

    /**
     * @var \MagicPill\Application\Handler\ExceptionInterface
     */
    protected $exceptionHandler = null;

    /**
     * @var \MagicPill\Application\Handler\ShutdownInterface
     */
    protected $shutdownHandler = null;

    /**
     * Initialize application handlers
     * @param \MagicPill\Core\Registry $di
     * @return $this
     */
    public function init(\MagicPill\Core\Registry $di)
    {
        $config = $di->getConfig()->handlers;
        if (!empty($config)) {
            $ns = isset($config->namespaces) ? $config->namespaces : [];
            $this->handlerLoader = new Loader($ns);

            foreach($this->parameters as $param => $method) {
                $val = $config->__get($param);
                if ($val instanceof \Traversable) {
                    if ($val->enabled) {
                        $this->$method($di, $val);
                    }
                } else {
                    ExceptionFactory::ResourceHandlersException(sprintf('Invalid handler configuration for handler %s', $param));
                }
            }
        }
        return $this;
    }

    /**
     * Retrieve error handler
     * @return \MagicPill\Application\Handler\ErrorInterface
     */
    public function getErrorHandler()
    {
        return $this->errorHandler;
    }

    /**
     * Retrieve exception handler
     * @return \MagicPill\Application\Handler\ExceptionInterface
     */
    public function getExceptionHandler()
    {
        return $this->exceptionHandler;
    }

    /**
     * Retrieve shutdown handler
     * @return \MagicPill\Application\Handler\ShutdownInterface
     */
    public function getShutdownHandler()
    {
        return $this->shutdownHandler;
    }

    /**
     * Registers error handler
     * @param \MagicPill\Core\Registry $di
     * @param \Traversable $config
     */
    protected function initErrorHandler(\MagicPill\Core\Registry $di, \Traversable $config)
    {
        $this->errorHandler = $this->handlerLoader->loadResource(ucfirst(self::ERROR_HANDLER));
        $this->errorHandler->config($config);
        set_error_handler([$this->errorHandler, self::ERROR_HANDLER]);
        $di->set(Resources::ERROR_HANDLER, $this->errorHandler);
    }

    /**
     * Registers exception handler
     * @param \MagicPill\Core\Registry $di
     * @param \Traversable $config
     */
    protected function initExceptionHandler(\MagicPill\Core\Registry $di, \Traversable $config)
    {
        $this->exceptionHandler = $this->handlerLoader->loadResource(ucfirst(self::EXCEPTION_HANDLER));
        $this->exceptionHandler->config($config);
        set_exception_handler([$this->exceptionHandler, self::EXCEPTION_HANDLER]);
        $di->set(Resources::EXCEPTION_HANDLER, $this->exceptionHandler);
    }

    /**
     * Registers shutdown handler
     * @param \MagicPill\Core\Registry $di
     * @param \Traversable $config
     */
    protected function initShutdownHandler(\MagicPill\Core\Registry $di, \Traversable $config)
    {
        $this->shutdownHandler = $this->handlerLoader->loadResource(ucfirst(self::SHUTDOWN_HANDLER));
        $this->shutdownHandler->config($config);
        register_shutdown_function([$this->shutdownHandler, self::SHUTDOWN_HANDLER]);
        $di->set(Resources::SHUTDOWN_HANDLER, $this->shutdownHandler);
    }

}
