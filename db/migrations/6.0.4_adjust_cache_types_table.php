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
        return 'Replaces the renamed cache classes in table "cache_types"';
    }

    protected function up()
    {
        foreach (self::MAPPING as $old => $new) {
            self::updateCacheTypesTable($old, $new);
        }
    }

    protected function down()
    {
        foreach (self::MAPPING as $old => $new) {
            self::updateCacheTypesTable($new, $old);
        }
    }

    private function updateCacheTypesTable(string $old, string $new): void
    {
        $query = "UPDATE `cache_types`
                  SET `class_name` = ?
                  WHERE `class_name` = ?";
        DBManager::get()->execute($query, [$new, $old]);
    }
};
