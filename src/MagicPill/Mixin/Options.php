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
        $this->getOptionsDictionary()->add($key, $value);
        return $this;
    }

    /**
     * Retrieve an option by key
     * @param string $key
     * @param mixed $key
     * @return mixed
     */
    public function getOption($key, $defaultValue = null)
    {
        if ($this->getOptionsDictionary()->containsKey($key)) {
            return $this->getOptionsDictionary()->get($key);
        }
        return $defaultValue;
    }

    /**
     * Remove an option by key
     * @param string $key
     * @return $this
     */
    public function removeOption($key)
    {
        $this->getOptionsDictionary()->remove($key);
        return $this;
    }

    /**
     * Clear all options
     * return $this
     */
    public function clearOptions()
    {
        $this->getOptionsDictionary()->clear();
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
        $this->getOptionsDictionary()->fromArray($optionList);
        return $this;
    }

    /**
     * Retrieve all options as an array
     * @return array
     */
    public function getOptions()
    {
        return $this->getOptionsDictionary()->toArray();
    }

    /**
     * Retrieve Options Dictionary
     * @return Dictionary
     */
    public function getOptionsDictionary()
    {
        if (null == $this->optionsDictionary) {
            $this->optionsDictionary = new Dictionary();
        }
        return $this->optionsDictionary;
    }
}