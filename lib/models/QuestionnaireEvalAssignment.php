<?php
class QuestionnaireEvalAssignment extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'questionnaire_eval_assignments';

        $config['belongs_to']['questionnaire'] = [
            'class_name'        => Questionnaire::class,
            'foreign_key'       => 'questionnaire_id',
            'assoc_foreign_key' => 'questionnaire_id'
        ];

        parent::configure($config);
    }
}
