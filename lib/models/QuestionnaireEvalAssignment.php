<?php

/**
 * @property string semester_id database column
 * @property string course_id database column
 * @property JSONArrayObject course_metadata database column
 * @property null|int $startdate database column
 * @property null|int $stopdate database column
 * @property Questionnaire $questionnaire belongs_to QuestionnaireEvalAssignment
 */
class QuestionnaireEvalAssignment extends SimpleORMap
{
    protected static function configure($config = []): void
    {
        $config['db_table'] = 'questionnaire_eval_assignments';

        $config['belongs_to']['questionnaire'] = [
            'class_name'        => Questionnaire::class,
            'foreign_key'       => 'questionnaire_id',
            'assoc_foreign_key' => 'questionnaire_id'
        ];
        $config['serialized_fields']['course_metadata'] = JSONArrayObject::class;

        parent::configure($config);
    }

    public function delete(): bool
    {
        return $this->questionnaire->delete() && parent::delete();
    }

}
