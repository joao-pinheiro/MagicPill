<?php

namespace MagicPill\Core\Container;

interface ResourceInterface
{
    /**
     * Performs initialization of a resource
     * @param \MagicPill\Core\Container $di
     * @return Object|mixed
     */
    public function init(\MagicPill\Core\Container $di);
}