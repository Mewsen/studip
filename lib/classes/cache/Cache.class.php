<?php

namespace Studip\Cache;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

/**
 * An abstract class which has to be extended by instances returned from
 * \Studip\Cache\Factory#getCache
 *
 * @author     Marco Diedrich (mdiedric@uos)
 * @author     Marcus Lunzenauer (mlunzena@uos.de)
 * @author     Moritz Strohm <strohm@data-quest.de>
 * @copyright  (c) Authors
 * @since      1.6
 * @license    GPL2 or any later version
 */
abstract class Cache implements CacheItemPoolInterface
{
    const DEFAULT_EXPIRATION = 12 * 60 * 60; // 12 hours

    /**
     * @return string A translateable display name for this cache class.
     */
    abstract public static function getDisplayName(): string;

    /**
     * Get some statistics from cache, like number of entries, hit rate or
     * whatever the underlying cache provides.
     * Results are returned in form of an array like
     *      "[
     *          [
     *              'name' => <displayable name>
     *              'value' => <value of the current stat>
     *          ]
     *      ]"
     *
     * @return array
     */
    abstract public function getStats(): array;

    /**
     * Return the Vue component name and props that handle configuration.
     * The associative array is of the form
     *  [
     *      'component' => <Vue component name>,
     *      'props' => <Properties for component>
     *  ]
     *
     * @return array
     */
    abstract public static function getConfig(): array;

    /**
     * Expire item from the cache.
     *
     * Example:
     *
     *   # expires foo
     *   $cache->expire('foo');
     *
     * @param string $arg a single key
     */
    abstract public function expire($arg);

    /**
     * Expire all items from the cache.
     */
    abstract public function flush();

    /**
     * @see CacheItemPoolInterface::getItem
     */
    abstract public function getItem(string $key): CacheItemInterface;

    /**
     * @see CacheItemPoolInterface::hasItem
     */
    abstract public function hasItem(string $key): bool;

    /**
     * @var array An array of deferred items that shall be saved only
     * when commit() is called. This is only used in PSR-6 cache methods.
     */
    protected array $deferred_items = [];

    /**
     * Retrieve item from the server.
     *
     * Example:
     *
     *   # reads foo
     *   $foo = $cache->reads('foo');
     *
     * @param string $arg a single key
     *
     * @return mixed    the previously stored data if an item with such a key
     *                  exists on the server or FALSE on failure.
     *
     * @deprecated To be removed with Stud.IP 7.0.
     */
    public function read($arg)
    {
        $item = $this->getItem($arg);
        if ($item->isHit()) {
            return $item->get();
        }
        return false;
    }

    /**
     * Store data at the server.
     *
     * @param string $name     the item's key.
     * @param mixed  $content  the item's content (will be serialized if necessary).
     * @param int    $expires  the item's expiry time in seconds. Optional, defaults to 12h.
     *
     * @return bool     returns TRUE on success or FALSE on failure.

     * @deprecated To be removed with Stud.IP 7.0.
     */
    public function write($name, $content, $expires = self::DEFAULT_EXPIRATION)
    {
        $item = new Item($name, $content, $expires);

        return $this->save($item);
    }

    /**
     * Calculates the expiration by a cache item. If that cannot be determined,
     * the default expiration period is returned.
     *
     * @param Item $item The item from which to get the expiration time.
     *
     * @return int The time from now until the expiration in seconds.
     */
    public function getExpiration(CacheItemInterface $item) : int
    {
        $expiration = self::DEFAULT_EXPIRATION;
        if ($item instanceof Item) {
            $expiration = $item->getExpirationInSeconds();
        }
        return $expiration;
    }

    // PSR-6 CacheItemPoolInterface:

    /**
     * @see CacheItemPoolInterface::getItems
     */
    public function getItems(array $keys = []): iterable
    {
        $items = [];
        foreach ($keys as $key) {
            $item = $this->getItem($key);
            if ($item instanceof Item) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * @see CacheItemPoolInterface::clear
     */
    public function clear(): bool
    {
        $this->deferred_items = [];
        $this->flush();
        return true;
    }

    /**
     * @see CacheItemPoolInterface::deleteItem
     */
    public function deleteItem($key): bool
    {
        $this->expire($key);
        return true;
    }

    /**
     * @see CacheItemPoolInterface::deleteItems
     */
    public function deleteItems(array $keys): bool
    {
        foreach ($keys as $key) {
            $this->expire($key);
        }
        return true;
    }

    /**
     * @see CacheItemPoolInterface::saveDeferred
     */
    public function saveDeferred(CacheItemInterface $item): bool
    {
        $this->deferred_items[] = $item;
        return true;
    }

    /**
     * @see CacheItemPoolInterface::commit
     */
    public function commit(): bool
    {
        foreach ($this->deferred_items as $item) {
            $this->save($item);
        }
        return true;
    }
}
