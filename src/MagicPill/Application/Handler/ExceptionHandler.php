<?php

namespace MagicPill\Application\Handler;

use MagicPill\Application\Resources;
use MagicPill\Collection\Collection;
use MagicPill\Mixin\DI;
use MagicPill\Util\Log\LogLevel;

class ExceptionHandler implements ExceptionInterface
{
    use DI;

    /**
     * @var \MagicPill\Collection\Collection
     */
    protected $exceptionMessages = null;

    /**
     * @var bool
     */
    protected $throwExceptions = true;

    /**
     * Configures the error handler
     * @param \Traversable $config
     * @return void
     */
    public function config(\Traversable $config)
    {
        $val = $config->throwExceptions;
        if (is_bool($val)) {
            $this->throwExceptions = $val;
        }
    }

    /**
     * Exception Handler
     * @param \Exception | \Throwable $ex
     */
    public function exceptionHandler($ex)
    {
        $message = implode(
            '',
            ['Uncaught', get_class($ex), 'Exception:', $ex->getMessage(), PHP_EOL, $ex->getTraceAsString()]
        );
        $this->getMessages()->add($message);
        if ($this->getDi()->resourceExists(Resources::LOG)) {
            $this->getDi(Resources::LOG)->log(LogLevel::EMERGENCY, $message);
        }

        if ($this->throwExceptions) {
            throw $ex;
        }
    }

    /**
     * Retrieve error messages
     * @return \MagicPill\Collection\Collection
     */
    public function getMessages()
    {
        if (null === $this->exceptionMessages) {
            $this->exceptionMessages = new Collection();
        }
        return $this->exceptionMessages;
    }
}