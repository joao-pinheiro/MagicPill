<?php

namespace MagicPill\Application\Handler;

use MagicPill\Application\Resources;
use MagicPill\Collection\Collection;
use MagicPill\Mixin\DI;
use MagicPill\Util\Log\LogLevel;

class ErrorHandler implements ErrorInterface
{
    use DI;

    /**
     * @var \MagicPill\Collection\Collection
     */
    protected $errorMessages = null;

    /**
     * Configures the error handler
     * @param \Traversable $config
     * @return void
     */
    public function config(\Traversable $config)
    {
    }

    /**
     * Error Handler
     * @param int $number
     * @param string $message
     * @param string $file
     * @param int $line
     */
    public function errorHandler($number, $message, $file, $line)
    {
        $message = implode(' ', [$message, 'in', $file, 'Line', $line]);

        $this->getMessages()->add($message);
        if ($this->getDi()->resourceExists(Resources::LOG)) {
            $this->getDi(Resources::LOG)->log(LogLevel::ERROR, $message);
        }
    }

    /**
     * Retrieve error messages
     * @return \MagicPill\Collection\Collection
     */
    public function getMessages()
    {
        if (null === $this->errorMessages) {
            $this->errorMessages = new Collection();
        }
        return $this->errorMessages;
    }
}