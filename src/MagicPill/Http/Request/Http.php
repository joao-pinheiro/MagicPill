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
 * @package    MVC
 * @copyright  Copyright (c) 2014 Joao Pinheiro
 * @version    0.9
 */

namespace MagicPill\MVC\Request;

use MagicPill\Core\Object;
use MagicPill\Collection\Dictionary;
use MagicPill\Exception\ExceptionFactory;

class Http extends RequestAbstract
{
    /**
     * RFC 2616 Methods
     */
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_TRACE = 'TRACE';
    const METHOD_CONNECT = 'CONNECT';

    /**
     * RFC 2518 Methods
     */    
    const METHOD_PROPFIND = 'PROPFIND';
    const METHOD_PROPPATCH = 'PROPPATCH';
    const METHOD_MKCOL = 'MKCOL';
    const METHOD_COPY = 'COPY';
    const METHOD_MOVE = 'MOVE';
    const METHOD_LOCK = 'LOCK';
    const METHOD_UNLOCK = 'UNLOCK';

    const HTTP = 'http';
    const HTTPS = 'https';
    
    /**
     * @var string 
     */
    protected $method = '';
    
    protected $uri = null;
    
    /**
     * @var MagicPill\Collection\Dictionary 
     */    
    protected $properties = null;

    /**
     * Constructor
     */
    public function __construct()
    {
    }
    
    /**
     * Retrieve parameter list
     * @return MagicPill\Collection\Dictionary
     */
    public function getParameters()
    {
        if (null === $this->parameters) {
            $this->parameters = new Dictionary($_REQUEST);
        }
        return $this->parameters;
    }    
    
    /**
     * Retrieve HTTP method
     * @return string
     */
    public function getRequestMethod()
    {
        $prop = $this->getProperties();
        if (!$prop->offsetExists('requestMethod')) {
            $prop->add('requestMethod', $this->detectRequestMethod());
        }
        return $prop->get('requestMethod');
    }
    
    /**
     * Defines HTTP method
     * @param string $method
     * @return \MagicPill\MVC\Request\Http
     */
    public function setRequestMethod($method)
    {
        $this->getProperties()->add('requestMethod', $method);
        return $this;
    }
    
    /**
     * Check request method
     * @return bool
     */
    public function isOptions()
    {
        return $this->getRequestMethod() === self::METHOD_OPTIONS;
    }
    
    /**
     * Check request method
     * @return bool
     */
    public function isGet()
    {
        return $this->getRequestMethod() === self::METHOD_GET;
    }
    
    /**
     * Check request method
     * @return bool
     */
    public function isHead()
    {
        return $this->getRequestMethod() === self::METHOD_HEAD;        
    }
    
    /**
     * Check request method
     * @return bool
     */
    public function isPost()
    {
        return $this->getRequestMethod() === self::METHOD_POST;        
    }
    
    /**
     * Check request method
     * @return bool
     */
    public function isPut()
    {
        return $this->getRequestMethod() === self::METHOD_PUT;        
    }
    
    /**
     * Check request method
     * @return bool
     */
    public function isTrace()
    {
        return $this->getRequestMethod() === self::METHOD_TRACE;        
    }
    
    /**
     * Check request method
     * @return bool
     */
    public function isConnect()
    {
        return $this->getRequestMethod() === self::METHOD_CONNECT;        
    } 
    
    /**
     * Checks if request is an ajax request
     * @return bool
     */
    public function isAjaxRequest()
    {
        return ('XMLHttpRequest' === $this->getHeader('X_REQUESTED_WITH'));
    }
    
    /**
     * Retrieve header collection
     * @return MagicPill\Collection\Dictionary 
     */
    protected function getHeaders()
    {
        $prop = $this->getProperties();
        if (!$prop->offsetExists('headers')) {
            $prop->add(
                    'headers',
                    new Dictionary($this->detectHeaders())
                    );
        }
        return $prop->get('headers');
    }
    
    /**
     * Retrieve a header value
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getHeader($name, $defaultValue = null)
    {
        return $this->getHeaders()->offsetExists($name)
                ? $this->getHeaders()->get($name)
                : $defaultValue;
    }
    
    /**
     * Defines a header value
     * @param string $name
     * @param mixed $value
     * @return \MagicPill\MVC\Request\Http
     */
    public function setHeader($name, $value)
    {
        $this->getHeaders()->add($name, $value);
        return $this;
    }
    
    /**
     * Retrieve Client IP
     * @return string
     */
    public function getClientIp()
    {
        $prop = $this->getProperties();
        if (!$prop->offsetExists('clientIp')) {
            $prop->add('clientIp', $this->detectClientIp());
        }
        return $prop->get('clientIp');
    }
    
    /**
     * Define client ip value
     * @param string $value
     * @return \MagicPill\MVC\Request\Http
     */
    public function setClientIp($value)
    {
        $this->getProperties()->add('clientIp', $value);
        return $this;
    }
    
