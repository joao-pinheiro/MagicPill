<?php

namespace MagicPill\Application\Handler;

interface ShutdownInterface
{
    /**
     * Configures the shutdown handler
     * @param \Traversable $config
     * @return void
     */
    public function config(\Traversable $config);

    /**
     * Shutdown Handler
     */
    public function shutdownHandler();
}