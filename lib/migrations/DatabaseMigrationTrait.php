<?php
trait DatabaseMigrationTrait
{
    /**
     * Returns whether a key/index with the given name exists on the given
     * table.
     */
    protected function keyExists(string $table, string $key): bool
    {
        $query = "SHOW INDEX FROM `$table` WHERE Key_name = ?";
        return (bool) DBManager::get()->fetchOne($query, [$key]);
    }

    /**
     * Returns whether a column with the given name exists on the given table.
     */
    protected function columnExists(string $table, string $column): bool
    {
        $query = "SHOW COLUMNS FROM `{$table}` LIKE ?";
        return (bool) DBManager::get()->fetchOne($query, [$column]);
    }

    /**
     * Returns whether a table with the given name exists.
     */
    protected function tableExists(string $table): bool
    {
        $query = "SHOW TABLES LIKE ?";
        return (bool) DBManager::get()->fetchOne($query, [$table]);
    }
}
