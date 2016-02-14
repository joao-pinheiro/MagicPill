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
 * @package    Application
 * @copyright  Copyright (c) 2014 Joao Pinheiro
 * @version    0.9
 */

namespace MagicPill;

use MagicPill\Application\Resource\ResourceManager;
use MagicPill\Exception\ExceptionFactory;

class Application extends \MagicPill\Core\Object
{
    /**
     * @var \MagicPill\Application
     */
    protected static $instance = null;

    /**
     * @var \MagicPill\Application\Resource\Manager
     */
    protected static $resourceManager = null;

    /**
     * Options to be passed to the resource mananager upon creation
     * @var array
     */
    protected $resourceManagerOptions = array(
        'baseClass' => '\MagicPill\Application\Resource\ResourceInterface',
        'baseClassCheck' => false,
        'namespaces' => array(
            '\MagicPill\Application\Resource',
        ),
        'parentProxy' => true
    );

    /**
     * @var \MagicPill\Dictionary
     */
    protected $applicationOptions = null;

    /**
     * @var string
     */
    protected $defaultEnv = 'live';

    /**
     * Constructor
     * @param array $options
     * @param string $resourceToExecute
     */
    public function __construct(array $options = array(), $resourceToExecute = null)
    {
        static::$instance = $this;
        $this->initResourceManager();
        $this->getApplicationOptions()->fromArray($options);
        $this->applyApplicationOptions();
    }

    /**
     * Magic resource discovery
     * @param string $name
     * @param mixed $arguments (ignored)
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return static::getResourceByMethod($name);
    }

    /**
     * Magic resource discovery
     * @param string $name
     * @param mixed $arguments (ignored)
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::getResourceByMethod($name);
    }

    /**
     * Retrieve the application options passed onto the constructor
     * @return \MagicPill\Collection\Dictionary
     */
    public function getApplicationOptions()
    {
        if (null == $this->applicationOptions) {
            $this->applicationOptions = new Collection\Dictionary();
        }
        return $this->applicationOptions;
    }

    /**
     * Applies misc application options
     * May be overriden in child classes
     * @return \MagicPill\Application
     */
    public function applyApplicationOptions()
    {
        $namespaces = $this->getApplicationOption('resourceNamespace');
        if (!empty($namespaces)) {
            $this->addResourceNamespace($namespaces);
        }
        return $this;
    }

    /**
     * Retrieves a single application option entry
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getApplicationOption($name, $defaultValue = null)
    {
        if ($this->getApplicationOptions()->containsKey($name)) {
            return $this->getApplicationOptions()->get($name);
        }
        return $defaultValue;
    }

    /**
     * Retrieves the unique application id
     * @return string
     */
    public function getObjectIdentifier()
    {
        $appId = $this->getApplicationOption('applicationId');
        if (null == $appId) {
            $appId = md5(get_class($this));
        }
        return $appId;
    }

    /**
     * Adds resource namepsaces
     * @param string|array $namespace
     * @return \MagicPill\Application
     * @throws ApplicationResourceException
     */
    public function addResourceNamespace($namespace)
    {
        if (is_array($namespace)) {
            foreach($namespace as $item) {
                static::$resourceManager->addNamespace($item);
            }
        } elseif (is_string($namespace)) {
            static::$resourceManager->addNamespace($namespace);
        } else {
            ExceptionFactory::ApplicationResourceException('Invalid resourceNamespace format');
        }

        return $this;
    }

    /**
     * Returns true if application is executing in a development environment
     * @return boolean
     */
    public function isDevelopmentEnvironment()
    {
        return (bool) $this->getApplicationOption('developmentEnvironment', false);
    }

    /**
     * Retrieve application environment
     * @return string
     */
    public function getEnvironment()
    {
        return defined('APPLICATION_ENV') ? APPLICATION_ENV : $this->defaultEnv;
    }

    /**
     * Initializes the Resource Manager
     */
    protected function initResourceManager()
    {
        static::$resourceManager = new ResourceManager($this->resourceManagerOptions);
        static::$resourceManager->setParent($this);
    }

    /**
     * Retrieves a resource using get<Name>/run<Name> syntax
     * If get<Name> syntax is used, the resource result is stored in the resource registry
     * If run<Name> syntax is used, the resource executed without being stored
     * @param string $name
     * @return mixed
     * @throws ApplicationInvalidMethodException
     */
    protected static function getResourceByMethod($name)
    {
        if (substr($name, 0, 3) == 'get') {
            $name = substr($name, 3);
            return static::$resourceManager->getResource($name);
        } elseif (substr($name, 0, 3) == 'run') {
            $name = substr($name, 3);
            return static::$resourceManager->loadResource($name);
        }
        ExceptionFactory::ApplicationInvalidMethodException($name);
    }
}
