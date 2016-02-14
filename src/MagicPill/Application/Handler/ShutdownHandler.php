<?php

namespace MagicPill\Application\Handler;

use MagicPill\Application\Resources;
use MagicPill\Mixin\Registry;

class ShutdownHandler implements ShutdownInterface
{
    use Registry;

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
        $registry = $this->getRegistry();
        if ($this->getRegistry()->resourceExists(Resources::LOG)) {
            $error = error_get_last();
            if ($error) {
                $message = implode(' ', [$error['message'], 'in', $error['file'], 'Line', $error['line']]);
                $registry->get(Resources::LOG)->error($message);
            }
        }
    }
}