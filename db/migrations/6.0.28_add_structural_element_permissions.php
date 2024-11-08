<?php

final class AddStructuralElementPermissions extends Migration
{

    public function description()
    {
        return 'Add cols to structural element for permission settings';
    }
    public function up()
    {
        // add cols
        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` 
            ADD `permission_type` ENUM('all', 'users', 'groups') COLLATE latin1_bin NOT NULL DEFAULT 'all'
            AFTER `commentable`
        ");
        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` 
            ADD `visible` ENUM('always', 'never', 'period') COLLATE latin1_bin  NOT NULL DEFAULT 'always'
            AFTER `permission_type`
        ");
        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` 
            ADD `visible_all` TINYINT NOT NULL DEFAULT 0
            AFTER `visible`
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` 
            ADD `writable` ENUM('always', 'never', 'period') COLLATE latin1_bin NOT NULL DEFAULT 'never'
            AFTER `withdraw_date`
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` 
            ADD `writable_all` TINYINT NOT NULL DEFAULT 0
            AFTER `writable`
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` 
            ADD `writable_start_date` INT UNSIGNED NULL DEFAULT NULL
            AFTER `writable_all`
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` 
            ADD `writable_end_date` INT UNSIGNED NULL DEFAULT NULL
            AFTER `writable_start_date`
        ");

            \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` 
                ADD `content_approval` TEXT NOT NULL
                AFTER `write_approval`
            ");

        // change cols
        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements`
            CHANGE `release_date` `visible_start_date` INT UNSIGNED NULL DEFAULT NULL
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements`
            CHANGE `withdraw_date` `visible_end_date` INT UNSIGNED NULL DEFAULT NULL
        ");

        \DBManager::get()->exec("UPDATE `cw_structural_elements` SET `visible_start_date` = NULL WHERE `visible_start_date` = 0 ");

        \DBManager::get()->exec("UPDATE `cw_structural_elements` SET `visible_end_date` = NULL WHERE `visible_end_date` = 0 ");

        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements`
            CHANGE `read_approval` `visible_approval` TEXT NOT NULL
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements`
            CHANGE `write_approval` `writable_approval` TEXT NOT NULL
        ");
    }

    public function down()
    {
        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` DROP `visible`");
        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` DROP `visible_all`");
        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` DROP `writable`");
        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` DROP `writable_all`");
        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` DROP `writable_start_date`");
        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements` DROP `writable_end_date`");

        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements`
            CHANGE `visible_start_date` `release_date` INT UNSIGNED DEFAULT NULL
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements`
            CHANGE `visible_end_date` `withdraw_date` INT UNSIGNED DEFAULT NULL
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements`
            CHANGE `visible_approval` `read_approval` TEXT NOT NULL
        ");

        \DBManager::get()->exec("ALTER TABLE `cw_structural_elements`
            CHANGE `writable_approval` `write_approval` TEXT NOT NULL
        ");

    }

}