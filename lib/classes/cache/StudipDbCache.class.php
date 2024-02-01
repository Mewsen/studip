<?php
/**
 * StudipCache implementation using database table
 *
 * @package     studip
 * @subpackage  cache
 *
 * @author    Elmar Ludwig <elmar.ludwig@uos.de>
 */
class StudipDbCache extends StudipCache
{

    /**
     * @return string A translateable display name for this cache class.
     */
    public static function getDisplayName(): string
    {
        return _('Datenbank');
    }

    /**
     * Expire item from the cache.
     *
     * @param string $arg a single key
     */
    public function expire($arg)
    {
        $db = DBManager::get();

        $stmt = $db->prepare('DELETE FROM cache WHERE cache_key = ?');
        $stmt->execute([$arg]);
    }

    /**
     * Expire all items from the cache.
     */
    public function flush()
    {
        $db = DBManager::get();

        $db->exec('TRUNCATE TABLE cache');
    }

    /**
     * Delete all expired items from the cache.
     */
    public function purge()
    {
        $db = DBManager::get();

        $stmt = $db->prepare('DELETE FROM cache WHERE expires < ?');
        $stmt->execute([time()]);
    }

    /**
     * Retrieve item from the server.
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
            return unserialize($item->get());
        }
        return false;
    }

    /**
     * Return statistics.
     *
     * @see StudipCache::getStats()
     *
     * @return array|array[]
     */
    public function getStats(): array
    {
        return [
            __CLASS__ => [
                'name' => _('Anzahl Einträge'),
                'value' => DBManager::get()->fetchColumn("SELECT COUNT(*) FROM `cache`")
            ]
        ];
    }

    /**
     * Return the Vue component name and props that handle configuration.
     *
     * @see StudipCache::getConfig()
     *
     * @return array
     */
    public static function getConfig(): array
    {
        return [
            'component' => null,
            'props' => []
        ];
    }

    /**
     * @inheritDoc
     */
    public function getItem($key)
    {
        $db = DBManager::get();
        $stmt = $db->prepare(
            'SELECT `content`, `expires`
            FROM `cache`
            WHERE `cache_key` = :key AND `expires` > :now'
        );
        $stmt->execute(
            [
                'key' => $key,
                'now' => time()
            ]
        );
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $item = new \Studip\CacheItem($key, $result['content'] ?? null, !empty($result));
        if (!empty($result)) {
            $expiration = new DateTime();
            $expiration->setTimestamp($result['expires']);
            $item->expiresAt($expiration);
        }
        return $item;
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key)
    {
        $db = DBManager::get();
        $stmt = $db->prepare(
            "SELECT '1' FROM `cache`
            WHERE `cache_key` = :key AND `expires` > :now"
        );
        $stmt->execute(
            [
                'key' => $key,
                'now' => time()
            ]
        );
        $result = $stmt->fetchColumn();
        if ($result === '1') {
            return true;
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function save(\Psr\Cache\CacheItemInterface $item)
    {
        $expiration = $this->getExpiration($item);
        if ($expiration < 1) {
            //The item would expire immediately.
            return false;
        }

        $db = DBManager::get();
        $stmt = $db->prepare('REPLACE INTO `cache` VALUES(?, ?, ?)');
        return $stmt->execute([$item->getKey(), serialize($item->get()), $expiration]);
    }
}
