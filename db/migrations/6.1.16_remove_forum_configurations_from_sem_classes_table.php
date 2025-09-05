<?php

final class RemoveForumConfigurationsFromSemClassesTable extends Migration
{
    public function up()
    {
        DBManager::get()->exec("
            ALTER TABLE `sem_classes`
                DROP COLUMN `write_access_nobody`,
                DROP COLUMN `topic_create_autor`
        ");
    }

    public function down()
    {
        DBManager::get()->exec("
            ALTER TABLE `sem_classes`
                ADD COLUMN `write_access_nobody` TINYINT(3) UNSIGNED NOT NULL AFTER `show_browse`,
                ADD COLUMN `topic_create_autor` TINYINT(3) UNSIGNED NOT NULL AFTER `write_access_nobody`
        ");
    }
}
