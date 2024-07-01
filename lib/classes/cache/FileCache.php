<?php

namespace Studip\Cache;

use Config;
use Exception;
use Psr\Cache\CacheItemInterface;

/**
 * Cache implementation using files
 *
 * @author    André Noack <noack@data-quest.de>
 * @copyright 2007 André Noack <noack@data-quest.de>
 * @license GPL2 or any later version
 */
class FileCache extends Cache
{
    use KeyTrait;

    /**
     * full path to cache directory
     *
     * @var string
     */
    private string $dir;

    /**
     * @return string A translateable display name for this cache class.
     */
    public static function getDisplayName(): string
    {
        return _('Dateisystem');
    }

    /**
     * without the 'dir' argument the cache path is taken from
     * $CACHING_FILECACHE_PATH or is set to
     * $TMP_PATH/studip_cache
     *
     * @param string $path the path to use
     * @throws Exception if the directory does not exist or could not be
     *         created
     */
    public function __construct(string $path = '')
    {
        $this->dir = $path
                  ?: (
                      Config::get()->SYSTEMCACHE['type'] === self::class
                          ? Config::get()->SYSTEMCACHE['config']['path']
                          : ''
                  )
                  ?: $GLOBALS['CACHING_FILECACHE_PATH']
                  ?: ($GLOBALS['TMP_PATH'] . '/' . 'studip_cache');
        $this->dir = rtrim($this->dir, '\\/') . '/';

        if (!is_dir($this->dir) && !@mkdir($this->dir, 0700)) {
            throw new \Exception('Could not create directory: ' . $this->dir);
        }

        if (!is_writable($this->dir)) {
            throw new \Exception('Can not write to directory: ' . $this->dir);
        }
    }

    /**
     * get path to cache directory
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->dir;
    }

    /**
     * expire cache item
     *
     * @param string $arg
     *
     * @return void
     * @throws Exception
     * @see Cache::expire()
     */
    public function expire($arg)
    {
        $key = $this->getCacheKey($arg);

        if ($file = $this->getPathAndFile($key)){
            @unlink($file);
        }
    }

    /**
     * Expire all items from the cache.
     */
    public function flush()
    {
        rmdirr($this->dir);
    }

    /**
     * checks if specified cache item is expired
     * if expired the cache file is deleted
     *
     * @param string $key a cache key to check
     *
     * @return array|bool the path to the cache file or false if expired
     * @throws Exception
     */
    private function check($key)
    {
        if ($file = $this->getPathAndFile($key)){
            [$id, $expire] = explode('-', basename($file));
            if (time() < $expire) {
                return [$file, $expire];
            } else {
                @unlink($file);
            }
        }
        return false;
    }

    /**
     * get the full path to a cache file
     *
     * the cache files are organized in sub-folders named by
     * the first two characters of the hashed cache key.
     * the filename is constructed from the hashed cache key
     * and the timestamp of expiration
     *
     * @param string   $key    a cache key
     * @param int|null $expire expiry time in seconds
     *
     * @return string|bool full path to cache item or false on failure
     * @throws Exception
     */
    private function getPathAndFile(string $key, ?int $expire = null): bool|string
    {
        $id = hash('md5', $key);
        $path = $this->dir . mb_substr($id, 0, 2);
        if (!is_dir($path) && !@mkdir($path, 0700)) {
            throw new \Exception('Could not create directory: ' . $path);
        }
        if (!is_null($expire)){
            return $path . '/' . $id . '-' . (time() + $expire);
        } else {
            $files = @glob("{$path}/{$id}*");
            if (count($files) > 0) {
                return $files[0];
            }
        }
        return false;
    }

    /**
     * purges expired entries from the cache directory
     *
     * @param bool $be_quiet echo messages if set to false
     *
     * @return int the number of deleted files
     */
    public function purge(bool $be_quiet = true): int
    {
        $now = time();
        $deleted = 0;
        foreach (@glob($this->dir . '*', GLOB_ONLYDIR) as $current_dir){
            foreach (@glob("{$current_dir}/*") as $file){
                [$id, $expire] = explode('-', basename($file));
                if ($expire < $now) {
                    if (@unlink($file)) {
                        ++$deleted;
                        if (!$be_quiet) {
                            echo "File: {$file} deleted.\n";
                        }
                    }
                } else if (!$be_quiet) {
                    echo "File: {$file} expires on " . date('Y-m-d H:i:s', $expire) . "\n";
                }
            }
        }
        return $deleted;
    }

    /**
     * Return statistics.
     *
     * @return array|array[]
     */
    public function getStats(): array
    {
        $count = 0;
        foreach (@glob($this->dir . '*', GLOB_ONLYDIR) as $current_dir){
            $count += count(@glob("{$current_dir}/*"));
        }

        return [
            __CLASS__ => [
                'name' => _('Anzahl Einträge'),
                'value' => $count,
            ],
        ];
    }

    /**
     * Return the Vue component name and props that handle configuration.
     *
     * @return array
     */
    public static function getConfig(): array
    {
        $currentCache = Config::get()->SYSTEMCACHE;

        // Set default config for this cache
        $currentConfig = [
            'path' => $GLOBALS['TMP_PATH'] . '/studip_cache'
        ];

        // If this cache is set as system cache, use config from global settings.
        if ($currentCache['type'] == __CLASS__) {
            $currentConfig = $currentCache['config'];
        }

        return [
            'component' => 'FileCacheConfig',
            'props' => $currentConfig
        ];
    }

    /**
     * @inheritDoc
     */
    public function getItem(string $key): CacheItemInterface
    {
        $real_key = $this->getCacheKey($key);

        $item = new \Studip\Cache\Item($key);

        $file_data = $this->check($real_key);
        if ($file_data) {
            $file = $file_data[0];
            $expire = $file_data[1];
            $f = @fopen($file, 'rb');
            if ($f) {
                @flock($f, LOCK_SH);
                $result = stream_get_contents($f);
                @fclose($f);
            }
            $item->setHit();
            $item->set(unserialize($result));
            $expiration = new \DateTime();
            $expiration->setTimestamp($expire);
            $item->expiresAt($expiration);
        }
        return $item;
    }

    /**
     * @inheritDoc
     */
    public function hasItem(string $key): bool
    {
        $real_key = $this->getCacheKey($key);
        $file_data = $this->check($real_key);
        return $file_data !== false;
    }

    /**
     * @inheritDoc
     */
    public function save(CacheItemInterface $item): bool
    {
        $expiration = $this->getExpiration($item);
        if ($expiration < 1) {
            //The item would expire immediately.
            return false;
        }

        $real_key = $this->getCacheKey($item->getKey());
        $this->expire($real_key);
        $file = $this->getPathAndFile($real_key, $expiration);
        return @file_put_contents($file, serialize($item->get()), LOCK_EX);
    }
}
