<?php
final class FixTreeChanges extends Migration
{
    public function description()
    {
        return 'Actually removes the config entries from 5.4.6 that was faulty at first';
    }

    protected function up()
    {
        $query = "DELETE `config`, `config_values`
                  FROM `config`
                  LEFT JOIN `config_values` USING (`field`)
                  WHERE `field` IN (
                    'RANGE_TREE_ADMIN_PERM',
                    'SEM_TREE_ADMIN_PERM'
                  )";
        DBManager::get()->exec($query);
    }
}
