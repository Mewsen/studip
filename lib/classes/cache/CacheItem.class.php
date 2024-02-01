<?php

namespace Studip;

class CacheItem implements \Psr\Cache\CacheItemInterface
{
    protected $key;

    protected $value;

    protected ?\DateTime $expiration = null;

    protected bool $cache_hit = false;

    public function __construct($key, $value = null, bool $cache_hit = false, ?\DateTime $expiration = null)
    {
        $this->key       = $key;
        $this->value     = $value;
        $this->cache_hit = $cache_hit;
        if ($expiration) {
            $this->expiration = $expiration;
        }
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
        if ($expiration instanceof \DateTime) {
            $this->expiration = $expiration;
        }
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter($time)
    {
        if ($time instanceof \DateInterval) {
            $this->expiration = new DateTime();
            $this->expiration = $this->expiration->add($time);
        } elseif (is_integer($time)) {
            $this->expiration = new DateTime();
            $this->expiration->setTimestamp(time() + $time);
        } else {
            //Remove any existing expiration:
            $this->expiration = null;
        }
    }

    //\Studip\CacheItem specific methods:

    /**
     * Sets the item to be a cache hit.
     *
     * @return void
     */
    public function setHit() : void
    {
        $this->cache_hit = true;
    }

    public function getExpiration() : ?\DateTime
    {
        return $this->expiration;
    }

    public function getExpirationInSeconds() : int
    {
        if ($this->expiration) {
            return $this->expiration->getTimestamp() - time();
        }
        //No expiration: The cache entry is permanent:
        return PHP_INT_MAX;
    }
}
