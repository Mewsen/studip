<?php

return new class extends Migration
{
    public function description()
    {
        return 'Zielgruppenspezifische Startseitenwidgets';
    }

    protected function up()
    {
        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `masswidget` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `author_id` CHAR(32) COLLATE latin1_bin NOT NULL,
            `target` ENUM('all', 'students', 'employees', 'lecturers', 'courses', 'usernames') COLLATE latin1_bin,
            `settings` LONGTEXT,
            `exclude_users` LONGTEXT,
            `plugin_id` INT UNSIGNED NOT NULL,
            `row` TINYINT UNSIGNED DEFAULT 0,
            `col` TINYINT UNSIGNED DEFAULT 0,
            `mkdate` INT UNSIGNED NOT NULL,
            `chdate` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`id`),
            INDEX author_id (`author_id`)
        )");

        DBManager::get()->exec("CREATE TABLE IF NOT EXISTS `masswidget_filter` (
            `masswidget_id` INT NOT NULL,
            `filter_id` CHAR(32) COLLATE latin1_bin NOT NULL,
            `mkdate` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`masswidget_id`, `filter_id`),
            INDEX filter_id (`filter_id`)
        )");

        DBManager::get()->exec("ALTER TABLE `widget_user`
                    ADD COLUMN `is_active` TINYINT(1) UNSIGNED DEFAULT 1,
                    ADD COLUMN `chdate` INT UNSIGNED NOT NULL");
    }

    protected function down()
    {
        DBManager::get()->exec("
            DROP TABLE IF EXISTS
                `masswidget_filter`,
                `masswidget`
        ");

        DBManager::get()->exec("ALTER TABLE `widget_user`
                    DROP COLUMN `is_active`,
                    DROP COLUMN `chdate`");
    }
};
