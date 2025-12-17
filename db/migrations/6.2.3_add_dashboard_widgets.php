<?php

class AddDashboardWidgets extends Migration
{
    public function description()
    {
        return 'Create tables for Dashboard Widgets, including dashboard widgets and dashboard widget containers tables';
    }

    protected function up()
    {
        $db = DBManager::get();
        $db->exec("CREATE TABLE IF NOT EXISTS `dashboard_widget_containers` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for the user\'s container in a specific context.',
            `owner_id` CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL COMMENT 'Identifier of the user owning this container',
            `context` ENUM('community', 'course', 'content', 'start') COLLATE latin1_bin NOT NULL COMMENT 'Context in which the container is used. it could be extended in the future.',
            `context_id` CHAR(32) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'generic' COMMENT 'Identifier of the context (e.g., course id, community id, etc.)',
            `payload` LONGTEXT COLLATE latin1_bin NOT NULL COMMENT 'Serialized data representing the layout and configuration of widgets within the container for different breakpoints.',
            `mkdate` INT(11) UNSIGNED NOT NULL,
            `chdate` INT(11) UNSIGNED NOT NULL,
            PRIMARY KEY (`id`),
            INDEX owner_id (`owner_id`)
        )");

        $db->exec("CREATE TABLE IF NOT EXISTS `dashboard_widgets` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique identifier for the widget in a container',
            `container_id` INT(11) UNSIGNED NOT NULL COMMENT 'Identifier of the container to which this widget belongs',
            `type` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL COMMENT 'Type of the widget (e.g., chat, calendar, etc.)',
            `scope` VARCHAR(255) CHARACTER SET latin1 COLLATE latin1_bin NOT NULL DEFAULT 'default' COMMENT 'The variant of the widget type.',
            `payload` MEDIUMTEXT COLLATE latin1_bin NOT NULL COMMENT 'Serialized data representing the configuration of the widget',
            `mkdate` INT(11) UNSIGNED NOT NULL,
            `chdate` INT(11) UNSIGNED NOT NULL,
            PRIMARY KEY (`id`),
            INDEX container_id (`container_id`)
        )");
    }

    protected function down()
    {
        $db = DBManager::get();
        $db->exec("DROP TABLE IF EXISTS `dashboard_widgets`");
        $db->exec("DROP TABLE IF EXISTS `dashboard_widget_containers`");
    }
};
