<?php
/**
 * Session handler for using Stud.IP database as session storage
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      André Noack <noack@data-quest.de>
 */

namespace Studip\Session;
use \DBManager, \Config, \CronjobTask;

class DbSessionHandler implements \SessionHandlerInterface, \SessionIdInterface, \SessionUpdateTimestampHandlerInterface
{

    private $exists;

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
        return (bool)DBManager::get()->execute("DELETE FROM session_data WHERE sid = ? LIMIT 1", [$id]);
    }

    /**
     * @inheritDoc
     */
    public function gc($max_lifetime): false|int
    {
        //bail out if cronjob activated and not called in cli context
        if (Config::getInstance()->getValue('CRONJOBS_ENABLE')
            && ($task = array_pop(CronjobTask::findByClass('SessionGcJob')))
            && count($task->schedules->findBy('active', 1))
            && PHP_SAPI !== 'cli'
        ) {
            return false;
        }
        return DBManager::get()->execute("DELETE FROM session_data WHERE changed < FROM_UNIXTIME(?) ", [time() - $max_lifetime]);
    }

    /**
     * @inheritDoc
     */
    public function open($path, $name): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    #[\ReturnTypeWillChange]
    public function read($id)
    {
        $str = DBManager::get()->fetchColumn("SELECT val FROM session_data where sid  = ?", [$id]);
        if ($str) {
            $this->exists = $id;
        }
        return (string)$str;
    }

    /**
     * @inheritDoc
     */
    public function write($id, $data): bool
    {
        $db = DBManager::get();
        if ($this->exists === $id) {
            $stmt = $db->prepare("UPDATE session_data SET val = ? WHERE sid = ?");
        } else {
            $stmt = $db->prepare("REPLACE INTO session_data ( val, sid ) VALUES (?, ?)");
        }
        return (bool) $stmt->execute([$data, $id]);
    }

    public function exists($id)
    {
        return (bool)DBManager::get()->fetchColumn("SELECT 1 FROM session_data where sid  = ?", [$id]);
    }

    public function create_sid(): string
    {
        do {
            $new_id = md5(bin2hex(random_bytes(128)));
        } while ($this->exists($new_id));
        $this->exists = null;
        return $new_id;
    }

    public function updateTimestamp(string $id, string $data): bool
    {
        DBManager::get()->execute("UPDATE session_data SET changed = CURRENT_TIMESTAMP() WHERE sid = ?", [$id]);
        return true;
    }

    public function validateId(string $id): bool
    {
        return (bool)$this->exists($id);
    }


}
