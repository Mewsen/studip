<?php
return new class extends Migration
{
    private const MAPPING = [
        StudipDbCache::class        => Studip\Cache\DbCache::class,
        StudipFileCache::class      => Studip\Cache\FileCache::class,
        StudipMemcachedCache::class => Studip\Cache\MemcachedCache::class,
        StudipRedisCache::class     => Studip\Cache\RedisCache::class,
    ];

    public function description()
    {
        return 'Replaces the renamed cache classes in system configuration';
    }

    protected function up()
    {
        foreach (self::MAPPING as $old => $new) {
            self::replaceCache($old, $new);
        }
    }

    protected function down()
    {
        foreach (self::MAPPING as $old => $new) {
            self::replaceCache($new, $old);
        }
    }

    private function replaceCache(string $old, string $new): void
    {
        $query = "UPDATE `config_values`
                  SET `value` = JSON_REPLACE(`value`, '$.type', ?)
                  WHERE `field` = 'SYSTEMCACHE'
                    AND JSON_CONTAINS(`value`, JSON_QUOTE(?), '$.type')";
        DBManager::get()->execute($query, [$new, $old]);
    }
};
