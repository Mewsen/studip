<?php

final class AddThemesTable extends Migration
{
    public function description()
    {
        return 'Create table for Stud.IP Themes';
    }

    public function up()
    {
        $query = "CREATE TABLE IF NOT EXISTS `themes` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `active` TINYINT(1) NOT NULL DEFAULT 0,
                    `name` VARCHAR(255) NOT NULL,
                    `origin` ENUM('system', 'custom') COLLATE latin1_bin NOT NULL,
                    `version` VARCHAR(50) NOT NULL,
                    `studip_min_version` VARCHAR(50) NOT NULL,
                    `studip_max_version` VARCHAR(50) NOT NULL,
                    `author` VARCHAR(255) NOT NULL,
                    `description` VARCHAR(255) NOT NULL,
                    `type` ENUM('light', 'dark', 'high-contrast') COLLATE latin1_bin NOT NULL,
                    `values` MEDIUMTEXT NOT NULL,
                    `mkdate` INT(11) NOT NULL,
                    `chdate` INT(11) NOT NULL,
                    PRIMARY KEY (`id`)
                  )";
        DBManager::get()->exec($query);

        $default_values = json_encode([
            '--color--brand-primary'            => '#28497c',
            '--color--brand-primary-contrast'   => '#ffffff',
            '--color--brand-secondary'          => '#28497c',
            '--color--brand-secondary-contrast' => '#ffffff',
            '--color--global-background'        => '#ffffff',
            '--color--font-primary'             => '#101010',
            '--color--font-secondary'           => '#3c454e',
            '--color--font-inactive'            => '#676767',
            '--color--font-inverted'            => '#ffffff',
            '--color--main-navigation-item'     => '#28497c',
            '--color--sidebar-item'             => '#28497c',
            '--color--sidebar-item-hover'       => '#d60000',
            '--color--highlight'                => '#28497c',
            '--color--highlight-hover'          => '#d60000',
            '--color--content-link'             => '#28497c',
            '--color--content-link-hover'       => '#d60000',
        ]);

        $stmt = DBManager::get()->prepare("
            INSERT INTO `themes`
                (`id`, `active`, `name`,  `origin`, `version`, `studip_min_version`, `studip_max_version`, `author`, `description`, `type`, `values`, `mkdate`, `chdate`)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
        ");

        $stmt->execute([
            1,
            1,
            'Stud.IP Light Theme',
            'system',
            '1.0',
            '6.1',
            '6.1',
            'Ron Lucke',
            'Default Light Theme',
            'light',
            $default_values,
        ]);

    }

    public function down()
    {
        DBManager::get()->exec('DROP TABLE IF EXISTS `themes`');
    }
}
