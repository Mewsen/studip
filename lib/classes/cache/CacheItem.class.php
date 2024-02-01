<?php

namespace Studip;

class CacheItem implements \Psr\Cache\CacheItemInterface
{
    protected $key;

    protected $value;

    protected bool $cache_hit = false;

    public function __construct($key, $value = null, bool $cache_hit = false)
    {
        $this->key       = $key;
        $this->value     = $value;
        $this->cache_hit = $cache_hit;
    }

    /**
     * @inheritDoc
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function get()
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function isHit()
    {
        return $this->cache_hit;
    }

    /**
     * @inheritDoc
     */
    public function set($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritDoc
     */
    public function expiresAt($expiration)
    {
        // TODO: Implement expiresAt() method.
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter($time)
    {
        // TODO: Implement expiresAfter() method.
    }
}
