<?php

namespace Studip\Cache;

use Psr\Cache\CacheItemInterface;
use StudipCacheOperation;

/**
 * Proxies a StudipCache and stores the expire operation in the database.
 * These operations are lateron applied to the cache they should have
 * been applied to in the beginning.
 *
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license GPL2 or any later version
 * @since   Stud.IP 3.3
 */
class Proxy extends Cache
{
    protected Cache $actual_cache;
    protected array $proxy_these;

    /**
     * @param Cache $cache       The actual cache object
     * @param mixed $proxy_these List of operations to proxy (should be an
     *                           array but a space seperated string is also
     *                           valid)
     */
    public function __construct(Cache $cache, $proxy_these = ['expire'])
    {
        if (!is_array($proxy_these)) {
            $proxy_these = words($proxy_these);
        }

        $this->actual_cache = $cache;
        $this->proxy_these  = is_array($proxy_these)
                            ? $proxy_these
                            : words($proxy_these);
    }

    /**
     * Expires just a single key.
     *
     * @param string $arg The item's key
     */
    public function expire($arg)
    {
        if (in_array('expire', $this->proxy_these)) {
            try {
                $operation = new StudipCacheOperation([$arg, 'expire']);
                $operation->parameters = serialize([]);
                $operation->store();
            } catch (\Exception) {
            }
        }

        return $this->actual_cache->expire($arg);
    }

    /**
     * Expire all items from the cache.
     */
    public function flush()
    {
        if (in_array('flush', $this->proxy_these)) {
            try {
                $operation = new StudipCacheOperation(['', 'flush']);
                $operation->parameters = serialize([]);
                $operation->store();
            } catch (\Exception) {
            }
        }

        return $this->actual_cache->flush();
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
    public function getItem(string $key): CacheItemInterface
    {
        return $this->actual_cache->getItem($key);
    }

    /**
     * @inheritDoc
     */
    public function hasItem(string $key): bool
    {
        return $this->actual_cache->hasItem($key);
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        if (in_array('save', $this->proxy_these)) {
            try {
                $operation = new StudipCacheOperation([$item->getKey(), 'save']);
                $operation->parameters = serialize([$item]);
                $operation->store();
            } catch (\Exception) {
            }
        }

        return $this->actual_cache->save($item);
    }
}
