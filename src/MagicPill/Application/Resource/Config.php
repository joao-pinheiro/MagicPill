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

namespace MagicPill\Application\Resource;

use MagicPill\Exception\ExceptionFactory;
use MagicPill\Mixin\Inherit;

class Config extends ResourceAbstract
{
    use Inherit;

    /**
     * @var string
     */
    protected $cacheKey = null;

    /**
     * Config file initialization
     * @param \MagicPill\Core\Object $application
     * @return \MagicPill\Collection\Dictionary
     * @throws ResourceConfigException
     */
    public function init(\MagicPill\Core\Object $application)
    {
        $this->setParent($application);
        $options = $application->getApplicationOptions();
        if (!isset($options['configFile'])) {
            ExceptionFactory::ResourceConfigException('Config file path is not set');
        }

        $developmentEnvironment = $application->isDevelopmentEnvironment();
        $env = $application->getEnvironment();

        if ($developmentEnvironment) {
            return $this->readConfigFile($options['configFile'], $env);
        }

        $cachedObject = $this->readFromCache();
        if (empty($cachedObject)) {
            $cachedObject = $this->readConfigFile($options['configFile'], $env);
            $this->saveToCache($cachedObject);
        }

        return $cachedObject;
    }

    /**
     * Retrieves configuration from cache
     * @return \MagicPill\Util\Config\Container
     */
    protected function readFromCache()
    {
        $result = null;
        if(extension_loaded('apc') && ini_get('apc.enabled')) {
            $result = apc_fetch($this->getCacheKey());
        }
        return $result;
    }

    /**
     * Returns the current vhost or 'cli' for commandline applications
     * @return string
     */
    protected function getHttpHost()
    {
        return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'cli';
    }

    /**
     * Saves a Zend_Config object to cache
     * @param Zend_Config $data
     */
    protected function saveToCache($data)
    {
        if(extension_loaded('apc') && ini_get('apc.enabled')) {
            apc_store($this->getCacheKey(), $data, 0);
        }
    }

    /**
     * Builds the cache key identifier
     * @return string
     */
    protected function getCacheKey()
    {
        if (null == $this->cacheKey) {
            $this->cacheKey = get_class($this)
                    . $this->getParent()->getEnvironment()
                    . $this->getHttpHost();
        }
        return $this->cacheKey;
    }

    /**
     * Reads a config file into a \MagicPill\Util\Config\Container Object
     * @param string $filename
     * @param string $environment
     * @return \MagicPill\Util\Config\Container
     */
    protected function readConfigFile($filename, $environment)
    {
        $filename = realpath($filename);
        if (!file_exists($filename)) {
            ExceptionFactory::ResourceConfigException('Config file ' . $filename . ' not found');
        }
        $tmp = explode('.', $filename);
        $extension = array_pop($tmp);
        switch($extension) {
            case 'ini':
                return new IniFile($filename, $environment);
            
            case 'php':
                return new PhpFile($filename);
            
            default:
               ExceptionFactory::ResourceConfigException('Unsupported config file format'); 
        }
    }
}
