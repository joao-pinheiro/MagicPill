<?php

namespace MagicPill\Core\Registry;

interface ResourceInterface
{
    /**
     * Performs initialization of a resource
     * @param \MagicPill\Core\Registry $di
     * @return Object|mixed
     */
    public function init(\MagicPill\Core\Registry $di);
}