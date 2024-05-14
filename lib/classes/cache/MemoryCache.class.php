<?php

namespace Studip\Cache;

use DateTime;
use Psr\Cache\CacheItemInterface;

/**
 * The php memory implementation of the StudipCache interface.
 *
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 * @since   Stud.IP 5.0
 */
class MemoryCache extends Cache
{
    protected array $memory_cache = [];

    /**
     * Expires just a single key.
     *
     * @param  string  the key
     */
    public function expire($key)
    {
        unset($this->memory_cache[$key]);
    }

    /**
     * Expire all items from the cache.
     */
    public function flush()
    {
        $this->memory_cache = [];
    }

    public static function getDisplayName(): string
    {
        return 'Memory cache';
    }

    public function getStats(): array
    {
        return [];
    }

    public static function getConfig(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getItem($key)
    {
        $item = new Item($key);
        if (!isset($this->memory_cache[$key])) {
            return $item;
        }
        if ($this->memory_cache[$key]['expires'] < time()) {
            $this->expire($key);
            return $item;
        }
        $item->setHit();
        $item->set($this->memory_cache[$key]['data']);
        if (!empty($this->memory_cache[$key]['expires'])) {
            $expiration = new DateTime();
            $expiration->setTimestamp($this->memory_cache[$key]['expires']);
            $item->expiresAt($expiration);
        }
        return $item;
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key)
    {
        return isset($this->memory_cache[$key])
            && $this->memory_cache[$key]['expires'] < time();
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item)
    {
        $expiration = $this->getExpiration($item);

        $this->memory_cache[$item->getKey()] = [
            'expires' => $expiration + time(),
            'data'    => $item->get(),
        ];

        return true;
    }
}
