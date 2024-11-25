<?php

class AddAutomatedDataQuestionType extends Migration
{

    public function description()
    {
        return 'Adds a new question type Automated Data. You can disable this question type by changing the config QUESTIONNAIRE_AUTOMATED_DATA_PERM.';
    }

    public function up()
    {
        $query = "INSERT IGNORE INTO `config` (`field`, `value`, `type`, `range`, `section`, `mkdate`, `chdate`, `description`)
                  VALUES (:name, :value, :type, :range, :section, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), :description)";

        $statement = DBManager::get()->prepare($query);
        $statement->execute([
            ':name'        => 'QUESTIONNAIRE_AUTOMATED_DATA_PERM',
            ':description' => 'Ab welchem Status (autor, tutor, dozent, admin, root) darf man den Fragetyp Automatik in Fragebögen einbauen?',
            ':range'       => 'global',
            ':type'        => 'string',
            ':section'     => 'global',
            ':value'       => 'autor'
        ]);
    }

    public function down()
    {
        $query = "DELETE FROM `config`
                  WHERE `field` = 'QUESTIONNAIRE_AUTOMATED_DATA_PERM' ";
        DBManager::get()->exec($query);
        $query = "DELETE FROM `config_values`
                  WHERE `field` = 'QUESTIONNAIRE_AUTOMATED_DATA_PERM' ";
        DBManager::get()->exec($query);
    }
}
