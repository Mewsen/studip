<?php

namespace Studip\Cache;

use Memcached;
use Psr\Cache\CacheItemInterface;

/**
 * Cache implementation using memcached.
 *
 * @author    Marcus Lunzenauer <mlunzena@uos.de>
 * @copyright (c) Authors
 * @license GPL2 or any later version
 * @since   5.0
 */
class MemcachedCache extends Cache
{
    use KeyTrait;

    private Memcached $memcache;

    /**
     * @return string A translateable display name for this cache class.
     */
    public static function getDisplayName(): string
    {
        return _('Memcached');
    }

    public function __construct($servers)
    {
        if (!extension_loaded('memcached')) {
            throw new \Exception('Memcache extension missing.');
        }

        $prefix = \Config::get()->STUDIP_INSTALLATION_ID;
        $this->memcache = new Memcached('studip' . ($prefix ? '-' . $prefix : ''));

        if (count($this->memcache->getServerList()) === 0) {
            foreach ($servers as $server) {
                $status = $this->memcache->addServer($server['hostname'], (int) $server['port']);

                if (!$status) {
                    throw new \Exception("Could not add server: {$server['hostname']} @ port {$server['port']}");
                }
            }
        }
    }

    /**
     * Expire item from the cache.
     *
     * Example:
     *
     *   # expires foo
     *   $cache->expire('foo');
     *
     * @param   string $arg a single key.
     * @returns void
     */
    public function expire($arg)
    {
        $key = $this->getCacheKey($arg);
        $this->memcache->delete($key);
    }

    /**
     * Expire all items from the cache.
     */
    public function flush()
    {
        $this->memcache->flush();
    }

    /**
     * Return statistics.
     *
     * @StudipCache::getStats()
     *
     * @return array|array[]
     */
    public function getStats(): array
    {
        return $this->memcache->getStats();
    }

    /**
     * Return the Vue component name and props that handle configuration.
     *
     * @see Cache::getConfig()
     *
     * @return array
     */
    public static function getConfig(): array
    {
        $currentCache = \Config::get()->SYSTEMCACHE;

        // Set default config for this cache
        $currentConfig = [
            'servers' => []
        ];

        // If this cache is set as system cache, use config from global settings.
        if ($currentCache['type'] == __CLASS__) {
            $currentConfig = $currentCache['config'];
        }

        return [
            'component' => 'MemcachedCacheConfig',
            'props' => $currentConfig
        ];
    }

    /**
     * @inheritDoc
     */
    public function getItem($key)
    {
        $item = new Item($key);
        $value = $this->memcache->get($this->getCacheKey($key));
        if ($this->memcache->getResultCode() !== Memcached::RES_NOTFOUND) {
            // Set the value, even if it is the boolean value false:
            $item->setHit();
            $item->set($value);
        }
        return $item;
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key)
    {
        return $this->memcache->checkKey($this->getCacheKey($key));
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item)
    {
        $expiration = $this->getExpiration($item);
        if ($expiration < 1) {
            // The item would expire immediately.
            return false;
        }

        $real_key = $this->getCacheKey($item->getKey());
        return $this->memcache->set($real_key, $item->get(), $expiration);
    }
}
