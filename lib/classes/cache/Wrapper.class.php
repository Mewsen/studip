<?php

namespace Studip\Cache;

use Psr\Cache\CacheItemInterface;

/**
 * The cache wrapper wraps a memory cache around another cache. This should
 * reduce the accesses to the actual cache.
 *
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 * @since Stud.IP 5.4
 */
class Wrapper extends Cache
{
    protected Cache $actual_cache;
    protected MemoryCache $memory_cache;

    public function __construct(Cache $actual_cache)
    {
        $this->actual_cache = $actual_cache;
        $this->memory_cache = new MemoryCache();
    }

    /**
     * @inheritdoc
     */
    public function expire($arg)
    {
        $this->memory_cache->expire($arg);
        $this->actual_cache->expire($arg);
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->memory_cache->flush();
        $this->actual_cache->flush();
    }

    public static function getDisplayName(): string
    {
        return static::class;
    }

    public function getStats(): array
    {
        return $this->actual_cache->getStats();
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
        $cached = $this->memory_cache->getItem($key);
        if ($cached->isHit()) {
            return $cached;
        }

        $cached = $this->actual_cache->getItem($key);
        if ($cached->isHit()) {
            $this->memory_cache->save($cached);
        }
        return $cached;
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key)
    {
        return $this->actual_cache->hasItem($key);
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item)
    {
        if ($this->actual_cache->save($item)) {
            return $this->memory_cache->save($item);
        } else {
            return false;
        }
    }
}
