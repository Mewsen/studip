<?php

class CreateContainerTypeStates extends Migration
{
    public function description()
    {
        return 'create table for container type states';
    }

    public function up()
    {
        $db = DBManager::get();
        $query =
            "CREATE TABLE IF NOT EXISTS `cw_container_type_states` (
             `id` int(11) NOT NULL AUTO_INCREMENT,
             `container_type` VARCHAR(255) NOT NULL,
             `activated` TINYINT(1) NOT NULL DEFAULT 0,
             `mkdate` int(11) UNSIGNED NOT NULL,
             `chdate` int(11) UNSIGNED NOT NULL,
             PRIMARY KEY (`id`),
             KEY `container_type` (`container_type`))";
        $db->exec($query);
    }

    public function down()
    {
        $db = DBManager::get();
        $db->exec('DROP TABLE IF EXISTS `cw_container_type_states`');
    }
}
