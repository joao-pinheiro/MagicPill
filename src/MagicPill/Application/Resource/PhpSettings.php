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

class PhpSettings implements ResourceInterface
{
    /**
     * Retrieve configuration
     * @param \MagicPill\Core\Registry $di
     * @return null
     */
    public function init(\MagicPill\Core\Registry $di)
    {
        /** @var \MagicPill\Application\ApplicationAbstract $app */
        $config = $di->get(Resources::CONFIG)->php;
        if (!empty($config)) {
            $this->applyPHPSettings($config);
        }
        return null;
    }

    /**
     * Recusively applies php options
     * @param \Traversable $settings
     * @param string $prefix
     */
    protected function applyPHPSettings(\Traversable $settings, $prefix = '')
    {
        foreach ($settings as $key => $value) {
            $key = empty($prefix) ? $key : $prefix . $key;
            if (is_scalar($value)) {
                ini_set($key, $value);
            } elseif (is_array($value)) {
                $this->applyPHPSettings($value, $key . '.');
            }
        }
    }
}
