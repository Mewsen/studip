<?php
/**
 * @var Exercise $exercise
 * @var VipsSolution $solution
 * @var array $results
 * @var array $response
 * @var bool $show_solution
 */
?>
<? $exercise->sortAnswersById(); ?>

<table class="default description inline-content">
    <thead>
        <tr>
            <th>
                <?= _('Vorgegebener Text') ?>
            </th>

            <th>
                <?= _('Zugeordnete Antworten') ?>
            </th>

            <? if ($show_solution): ?>
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
                            <div class="<?= $exercise->isCorrectAnswer($answer, $i) ? 'correct_item' : 'mc_item' ?>">
                                <?= formatReady($answer['text']) ?>

                                <? if ($exercise->isCorrectAnswer($answer, $i)): ?>
                                    <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asImg(['class' => 'correction_marker', 'title' => _('richtig')]) ?>
                                <? else: ?>
                                    <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asImg(['class' => 'correction_marker', 'title' => _('falsch')]) ?>
                                <? endif ?>
                            </div>
                        <? endif ?>
                    <? endforeach ?>
                </td>

                <? if ($show_solution): ?>
                    <td>
                        <? foreach ($exercise->correctAnswers($i) as $correct_answer): ?>
                            <div class="correct_item">
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
    <?= _('Nicht zugeordnete Antworten:') ?>
</div>

<? foreach ($exercise->task['answers'] as $answer): ?>
    <? if (!isset($response[$answer['id']]) || $response[$answer['id']] == -1): ?>
        <div class="inline-block inline-content <?= $exercise->isCorrectAnswer($answer, -1) ? 'correct_item' : 'mc_item' ?>">
            <?= formatReady($answer['text']) ?>

            <? if ($solution->id): ?>
                <? if ($exercise->isCorrectAnswer($answer, -1)): ?>
                    <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asImg(['class' => 'correction_inline', 'title' => _('richtig')]) ?>
                <? else: ?>
                    <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asImg(['class' => 'correction_inline', 'title' => _('falsch')]) ?>
                <? endif ?>
            <? endif ?>
        </div>
    <? endif ?>
<? endforeach ?>
