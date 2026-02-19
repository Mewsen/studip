<?php
/**
 * @property Semester $semester belongs_to QuestionnaireEvalCentralProfile
 * @property Questionnaire $template belongs_to QuestionnaireEvalCentralProfile
 * @property int $semester_id database column
 * @property string $optional_templates database column
 */
class QuestionnaireEvalCentralProfile extends SimpleORMap
{
    public const RESULT_VISIBILITY_OPTIONS =
        ['never' => 'nie', 'afterending' => 'nach Ende', 'afterparticipation' => 'nach Teilnahme'];
    public const RESULT_VISIBLE_FOR_OPTIONS = ['autor' => 'Alle', 'tutor' => 'Tutor/-innen', 'dozent' => 'Lehrende'];

    protected static function configure($config = []): void
    {
        $config['db_table'] = 'questionnaire_eval_central_profiles';

        $config['belongs_to']['semester'] = [
            'class_name'        => Semester::class,
            'foreign_key'       => 'semester_id'
        ];

        $config['belongs_to']['template'] = [
            'class_name'        => Questionnaire::class,
            'foreign_key'       => 'template_id',
            'assoc_foreign_key' => 'questionnaire_id'
        ];

        parent::configure($config);
    }
}
