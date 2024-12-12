<?php
/**
 * Session handler for using Stud.IP Cache as session storage
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      André Noack <noack@data-quest.de>
 */
namespace Studip\Session;

class CacheSessionHandler implements \SessionHandlerInterface, \SessionIdInterface, \SessionUpdateTimestampHandlerInterface
{

    const CACHE_KEY_PREFIX = 'session_data';

    private $session_lifetime = 7200;

    private $cache;

    public function __construct($session_lifetime = null)
    {
        if ($session_lifetime) {
            $this->session_lifetime = $session_lifetime;
        }
    }

    /**
     * @inheritDoc
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function destroy($id): bool
    {
        $cache_key = self::CACHE_KEY_PREFIX . '/' . $id;
        $this->cache->expire($cache_key);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function gc($max_lifetime): int|false
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function open($path, $name): bool
    {
        $this->cache = \Studip\Cache\Factory::getCache();
        return true;
    }

    /**
     * @inheritDoc
     */
    public function read($id): string|false
    {
        $cache_key = self::CACHE_KEY_PREFIX . '/' . $id;
        return $this->cache->read($cache_key);
    }

    /**
     * @inheritDoc
     */
    public function write($id, $data): bool
    {
        $cache_key = self::CACHE_KEY_PREFIX . '/' . $id;
        return (bool)$this->cache->write($cache_key, $data, $this->session_lifetime);
    }

    public function create_sid(): string
    {
        do {
            $new_id = md5(bin2hex(random_bytes(128)));
        } while (!$this->read($new_id));
        return $new_id;
    }

    public function updateTimestamp(string $id, string $data): bool
    {
        return $this->write($id, $data);
    }

    public function validateId(string $id): bool
    {
        return (bool)$this->read($id);
    }
}
