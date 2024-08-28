<?php


class AddPreventRootFolderUploadByStudentsInCoursesConfig extends Migration
{
    public function description()
    {
        return 'Adds the PREVENT_ROOT_FOLDER_UPLOADS_BY_STUDENTS_IN_COURSES configuration.';
    }

    protected function up()
    {
        DBManager::get()->exec(
            "INSERT IGNORE INTO `config`
            (`field`, `value`, `type`, `range`, `section`,
            `mkdate`, `chdate`,
            `description`)
            VALUES
            ('PREVENT_ROOT_FOLDER_UPLOADS_BY_STUDENTS_IN_COURSES', '0', 'boolean', 'global', 'files',
            UNIX_TIMESTAMP(), UNIX_TIMESTAMP(),
            'Studierende können im Dateibereich einer Veranstaltung auf der Ebene des Hauptordners keine Dateien hochladen.')"
        );
    }

    protected function down()
    {
        DBManager::get()->exec(
            "DELETE `config`, `config_values`
             FROM `config`
             LEFT JOIN `config_values` USING (`field`)
             WHERE `field` = 'PREVENT_ROOT_FOLDER_UPLOADS_BY_STUDENTS_IN_COURSES'"
        );
    }
}
