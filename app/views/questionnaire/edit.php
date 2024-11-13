<?php
/**
 * @var Questionnaire $questionnaire
 */
$questiontypes = [];
$questiontypes['Vote'] = [
    'name' => Vote::getName(),
    'type' => Vote::class,
    'icon' => Vote::getIconShape(),
    'component' => Vote::getEditingComponent()
];
foreach (get_declared_classes() as $class) {
    if (
        is_subclass_of($class, QuestionType::class)
        && !isset($questiontypes[$class])
    ) {
        $questiontypes[$class] = [
            'name' => $class::getName(),
            'type' => $class,
            'icon' => $class::getIconShape(),
            'component' => $class::getEditingComponent()
        ];
    }
}

$questionnaire_data = [
    'anonymous'        => $questionnaire->anonymous,
    'copyable'         => $questionnaire->copyable,
    'editanswers'      => $questionnaire->editanswers,
    'id'               => $questionnaire->id,
    'questions'        => $questionnaire->questions->map(function ($question) {
        return [
            'id'            => $question->id,
            'questiontype'  => $question->questiontype,
            'internal_name' => $question->internal_name,
            'questiondata'  => $question->questiondata->getArrayCopy(),
        ];
    }),
    'resultvisibility' => $questionnaire->resultvisibility,
    'startdate'        => $questionnaire->isNew() ? _('sofort') : $questionnaire->startdate,
    'stopdate'         => $questionnaire->stopdate,
    'title'            => $questionnaire->title,
];
?>
<?= Studip\VueApp::create('questionnaires/QuestionnaireEditor')
        ->withProps([
            'as-dialog'      => Request::isAjax(),
            'question-data'  => $questionnaire_data,
            'question-types' => $questiontypes,
            'range-id'       => Request::get('range_id'),
            'range-type'     => Request::get('range_type'),
        ]) ?>
