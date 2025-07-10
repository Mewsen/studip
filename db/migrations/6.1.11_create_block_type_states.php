<?php

class CreateBlockTypeStates extends Migration
{
    public function description()
    {
        return 'create table for block type states';
    }

    public function up()
    {
        $db = DBManager::get();
        $query =
            "CREATE TABLE IF NOT EXISTS `cw_block_type_states` (
             `id` int(11) NOT NULL AUTO_INCREMENT,
             `block_type` VARCHAR(255) NOT NULL,
             `activated` TINYINT(1) NOT NULL DEFAULT 0,
             `mkdate` int(11) UNSIGNED NOT NULL,
             `chdate` int(11) UNSIGNED NOT NULL,
             PRIMARY KEY (`id`),
             KEY `block_type` (`block_type`))";
        $db->exec($query);
    }

    public function down()
    {
        $db = DBManager::get();
        $db->exec('DROP TABLE IF EXISTS `cw_block_type_states`');
    }
}
