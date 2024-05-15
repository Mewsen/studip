<?php

namespace Studip\Cache;

use BadMethodCallException;
use Config;
use DateTime;
use Exception;
use Psr\Cache\CacheItemInterface;
use Redis;
use RedisException;

/**
 * Cache implementation using redis.
 *
 * @author      Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license     GPL2 or any later version
 * @package     studip
 * @subpackage  cache
 * @since       Stud.IP 5.0
 */
class RedisCache extends Cache
{
    use KeyTrait;

    private $redis;

    /**
     * @return string A translateable display name for this cache class.
     */
    public static function getDisplayName(): string
    {
        return _('Redis');
    }

    /**
     * Construct a cache instance.
     *
     * @param string $hostname Hostname of redis server
     * @param int    $port     Port of redis server
     * @param string $auth     Optional auth token/password
     *
     * @throws RedisException
     */
    public function __construct($hostname, $port, string $auth = '')
    {
        if (!extension_loaded('redis')) {
            throw new Exception('Redis extension missing.');
        }

        $this->redis = new Redis();
        $status = $this->redis->connect($hostname, $port, 1);

        if (!$status) {
            throw new Exception('Could not add cache.');
        }

        if ($auth !== '') {
            $this->redis->auth($auth);
        }
    }

    /**
     * Returns the instance of the redis server connection.
     *
     * @return Redis instance
     */
    public function getRedis()
    {
        return $this->redis;
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
     */
    public function expire($arg)
    {
        $key = $this->getCacheKey($arg);
        $this->redis->unlink($key);
    }

    /**
     * Expire all items from the cache.
     */
    public function flush()
    {
        $pattern = $this->getCacheKey('*');
        foreach ($this->redis->keys($pattern) as $key) {
            $this->redis->unlink($key);
        }
    }

    /**
     * @param string $method Method to call
     * @param array $args Arguments to pass
     * @return false|mixed
     */
    public function __call($method, $args)
    {
        if (is_callable([$this->redis, $method])) {
            return call_user_func_array([$this->redis, $method], $args);
        }
        throw new BadMethodCallException("Method {$method} does not exist");
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
        $stats = $this->redis->info();
        $stats['size'] = count($this->redis->keys($this->getCacheKey('*')));
        return ["{$this->redis->getHost()}:{$this->redis->getPort()}" => $stats];
    }

    /*
     * Return the Vue component name and props that handle configuration.
     *
     * @see StudipCache::getConfig()
     *
     * @return array
     */
    public static function getConfig(): array
    {
        $currentCache = Config::get()->SYSTEMCACHE;

        // Set default config for this cache
        $currentConfig = [
            'hostname' => '',
            'port' => null
        ];

        // If this cache is set as system cache, use config from global settings.
        if ($currentCache['type'] == __CLASS__) {
            $currentConfig = $currentCache['config'];
            $currentConfig['port'] = $currentConfig['port'] ? (int) $currentConfig['port'] : null;
        }

        return [
            'component' => 'RedisCacheConfig',
            'props' => $currentConfig
        ];
    }

    /**
     * @inheritDoc
     */
    public function getItem(string $key): CacheItemInterface
    {
        $item = new Item($key);
        $real_key = $this->getCacheKey($key);
        $result = $this->redis->get($real_key);
        if ($result === null) {
            return $item;
        }
        $item->setHit();
        $item->set(unserialize($result));
        $expiration = new DateTime();
        $expiration->setTimestamp($this->redis->expiretime($real_key));
        $item->expiresAt($expiration);
        return $item;
    }

    /**
     * @inheritDoc
     */
    public function hasItem(string $key): bool
    {
        $real_key = $this->getCacheKey($key);
        return $this->redis->get($real_key) !== null;
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        $expiration = $this->getExpiration($item);
        if ($expiration < 1) {
            // The item would expire immediately.
            return false;
        }

        $real_key = $this->getCacheKey($item->getKey());
        return $this->redis->setEx($real_key, $expiration, serialize($item->get()));
    }
}
