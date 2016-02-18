<?php

namespace MagicPill\Application\Handler;

use MagicPill\Application\Resources;
use MagicPill\Mixin\DI;

class ShutdownHandler implements ShutdownInterface
{
    use DI;

    /**
     * Configures the shutdown handler
     * @param \Traversable $config
     * @return void
     */
    public function config(\Traversable $config)
    {
    }

    /**
     * Shutdown Handler
     */
    public function shutdownHandler()
    {
        $registry = $this->getDi();
        if ($this->getDi()->resourceExists(Resources::LOG)) {
            $error = error_get_last();
            if ($error) {
                $message = implode(' ', [$error['message'], 'in', $error['file'], 'Line', $error['line']]);
                $registry->get(Resources::LOG)->error($message);
            }
        }
    }
}