<?php
/**
 * @var ClozeTask $exercise
 * @var VipsSolution $solution
 * @var bool $print_correction
 * @var bool $show_solution
 */
?>
<? $exercise->sortAnswersById(); ?>

<table class="content description inline-content" style="min-width: 40em;">
    <thead>
        <tr>
            <th>
                <?= _('Vorgegebener Text') ?>
            </th>

            <th>
                <?= _('Zugeordnete Antworten') ?>
            </th>

            <? if ($show_solution) : ?>
                <th>
                    <?= _('Richtige Antworten') ?>
                </th>
            <? endif ?>
        </tr>
    </thead>

    <tbody>
        <? foreach ($exercise->task['groups'] as $i => $group) : ?>
            <tr style="vertical-align: top;">
                <td>
                    <div class="mc_item">
                        <?= formatReady($group) ?>
                    </div>
                </td>

                <td>
                    <? foreach ($exercise->task['answers'] as $answer): ?>
                        <? if (isset($response[$answer['id']]) && $response[$answer['id']] == $i): ?>
                            <div class="<?= $print_correction && $exercise->isCorrectAnswer($answer, $i) ? 'correct_item' : 'mc_item' ?>">
                                <?= formatReady($answer['text']) ?>

                                <? if ($print_correction): ?>
                                    <? if ($exercise->isCorrectAnswer($answer, $i)) : ?>
                                        <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asImg(['class' => 'correction_marker', 'title' => _('richtig')]) ?>
                                    <? else : ?>
                                        <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asImg(['class' => 'correction_marker', 'title' => _('falsch')]) ?>
                                    <? endif ?>
                                <? endif ?>
                            </div>
                        <? endif ?>
                    <? endforeach ?>
                </td>

                <? if ($show_solution) : ?>
                    <td>
                        <? foreach ($exercise->correctAnswers($i) as $correct_answer): ?>
                            <div class="mc_item">
                                <?= formatReady($correct_answer) ?>
                            </div>
                        <? endforeach ?>
                    </td>
                <? endif ?>
            </tr>
        <? endforeach ?>
    </tbody>
</table>

<div class="label-text">
    <? if ($print_correction): ?>
        <?= _('Nicht zugeordnete Antworten:') ?>
    <? else: ?>
        <?= _('Antwortmöglichkeiten:') ?>
    <? endif ?>
</div>

<ol class="inline-content">
    <? foreach ($exercise->task['answers'] as $answer): ?>
        <? if (!isset($response[$answer['id']]) || $response[$answer['id']] == -1): ?>
            <li>
                <?= formatReady($answer['text']) ?>

                <? if ($solution->id && $print_correction): ?>
                    <? if ($exercise->isCorrectAnswer($answer, -1)): ?>
                        <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asImg(['title' => _('richtig')]) ?>
                    <? else: ?>
                        <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asImg(['title' => _('falsch')]) ?>
                    <? endif ?>
                <? endif ?>
            </li>
        <? endif ?>
    <? endforeach ?>
</ol>
