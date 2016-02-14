<?php

namespace MagicPill\Mixin;

use MagicPill\Collection\Dictionary;
use MagicPill\Exception\ExceptionFactory;

Trait Options
{

    /**
     * @var \MagicPill\Collection\Dictionary
     */
    protected $optionsDictionary = null;

    /**
     * Adds or replaces an option
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function setOption($key, $value)
    {
        $this->getOptions()->add($key, $value);
        return $this;
    }

    /**
     * Retrieve an option by key
     * @param string $key
     * @return mixed
     */
    public function getOption($key)
    {
        return $this->getOptions()->get($key);
    }

    /**
     * Remove an option by key
     * @param string $key
     * @return $this
     */
    public function removeOption($key)
    {
        $this->getOptions()->remove($key);
        return $this;
    }

    /**
     * Clear all options
     * return $this
     */
    public function clearOptions()
    {
        $this->getOptions()->clear();
        return $this;
    }

    /**
     * Set all options from an array
     * @param array|Dictionary $optionList
     * @return $this
     */
    public function setOptions($optionList = [])
    {
        if (!is_array($optionList) && !($optionList instanceof \Traversable)) {
            ExceptionFactory::OptionsTraitException('Invalid list type on method setOptions()');
        }
        $options = $this->getOptions();
        $options->clear();
        foreach ($optionList as $key => $value) {
            $options->add($key, $value);
        }
        return $this;
    }

    /**
     * Retrieve Options Dictionary
     * @return Dictionary
     */
    public function getOptions()
    {
        if (null == $this->optionsDictionary) {
            $this->optionsDictionary = new Dictionary();
        }
        return $this->optionsDictionary;
    }
}