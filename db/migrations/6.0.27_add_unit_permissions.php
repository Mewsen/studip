<?php

final class AddUnitPermissions extends Migration
{
    public function description()
    {
        return 'Add cols to cw_units table for permission settings';
    }

    public function up()
    {
        \DBManager::get()->exec("ALTER TABLE `cw_units` 
            ADD `permission_scope` ENUM('unit', 'structural_element') COLLATE latin1_bin NOT NULL DEFAULT 'unit'
            AFTER `creator_id`
        ");
        \DBManager::get()->exec("ALTER TABLE `cw_units` 
            ADD `permission_type` ENUM('all', 'users', 'groups') COLLATE latin1_bin NOT NULL DEFAULT 'all'
            AFTER `permission_scope`
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_units` 
            ADD `visible` ENUM('always', 'never', 'period') COLLATE latin1_bin NOT NULL DEFAULT 'always'
            AFTER `permission_type`
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_units` 
            ADD `visible_all` TINYINT NOT NULL DEFAULT 0
            AFTER `visible`
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_units` 
            ADD `writable` ENUM('always', 'never', 'period') COLLATE latin1_bin NOT NULL DEFAULT 'never'
            AFTER `withdraw_date`
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_units` 
            ADD `writable_all` TINYINT NOT NULL DEFAULT 0
            AFTER `writable`
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_units` 
            ADD `writable_start_date` INT UNSIGNED DEFAULT NULL
            AFTER `writable_all`
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_units` 
            ADD `writable_end_date` INT UNSIGNED DEFAULT NULL
            AFTER `writable_start_date`
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_units` 
            ADD `visible_approval` TEXT NOT NULL
            AFTER `writable_end_date`
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_units` 
            ADD `writable_approval` TEXT NOT NULL
            AFTER `visible_approval`
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_units`
            CHANGE `release_date` `visible_start_date` INT UNSIGNED DEFAULT NULL
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_units`
            CHANGE `withdraw_date` `visible_end_date` INT UNSIGNED DEFAULT NULL
        ");

    }

    public function down()
    {
        \DBManager::get()->exec("ALTER TABLE `cw_units` DROP `permission_scope`");
        \DBManager::get()->exec("ALTER TABLE `cw_units` DROP `permission_type`");
        \DBManager::get()->exec("ALTER TABLE `cw_units` DROP `visible`");
        \DBManager::get()->exec("ALTER TABLE `cw_units` DROP `visible_all`");
        \DBManager::get()->exec("ALTER TABLE `cw_units` DROP `writable`");
        \DBManager::get()->exec("ALTER TABLE `cw_units` DROP `writable_all`");
        \DBManager::get()->exec("ALTER TABLE `cw_units` DROP `writable_start_date`");
        \DBManager::get()->exec("ALTER TABLE `cw_units` DROP `writable_end_date`");
        \DBManager::get()->exec("ALTER TABLE `cw_units` DROP `visible_approval`");
        \DBManager::get()->exec("ALTER TABLE `cw_units` DROP `writable_approval`");


        \DBManager::get()->exec("ALTER TABLE `cw_units`
            CHANGE `visible_start_date` `release_date` INT UNSIGNED DEFAULT NULL
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_units`
            CHANGE `visible_end_date` `withdraw_date` INT UNSIGNED DEFAULT NULL
        ");
    }
}