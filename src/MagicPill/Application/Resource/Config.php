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
 * @copyright  Copyright (c) 2014-2016 Joao Pinheiro
 * @version    1.0
 */

namespace MagicPill\Application\Resource;

use MagicPill\Application\ApplicationAbstract;
use MagicPill\Application\Resources;
use MagicPill\Config\Ini;
use MagicPill\Config\Inline;
use MagicPill\Config\Json;
use MagicPill\Config\Php;
use MagicPill\Exception\ExceptionFactory;
use MagicPill\Core\Container\ResourceInterface;

class Config implements ResourceInterface
{
    const PARAM_CONFIG_FILE = 'configFile';

    /**
     * Retrieve configuration
     * @param \MagicPill\Core\Container $di
     * @return \MagicPill\Collection\Container
     */
    public function init(\MagicPill\Core\Container $di)
    {
        /** @var \MagicPill\Application\ApplicationAbstract $app */
        $app = $di->get(Resources::APPLICATION);
        $configFile = $app->getOption(self::PARAM_CONFIG_FILE);

        if (is_array($configFile)) {
            return new Inline($configFile);
        }

        if (empty($configFile) || !file_exists($configFile)) {
            ExceptionFactory::ResourceConfigFileNotFoundException(sprintf('configuration file %s empty or not found', $configFile));
        }
        $tokens = explode('.', $configFile);
        $extension = strtolower(array_pop($tokens));
        switch($extension) {
            case 'ini':
                return new Ini($configFile);

            case 'php':
                return new Php($configFile);

            case 'json':
                $content = file_get_contents($configFile);
                return new Json($content);
        }

        ExceptionFactory::ResourceConfigUnknownTypeException(sprintf('Unknown type %s for config file %s', $extension, $configFile));
    }
}
