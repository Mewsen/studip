<?php

class SemClassI18n extends Migration
{
    public function description ()
    {
        return 'Add default translations for sem_class and sem_type';
    }

    public function up()
    {
        $translations = [[
            'table' => 'sem_classes',
            'field' => 'name',
            'values' => [
                'Lehre' => 'Teaching',
                'Studiengruppen' => 'Study groups',
            ]
        ], [
            'table' => 'sem_classes',
            'field' => 'description',
            'values' => [
                'Hier finden Sie alle in Stud.IP registrierten Lehrveranstaltungen' => 'Here you will find all the courses registered in Stud.IP',
                'Hier finden Sie virtuelle Veranstaltungen zu unterschiedlichen Themen' => 'Here you will find virtual courses on various topics',
            ]
        ], [
            'table' => 'sem_types',
            'field' => 'name',
            'values' => [
                'Vorlesung' => 'Lecture',
                'Übung' => 'Exercises',
                'Praktikum' => 'Practical training',
                'Forschungsgruppe' => 'Research group',
                'sonstige' => 'miscellaneous',
                'Gremium' => 'Committee',
                'Projektgruppe' => 'Project group',
                'Kulturforum' => 'Culture forum',
                'Veranstaltungsboard' => 'Course board',
                'Studiengruppe' => 'Study group',
            ]
        ]];

        $query = "INSERT INTO i18n SELECT id, ':table', ':field', 'en_GB', :value FROM `:table` WHERE `:field` = :key";
        $stmt = DBManager::get()->prepare($query);

        foreach ($translations as $i18n) {
            foreach ($i18n['values'] as $key => $value) {
                $stmt->bindValue(':table', $i18n['table'], StudipPDO::PARAM_COLUMN);
                $stmt->bindValue(':field', $i18n['field'], StudipPDO::PARAM_COLUMN);
                $stmt->bindValue(':value', $value);
                $stmt->bindValue(':key', $key);
                $stmt->execute();
            }
        }
    }

    public function down()
    {
        $query = "DELETE FROM i18n WHERE `table` IN ('sem_classes', 'sem_types')";
        DBManager::get()->exec($query);
    }
}
