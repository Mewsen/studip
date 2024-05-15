<?php
/**
 * Item.class.php
 * This file is part of Stud.IP.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @copyright   2024
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       6.0
 */

namespace Studip\Cache;

use DateInterval;
use DateTime;
use Psr\Cache\CacheItemInterface;

/**
 * \Studip\Cache\CacheItem implements the CacheItemInterface of PSR-6. It holds the value and the
 * key of a cache item and also provides additional methods to get the expiration of the item.
 */
class Item implements CacheItemInterface
{
    /**
     * @var string The key of the item in the cache.
     */
    protected string $key;

    /**
     * @var mixed The value of the item.
     */
    protected mixed $value;

    /**
     * @var DateTime|null The expiration as DateTime object or null if the expiration is not defined.
     */
    protected ?DateTime $expiration = null;

    /**
     * @var bool An indicator whether the item has been found in the cache (true) or not (false).
     */
    protected bool $cache_hit = false;

    /**
     * The constructor of \Studip\Cache\CacheItem.
     *
     * @param string $key The key of the item in the cache.
     * @param mixed $value The value of the item.
     * @param int|null $expiration The expiration of the item in seconds, if applicable.
     * @param bool $cache_hit Whether the item shall be constructed as cache hit (true) or not (false).
     *
     */
    public function __construct(
        string $key,
        mixed $value = null,
        ?int $expiration = null,
        bool $cache_hit = false
    ) {
        $this->key         = $key;
        $this->value       = $value;
        $this->cache_hit   = $cache_hit;
        $this->expiresAfter($expiration);
    }

    /**
     * @inheritDoc
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function get(): mixed
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function isHit(): bool
    {
        return $this->cache_hit;
    }

    /**
     * @inheritDoc
     */
    public function set($value): static
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAt($expiration): static
    {
        $this->expiration = $expiration;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter($time): static
    {
        $this->expiration = new DateTime();
        if ($time instanceof DateInterval) {
            $this->expiration = $this->expiration->add($time);
        } elseif (is_integer($time)) {
            $this->expiration->setTimestamp(time() + $time);
        } else {
            $this->expiration->setTimestamp(time() + Cache::DEFAULT_EXPIRATION);
        }
        return $this;
    }

    // \Studip\Cache\CacheItem specific methods:

    /**
     * Sets the item to be a cache hit.
     *
     * @return void
     */
    public function setHit() : void
    {
        $this->cache_hit = true;
    }

    /**
     * Returns the expiration, if set.
     *
     * @return DateTime|null A DateTime object with the expiration date and time
     *     or null if the expiration is not defined.
     */
    public function getExpiration() : ?DateTime
    {
        return $this->expiration;
    }

    /**
     * Returns the seconds from the current timestamp until the expiration of the item.
     *
     * @return int The seconds until the item expires
     */
    public function getExpirationInSeconds() : int
    {
        if ($this->expiration) {
            return $this->expiration->getTimestamp() - time();
        }
        return 0;
    }
}
