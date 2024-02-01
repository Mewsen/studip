<?php

/**
 * The cache wrapper wraps a memory cache around another cache. This should
 * reduce the accesses to the actual cache.
 *
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 * @since Stud.IP 5.4
 */
class StudipCacheWrapper implements StudipCache
{
    const DEFAULT_MEMORY_EXPIRATION = 60;

    protected $actual_cache;
    protected $memory_cache;

    public function __construct(StudipCache $actual_cache)
    {
        $this->actual_cache = $actual_cache;
        $this->memory_cache = new StudipMemoryCache();
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

    /**
     * @inheritdoc
     */
    public function read($arg)
    {
        $cached = $this->memory_cache->read($arg);
        if ($cached !== false) {
            return $cached;
        }

        $cached = $this->actual_cache->read($arg);
        if ($cached !== false) {
            $this->memory_cache->write($arg, $cached, self::DEFAULT_MEMORY_EXPIRATION);
        }
        return $cached;
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
        return $this->actual_cache->getItem($key);
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
    public function save(\Psr\Cache\CacheItemInterface $item)
    {
        if ($this->actual_cache->save($item)) {
            return $this->memory_cache->save($item);
        } else {
            return false;
        }
    }
}
