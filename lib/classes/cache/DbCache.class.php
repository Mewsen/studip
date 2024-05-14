<?php

namespace Studip\Cache;

use DBManager;

/**
 * StudipCache implementation using database table
 *
 * @author    Elmar Ludwig <elmar.ludwig@uos.de>
 */
class DbCache extends Cache
{
    /**
     * @return string A display name (that can be translated) for this cache class.
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
     * Return statistics.
     *
     * @return array|array[]
     *@see Cache::getStats()
     *
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
     * @return array
     *@see Cache::getConfig()
     *
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
        $query = "SELECT `content`, `expires`
                  FROM `cache`
                  WHERE `cache_key` = :key
                    AND `expires` > UNIX_TIMESTAMP()";
        $result = DBManager::get()->fetchOne($query, [':key' => $key]);

        $item = new Item($key);
        if (!empty($result)) {
            $item->setHit();
            if ($result['content']) {
                $item->set(unserialize($result['content']));
            }
            if ($result['expires']) {
                $expiration = new \DateTime();
                $expiration->setTimestamp($result['expires']);
                $item->expiresAt($expiration);
            }
        }
        return $item;
    }

    /**
     * @inheritDoc
     */
    public function hasItem($key)
    {
        $query = "SELECT 1
                  FROM `cache`
                  WHERE `cache_key` = :key
                    AND `expires` > UNIX_TIMESTAMP()";
        return (bool) DBManager::get()->fetchColumn($query, [':key' => $key]);
    }

    /**
     * @inheritDoc
     */
    public function save(\Psr\Cache\CacheItemInterface $item)
    {
        $expiration = $this->getExpiration($item);
        if ($expiration < 1) {
            // The item would expire immediately.
            return false;
        }

        return DBManager::get()->execute(
            'REPLACE INTO `cache` VALUES (?, ?, ?)',
            [$item->getKey(), serialize($item->get()), $expiration]
        );
    }
}
