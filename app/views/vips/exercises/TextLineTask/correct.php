<?php
/**
 * @var Exercise $exercise
 * @var VipsSolution $solution
 * @var array $results
 * @var array $response
 * @var bool $show_solution
 * @var bool $edit_solution
 */
?>
<? if ($solution->id): ?>
    <div class="label-text">
        <?= _('Antwort') ?>
    </div>

    <?= htmlReady($response[0]) ?>

    <? if ($results[0]['points'] == 1): ?>
        <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asImg(['title' => _('richtig')]) ?>
    <? elseif ($results[0]['points'] == 0.5): ?>
        <?= Icon::create('decline', Icon::ROLE_STATUS_YELLOW)->asImg(['title' => _('fast richtig')]) ?>
    <? elseif (!$edit_solution || $results[0]['safe']): ?>
        <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asImg(['title' => _('falsch')]) ?>
    <? else: ?>
        <?= Icon::create('question', Icon::ROLE_STATUS_RED)->asImg(['title' => _('unbekannte Antwort')]) ?>
    <? endif ?>
<? endif ?>

<? if ($show_solution && $exercise->correctAnswers()): ?>
    <div class="label-text">
        <?= _('Richtige Antworten') ?>:

        <span class="correct_item">
            <?= htmlReady(implode(' | ', $exercise->correctAnswers())) ?>
        </span>
    </div>
<? endif ?>
