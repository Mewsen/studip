<?php

/**
 * Adds namespace values to UserFilter database entries that already existed pre-6.0.
 */
final class Step4270 extends Migration
{

    public function description()
    {
        return 'Add additional field to table object_contentmodules';
    }

    public function up()
    {
        DBManager::get()->execute(
            "ALTER TABLE `object_contentmodules` 
             ADD `object_type` VARCHAR(32) NOT NULL DEFAULT '' AFTER `module_type`, 
             ADD `object_parent_id` VARCHAR(32) NOT NULL DEFAULT '' AFTER `object_type`, 
             ADD `object_parent_type` VARCHAR(32) NOT NULL DEFAULT '' AFTER `object_parent_id`"
        );
        DBManager::get()->execute(
            "ALTER TABLE `object_contentmodules`
             DROP PRIMARY KEY, 
             ADD PRIMARY KEY (`object_id`, `module_id`, `system_type`, `object_parent_id`)"
        );
    }

    public function down()
    {
        DBManager::get()->execute(
            "ALTER TABLE `object_contentmodules`
             DROP PRIMARY KEY, 
             ADD PRIMARY KEY (`object_id`, `module_id`, `system_type`)"
        );
        DBManager::get()->execute(
            "ALTER TABLE `object_contentmodules`
             DROP `object_type`,
             DROP `object_parent_id`,
             DROP `object_parent_type`;"
        );
    }
}
