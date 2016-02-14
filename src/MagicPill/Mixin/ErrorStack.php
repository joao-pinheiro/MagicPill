<?php

namespace MagicPill\Mixin;

use MagicPill\Collection\Collection;

Trait ErrorStack
{
    /**
     * @var \MagicPill\Collection\Collection
     */
    protected $errorStack = null;

    /**
     * Adds an error message to the error stack
     * @param $errorMessage
     * @return $this
     */
    public function addError($errorMessage)
    {
        $this->getErrorCollection()->add($errorMessage);
        return $this;
    }

    /**
     * Returns true if stack is not empty
     * @return bool
     */
    public function hasErrors()
    {
        return !$this->getErrorCollection()->isEmpty();
    }

    /**
     * Clear the error stack
     * @return $this
     */
    public function clearErrors()
    {
        $this->getErrorCollection()->clear();
        return $this;
    }

    /**
     * Retrieve all errors as an array
     * @return array
     */
    public function getErrors()
    {
        return $this->getErrorCollection()->toArray();
    }

    /**
     * Retrieve the error stack collection
     * @return Collection
     */
    public function getErrorCollection()
    {
        if (null == $this->errorStack) {
            $this->errorStack = new Collection();
        }
        return $this->errorStack;
    }
}