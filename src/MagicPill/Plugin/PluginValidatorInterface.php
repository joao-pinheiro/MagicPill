<?php

namespace MagicPill\Plugin;

interface PluginValidatorInterface
{
    /**
     * Checks if a given className is valid as a plugin
     * @param string $className
     * @return bool
     */
    public function isValid($className);
}