    /**
     * Retrieve a POST parameter or the whole array
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getPost($key = null, $defaultValue = null)
    {
        return (null === $key) 
            ? $_POST
            : isset($_POST[$key]) ? $_POST[$key] : $defaultValue;
    }
    
    /**
     * Retrieve a cookie value or the whole array
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getCookie($key = null, $defaultValue = null)
    {
        return (null === $key) 
            ? $_COOKIE
            : isset($_COOKIE[$key]) ? $_COOKIE[$key] : $defaultValue;
    }  
    
    /**
     * Retrieve a env value or the whole array
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getEnv($key = null, $defaultValue = null)
    {
        return (null === $key) 
            ? $_ENV
            : isset($_ENV[$key]) ? $_ENV[$key] : $defaultValue;
    }
    
    /**
     * Returns true if it is an SSL connection
     * @return bool
     */
    public function isHttps()
    {
        return ('on' === $this->getServer('HTTPS'));
    }
    
    /**
     * Retrieve HTTP host
     * @return string
     */
    public function getHttpHost()
    {
        $prod = $this->getProperties();
        if (!$prod->offsetExists('httpHost')) {
            $prod->add('httpHost', $this->detectHost());
        }
        return $prod->get('httpHost');
    }
    
    /**
     * Performs automatic detection of request method
     * @return string
     */
    protected function detectRequestMethod()
    {
        $method = static::METHOD_GET;
        foreach(array('REQUEST_METHOD', 'HTTP_X_HTTP_METHOD_OVERRIDE') as $key) {
            if (isset($this->getServer($key))) {
                $method = $this->getServer($key);
            }
        }
        return $method;
    }
    
    /**
     * Detects existing http headers
     * @return array
     */
    protected function detectHeaders()
    {
        $result = array();
        foreach($this->getServer() as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $result[substr($key, 5)] = $value;
            }
        }        
        return $result;
    }
    
    /**
     * Detects client ip
     * @param bool $detectProxy
     * @return string
     */
    protected function detectClientIp($detectProxy = true)
    {
        $result = $this->getServer('REMOTE_ADDR');
        if ($detectProxy) {
            if (null !== $this->getServer('HTTP_CLIENT_IP')) {
                $result = $this->getServer('HTTP_CLIENT_IP');
            } elseif (null !== $this->getServer('HTTP_X_FORWARDED_FOR')) {
                $result = $this->getServer('HTTP_X_FORWARDED_FOR');
            }
        }
        return $result;
    }
    
    /**
     * Detect the hostname
     * @return string
     */
    protected function detectHost()
    {
        if (!$host = $this->getServer('HTTP_HOST')) {
            $port = $this->getServer('SERVER_PORT');
            $host = $this->getServer('SERVER_NAME');
            if ($this->isHttps()) {
                if (443 != $port) {
                    $host .= ':' . $port;
                }
            } else {
                if (80 != $port) {
                    $host .= ':' . $port;
                }
            }
        }
        return $host;
    }
    
    /**
     * Retrieve the request property list
     * @return \MagicPill\Collection\Dictionary
     */
    protected function getProperties()
    {
        if (null == $this->properties) {
            $this->properties = new Dictionary();
        }
        return $this->properties;
    }
    
    public function getRequestUri()
    {
        
    }
    
    /**
     * Retrieve raw body
     * @return string
     */
    public function getBody()
    {
        $body = $this->getProperties()->get('requestBody');
        if (null === $body) {
            $body = file_get_contents('php://input');
            if (empty($body)) {
                $body = false;
            }
            $this->getProperties()->add('requestBody', $body);
        }
        return $body;
    }
    
    /**
     * @return \MagicPill\MVC\Request\Http
     */
    protected function parseRequestUri()
    {
        if ($requestUri === null) {
            if (isset($_SERVER['HTTP_X_ORIGINAL_URL'])) { 
                // IIS with Microsoft Rewrite Module
                $requestUri = $_SERVER['HTTP_X_ORIGINAL_URL'];
            } elseif (isset($_SERVER['HTTP_X_REWRITE_URL'])) { 
                // IIS with ISAPI_Rewrite
                $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            } elseif (
                // IIS7 with URL Rewrite: make sure we get the unencoded url (double slash problem)
                isset($_SERVER['IIS_WasUrlRewritten'])
                && $_SERVER['IIS_WasUrlRewritten'] == '1'
                && isset($_SERVER['UNENCODED_URL'])
                && $_SERVER['UNENCODED_URL'] != ''
                ) {
                $requestUri = $_SERVER['UNENCODED_URL'];
            } elseif (isset($_SERVER['REQUEST_URI'])) {
                $requestUri = $_SERVER['REQUEST_URI'];
                // Http proxy reqs setup request uri with scheme and host [and port] + the url path, only use url path
                $schemeAndHttpHost = $this->getScheme() . '://' . $this->getHttpHost();
                if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                    $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
                }
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
                $requestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $requestUri .= '?' . $_SERVER['QUERY_STRING'];
                }
            } else {
                return $this;
            }
        } elseif (!is_string($requestUri)) {
            return $this;
        } else {
            // Set GET items, if available
            if (false !== ($pos = strpos($requestUri, '?'))) {
                // Get key => value pairs and set $_GET
                $query = substr($requestUri, $pos + 1);
                parse_str($query, $vars);
                $this->setQuery($vars);
            }
        }

        $this->_requestUri = $requestUri;
        return $this;
    }
}