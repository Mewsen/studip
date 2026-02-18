<?php
class QuestionnaireEvalCentralProfiles extends SimpleORMap
{
    protected static function configure($config = []): void
    {
        $config['db_table'] = 'questionnaire_eval_central_profiles';

        parent::configure($config);
    }
}
