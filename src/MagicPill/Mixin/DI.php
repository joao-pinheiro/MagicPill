<?php

namespace MagicPill\Mixin;

use MagicPill\Core\Container as CoreRegistry;

trait DI
{
    /**
     * @var \MagicPill\Core\Container
     */
    protected $registry = null;

    /**
     * Retrieve DI component
     * @param string|null $key
     * @return Object|\MagicPill\Core\Container
     */
    public function getDi($key = null)
    {
        if (is_null($this->registry)) {
            $this->setDi(CoreRegistry::getInstance());
        }

        return is_null($key)
            ? $this->registry
            : $this->registry->get($key);
    }

    /**
     * Set DI component
     * @param \MagicPill\Core\Container $container
     * @return $this
     */
    protected function setDi(\MagicPill\Core\Container $container)
    {
        $this->registry = $container;
        return $this;
    }
}