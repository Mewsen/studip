<?php
/**
 * @var ClozeTask $exercise
 * @var VipsSolution $solution
 * @var array $response
 * @var array $results
 * @var bool $print_correction
 * @var bool $show_solution
 */
?>
<? if ($solution->id) : ?>
    <?= htmlReady($response[0]) ?>

    <? if ($print_correction): ?>
        <? if ($results[0]['points'] == 1): ?>
            <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asImg(['title' => _('richtig')]) ?>
        <? elseif ($results[0]['points'] == 0.5): ?>
            <?= Icon::create('decline', Icon::ROLE_STATUS_YELLOW)->asImg(['title' => _('fast richtig')]) ?>
        <? else: ?>
            <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asImg(['title' => _('falsch')]) ?>
        <? endif ?>
    <? endif ?>
<? else : ?>
    <div style="height: 6em;"></div>
<? endif ?>

<? if ($show_solution && $exercise->correctAnswers()) : ?>
    <div>
        <?= _('Richtige Antworten:') ?>

        <span class="correct_item">
            <?= htmlReady(implode(' | ', $exercise->correctAnswers())) ?>
        </span>
    </div>
<? endif ?>
