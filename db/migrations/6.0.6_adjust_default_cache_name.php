<?php
/**
 * @see https://gitlab.studip.de/studip/studip/-/issues/4196
 */
return new class extends Migration
{
    public function description()
    {
        return 'Adjust the default cache in table "config" as well"';
    }

    protected function up()
    {
        $query = "UPDATE `config`
                  SET `value` = JSON_REPLACE(`value`, '$.type', ?)
                  WHERE `field` = 'SYSTEMCACHE'";
        DBManager::get()->execute($query, [Studip\Cache\DbCache::class]);
    }

    protected function down()
    {
        $query = "UPDATE `config`
                  SET `value` = JSON_REPLACE(`value`, '$.type', ?)
                  WHERE `field` = 'SYSTEMCACHE'";
        DBManager::get()->execute($query, [StudipDbCache::class]);
    }
};
