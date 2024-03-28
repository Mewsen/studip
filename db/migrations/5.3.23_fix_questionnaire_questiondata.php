<?php

class FixQuestionnaireQuestiondata extends Migration
{
    public function description()
    {
        return 'fix invalid boolean values in questionnaire_questions';
    }

    public function up()
    {
        DBManager::get()->exec("
            UPDATE questionnaire_questions
            SET questiondata = REPLACE(questiondata, ':\"false\"', ':false')
            WHERE questiondata LIKE '%:\"false\"%'
        ");
    }
}
