<?php

/**
 * Description of class.
 * Second line
 */
final class ExtendFileLogging extends Migration
{
    public function description()
    {
        return 'Extends the logging of files during upload and deletion';
    }

    public function up()
    {
        DBManager::get()->exec("UPDATE `log_actions` SET `info_template` = '%user löscht Datei %info (File-Id: %affected Range-Id: %coaffected)' WHERE `name` = 'FILE_DELETE'");
        DBManager::get()->exec("UPDATE `log_actions` SET `info_template` = '%user löscht Ordner %info (Folder-Id: %affected Range-Id: %coaffected)' WHERE `name` = 'FOLDER_DELETE'");

        DBManager::get()->exec("
            INSERT IGNORE INTO `log_actions`
            SET `action_id` = MD5('FILE_UPLOAD'),
                `name` = 'FILE_UPLOAD',
                `description` = 'Nutzer lädt Datei hoch',
                `info_template` = '%user lädt Datei %info hoch (Folder-Id: %affected Range-Id: %coaffected)',
                `active` = '1',
                `expires` = '0'
        ");

        DBManager::get()->exec("
            INSERT IGNORE INTO `log_actions`
            SET `action_id` = MD5('FILE_UPDATE'),
                `name` = 'FILE_UPDATE',
                `description` = 'Nutzer ändert Datei',
                `info_template` = '%user ändert Datei %info (Folder-Id: %affected Range-Id: %coaffected)',
                `active` = '1',
                `expires` = '0'
        ");

        DBManager::get()->exec("
            INSERT IGNORE INTO `log_actions`
            SET `action_id` = MD5('FOLDER_CREATE'),
                `name` = 'FOLDER_CREATE',
                `description` = 'Nutzer legt Ordner an',
                `info_template` = '%user legt Ordner %info an (Range-Id: %coaffected)',
                `active` = '1',
                `expires` = '0'
        ");
        DBManager::get()->exec("
            INSERT IGNORE INTO `log_actions`
            SET `action_id` = MD5('FOLDER_UPDATE'),
                `name` = 'FOLDER_UPDATE',
                `description` = 'Nutzer aktualisiert Ordner',
                `info_template` = '%user aktualisiert Ordner %info an (Range-Id: %coaffected)',
                `active` = '1',
                `expires` = '0'
        ");
    }

    public function down()
    {
        DBManager::get()->exec("UPDATE `log_actions` SET `info_template` = '%user löscht Datei %info (File-Id: %affected)' WHERE `name` = 'FILE_DELETE'");
        DBManager::get()->exec("UPDATE `log_actions` SET `info_template` = '	%user löscht Datei %info (Folder-Id: %affected)' WHERE `name` = 'FOLDER_DELETE'");
        DBManager::get()->exec("DELETE FROM `log_actions` WHERE `name` = 'FILE_UPLOAD'");
        DBManager::get()->exec("DELETE FROM `log_actions` WHERE `name` = 'FOLDER_CREATE'");
        DBManager::get()->exec("DELETE FROM `log_actions` WHERE `name` = 'FILE_UPDATE'");
        DBManager::get()->exec("DELETE FROM `log_actions` WHERE `name` = 'FOLDER_UPDATE'");
    }
}
