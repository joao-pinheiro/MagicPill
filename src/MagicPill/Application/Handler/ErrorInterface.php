<?php

namespace MagicPill\Application\Handler;

interface ErrorInterface
{
    /**
     * Configures the error handler
     * @param \Traversable $config
     * @return void
     */
    public function config(\Traversable $config);

    /**
     * Error Handler
     * @param int $number
     * @param string $message
     * @param string $file
     * @param int $line
     */
    public function errorHandler($number, $message, $file, $line);

    /**
     * Retrieve error messages
     * @return \MagicPill\Collection\Collection
     */
    public function getMessages();
}
