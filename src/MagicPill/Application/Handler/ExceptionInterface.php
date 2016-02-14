<?php

namespace MagicPill\Application\Handler;

interface ExceptionInterface
{
    /**
     * Configures the exception handler
     * @param \Traversable $config
     * @return void
     */
    public function config(\Traversable $config);

    /**
     * Exception Handler
     * @param \Exception | \Throwable $ex
     */
    public function exceptionHandler($ex);

    /**
     * Retrieve exception messages
     * @return \MagicPill\Collection\Collection
     */
    public function getMessages();
}