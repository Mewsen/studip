<?php

final class AddTimetableFilters extends Migration
{
    public function description()
    {
        return 'Adds config fields for filtering content on timetable tiles';
    }


    public function up()
    {
        $query = "INSERT IGNORE INTO `config` (
                    `field`, `value`, `type`, `range`, `section`,
                    `mkdate`, `chdate`, `description`
                  ) VALUES (
                   :field, :value, :type, 'user', '',
                   UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description
                  )";
        $statement = DBManager::get()->prepare($query);

        $statement->execute([
            ':field' => 'TIMETABLE_COURSE_NUMBER_VISIBLE',
            ':value' => 1,
            ':type'  => 'int',
            ':description' => 'Soll die Veranstaltungs-Nummer auf dem Stundenplaneintrag sichtbar sein?',
        ]);
        $statement->execute([
            ':field' => 'TIMETABLE_COURSE_NAME_VISIBLE',
            ':value' => 1,
            ':type'  => 'int',
            ':description' => 'Soll der Veranstaltungs-Titel auf dem Stundenplaneintrag sichtbar sein?',
        ]);
        $statement->execute([
            ':field' => 'TIMETABLE_LECTURERS_VISIBLE',
            ':value' => 0,
            ':type'  => 'boolean',
            ':description' => 'Sollen die Dozenten einer Veranstaltung auf dem Stundenplaneintrag sichtbar sein?',
        ]);
        $statement->execute([
            ':field' => 'TIMETABLE_ROOMS_VISIBLE',
            ':value' => 0,
            ':type'  => 'boolean',
            ':description' => 'Soll der Raum einer Veranstaltung auf dem Stundenplaneintrag sichtbar sein?',
        ]);

    }

    public function down()
    {
        DBManager::get()->exec("
            DELETE `config`, `config_values`
            FROM `config`
                LEFT JOIN `config_values` USING (`field`)
            WHERE `config`.`field` IN (
                'TIMETABLE_COURSE_NUMBER_VISIBLE',
                'TIMETABLE_COURSE_NAME_VISIBLE',
                'TIMETABLE_LECTURERS_VISIBLE',
                'TIMETABLE_ROOMS_VISIBLE'
            )
        ");

    }
}
