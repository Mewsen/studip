<?php
/**
 * @var Questionnaire $questionnaire
 */
$questiontypes = [];
$questiontypes['Vote'] = [
    'name' => Vote::getName(),
    'type' => Vote::class,
    'icon' => Vote::getIconShape(),
    'component' => Vote::getAnsweringComponent(),
    'is_design_element' => false,
];
foreach (get_declared_classes() as $class) {
    if (
        is_subclass_of($class, QuestionType::class)
        && !isset($questiontypes[$class])
        && isset($class::getAnsweringComponent()[0])
    ) {
        $questiontypes[$class] = [
            'name' => $class::getName(),
            'type' => $class,
            'icon' => $class::getIconShape(),
            'component' => $class::getAnsweringComponent(),
            'is_design_element' => $class::isDesignElement()
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
<?= Studip\VueApp::create('questionnaires/QuestionnaireAnswer')
    ->withProps(['questionnaireData' => $questionnaire_data]) ?>

<!-- TODO check why footer buttons don't work in template -->
<div data-dialog-button style="text-align: center;">
    <? if ($questionnaire->isAnswerable()) : ?>
        <?= \Studip\Button::create(_("Speichern"), 'questionnaire_answer', ['onClick' => "return STUDIP.Questionnaire.beforeAnswer.call(this);"]) ?>
    <? endif ?>
    <? if ($questionnaire->resultsVisible()) : ?>
        <?= \Studip\LinkButton::create(_("Ergebnisse anzeigen"), URLHelper::getURL("dispatch.php/questionnaire/evaluate/".$questionnaire->getId()), ['data-dialog' => '']) ?>
    <? endif ?>
    <? if ($questionnaire->isEditable() && (!$questionnaire->isRunning() || !$questionnaire->countAnswers())) : ?>
        <?= \Studip\LinkButton::create(_("Bearbeiten"), URLHelper::getURL("dispatch.php/questionnaire/edit/".$questionnaire->getId()), ['data-dialog' => '']) ?>
    <? endif ?>
    <? if ($questionnaire->isEditable()) : ?>
        <?= \Studip\LinkButton::create(_("Kontext auswählen"), URLHelper::getURL("dispatch.php/questionnaire/context/".$questionnaire->getId(), ['range_type' => $range_type, 'range_id' => $range_id]), ['data-dialog' => '']) ?>
    <? endif ?>
    <? if ($questionnaire->isCopyable()) : ?>
        <?= \Studip\LinkButton::create(_("Kopieren"), URLHelper::getURL("dispatch.php/questionnaire/copy/".$questionnaire->getId()), ['data-dialog' => '']) ?>
    <? endif ?>
    <? if ($questionnaire->isEditable() && (!$questionnaire->isRunning())) : ?>
        <?= \Studip\LinkButton::create(_("Starten"), URLHelper::getURL("dispatch.php/questionnaire/start/".$questionnaire->getId(), in_array($range_type, ['course', 'insitute']) ? ['redirect' => $range_type . "/overview"] : [])) ?>
    <? endif ?>
    <? if ($questionnaire->isEditable() && $questionnaire->isRunning()) : ?>
        <?= \Studip\LinkButton::create(_("Beenden"), URLHelper::getURL("dispatch.php/questionnaire/stop/".$questionnaire->getId(), in_array($range_type, ['course', 'insitute']) ? ['redirect' => $range_type . "/overview"] : [])) ?>
    <? endif ?>
</div>
