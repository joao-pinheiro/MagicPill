<?php

namespace MagicPill\Mixin;

trait Singleton
{
    /**
     * @var Object
     */
    protected static $instance = null;

    /**
     * Retrieve instance
     * @return Object
     */
    final public static function getInstance()
    {
        return isset(static::$instance)
            ? static::$instance
            : static::$instance = new static;
    }

    /**
     *
     */
    final private function __clone()
    {
    }
}
