<?php

namespace MagicPill\Mixin;

use MagicPill\Core\Registry as CoreRegistry;

trait Registry
{
    /**
     * @var \MagicPill\Core\Registry
     */
    protected $registry = null;

    /**
     * Retrieve Registry component
     * @param string|null $key
     * @return Object|\MagicPill\Core\Registry
     */
    public function getRegistry($key = null)
    {
        if (is_null($this->registry)) {
            $this->setRegistry(CoreRegistry::getInstance());
        }

        return is_null($key)
            ? $this->registry
            : $this->registry->get($key);
    }

    /**
     * Set internal registry
     * @param \MagicPill\Core\Registry $registry
     * @return $this
     */
    protected function setRegistry(\MagicPill\Core\Registry $registry)
    {
        $this->registry = $registry;
        return $this;
    }
}