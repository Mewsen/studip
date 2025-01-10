<?php
/**
 * @var bool $show_solution
 * @var array|null $response
 * @var Exercise $exercise
 * @var array $results
 */
?>
<table class="default description inline-content nohover">
    <thead>
        <tr>
            <th>
                <?= _('Anzuordnende Antworten') ?>
            </th>

            <? if ($show_solution): ?>
                <th>
                    <?= _('Richtige Antworten') ?>
                </th>
            <? endif ?>
        </tr>
    </thead>

    <tbody>
        <tr style="vertical-align: top;">
            <td>
                <? if ($response): ?>
                    <? foreach ($response as $n => $id): ?>
                        <? foreach ($exercise->task['answers'] as $i => $answer): ?>
                            <? if ($answer['id'] === $id): ?>
                                <? if ($exercise->task['compare'] === 'sequence'): ?>
                                    <div class="neutral_item">
                                        <?= formatReady($answer['text']) ?>
                                    </div>

                                    <? if ($n + 1 < count($response)): ?>
                                        <div class="correction_marker sequence">
                                            <? if ($results[$i]['points'] == 1): ?>
                                                <span style="color: green;">}</span>
                                                <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asImg(['title' => _('richtig')]) ?>
                                            <? else: ?>
                                                <span style="color: red;">}</span>
                                                <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asImg(['title' => _('falsch')]) ?>
                                            <? endif ?>
                                        </div>
                                    <? endif ?>
                                <? elseif ($exercise->task['compare'] === 'position'): ?>
                                    <div class="<?= $results[$i]['points'] == 1 ? 'correct_item' : 'mc_item' ?>">
                                        <?= formatReady($answer['text']) ?>

                                        <? if ($results[$i]['points'] == 1): ?>
                                            <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asImg(['class' => 'correction_marker', 'title' => _('richtig')]) ?>
                                        <? else: ?>
                                            <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asImg(['class' => 'correction_marker', 'title' => _('falsch')]) ?>
                                        <? endif ?>
                                    </div>
                                <? else: ?>
                                    <div class="mc_item">
                                        <?= formatReady($answer['text']) ?>
                                    </div>
                                <? endif ?>
                            <? endif ?>
                        <? endforeach ?>
                    <? endforeach ?>
                <? else: ?>
                    <? foreach ($exercise->orderedAnswers($response) as $answer): ?>
                        <div class="mc_item">
                            <?= formatReady($answer['text']) ?>
                        </div>
                    <? endforeach ?>
                <? endif ?>
            </td>

            <? if ($show_solution): ?>
                <td>
                    <? foreach ($exercise->task['answers'] as $answer): ?>
                        <div class="correct_item">
                            <?= formatReady($answer['text']) ?>
                        </div>
                    <? endforeach ?>
                </td>
            <? endif ?>
        </tr>
    </tbody>
</table>
