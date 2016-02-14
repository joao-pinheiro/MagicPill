<?php

namespace MagicPill\Mixin;

use MagicPill\Collection\Dictionary;

Trait LocalCache
{
    /**
     * @var \MagicPill\Collection\Dictionary
     */
    protected $cacheDictionary = null;

    /**
     * Add an entry to cache
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function cacheAdd($key, $value)
    {
        $this->getCacheDictionary()->add($key, $value);
        return $this;
    }

    /**
     * Retrieve cache entry
     * @param string $key
     * @return mixed
     */
    public function cacheGet($key)
    {
        return $this->getCacheDictionary()->get($key);
    }

    /**
     * Removes a cache key
     * @param string $key
     * @return $this
     */
    public function cacheRemove($key)
    {
        $this->getCacheDictionary()->remove($key);
        return $this;
    }

    /**
     * Checks if a key exists in cache
     * @param $key
     * @return bool
     */
    public function cacheExists($key)
    {
        return $this->getCacheDictionary()->containsKey($key);
    }

    /**
     * Clears the cache dictionary
     * @return $this
     */
    public function cacheClear()
    {
        $this->getCacheDictionary()->clear();
        return $this;
    }

    /**
     * Retrieve cache dictionary
     * @return Dictionary
     */
    protected function getCacheDictionary()
    {
        if (null == $this->cacheDictionary) {
            $this->cacheDictionary = new Dictionary();
        }
        return $this->cacheDictionary;
    }
}