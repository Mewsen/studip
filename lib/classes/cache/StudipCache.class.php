<?php
/**
 * An abstract class which has to be extended by instances returned from
 * StudipCacheFactory#getCache
 *
 * @package    studip
 * @subpackage lib
 *
 * @author     Marco Diedrich (mdiedric@uos)
 * @author     Marcus Lunzenauer (mlunzena@uos.de)
 * @author     Moritz Strohm <strohm@data-quest.de>
 * @copyright  (c) Authors
 * @since      1.6
 * @license    GPL2 or any later version
 */

interface StudipCache
{
    const DEFAULT_EXPIRATION = 12 * 60 * 60; // 12 hours

    /**
     * @var array An array of deferred items that shall be saved only
     * when commit() is called. This is only used in PSR-6 cache methods.
     */
    protected array $deferred_items = [];

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
    public function expire($arg);

    /**1
     * Expire all items from the cache.
     */
    public function flush();

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
     */
    public function write($name, $content, $expires = self::DEFAULT_EXPIRATION);

    /**
     * @return string A translateable display name for this cache class.
     */
    public static function getDisplayName(): string;

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
    public function getStats(): array;

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
    public static function getConfig(): array;

    //PSR-6 CacheItemPoolInterface:

    /**
     * @see \Psr\Cache\CacheItemPoolInterface::getItem()
     */
    abstract public function getItem($key);

    /**
     * @see \Psr\Cache\CacheItemPoolInterface::getItems()
     */
    public function getItems(array $keys = array())
    {
        $items = [];
        foreach ($keys as $key) {
            $item = $this->getItem($key);
            if ($item instanceof \Studip\CacheItem) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * @see \Psr\Cache\CacheItemPoolInterface::hasItem()
     */
    abstract public function hasItem($key);

    /**
     * @see \Psr\Cache\CacheItemPoolInterface::clear()
     */
    public function clear()
    {
        $this->deferred_items = [];
        $this->flush();
    }

    /**
     * @see \Psr\Cache\CacheItemPoolInterface::deleteItem()
     */
    public function deleteItem($key)
    {
        $this->expire($key);
    }

    /**
     * @see \Psr\Cache\CacheItemPoolInterface::deleteItems()
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            $this->expire($key);
        }
    }

    /**
     * @see \Psr\Cache\CacheItemPoolInterface::save()
     */
    public function save(\Psr\Cache\CacheItemInterface $item)
    {
        $expiration_seconds = null;
        $expiration = $item->expiresAfter();
        if ($expiration instanceof DateInterval) {
            $now = new DateTime();
            $then = clone $now;
            $then = $then->add($expiration);
            $expiration_seconds = $then->getTimestamp() - $now->getTimestamp();
        } elseif (is_int($expiration) && $expiration > 0) {
            $expiration_seconds = $expiration;
        }
        $this->write($item->getKey(), $item->get(), $expiration_seconds);
    }

    /**
     * @see \Psr\Cache\CacheItemPoolInterface::saveDeferred()
     */
    public function saveDeferred(\Psr\Cache\CacheItemInterface $item)
    {
        $this->deferred_items[] = $item;
    }

    /**
     * @see \Psr\Cache\CacheItemPoolInterface::commit()
     */
    public function commit()
    {
        foreach ($this->deferred_items as $item) {
            $this->save($item);
        }
    }
}
