<?php
/**
 * @var bool $show_solution
 * @var array|null $response
 * @var Exercise $exercise
 * @var bool $print_correction
 * @var array $results
 */
?>
<table class="content description inline-content" style="min-width: 40em;">
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
                <ol>
                    <? if ($response): ?>
                        <? foreach ($response as $n => $id): ?>
                            <? foreach ($exercise->task['answers'] as $i => $answer): ?>
                                <? if ($answer['id'] === $id): ?>
                                    <? if ($exercise->task['compare'] === 'sequence'): ?>
                                        <li class="neutral_item">
                                            <?= formatReady($answer['text']) ?>
                                        </li>

                                        <? if ($print_correction && $n + 1 < count($response)): ?>
                                            <div class="correction_marker sequence">
                                                <? if ($results[$i]['points'] == 1): ?>
                                                    <span style="color: green;">}</span>
                                                    <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asSvg(['title' => _('richtig')]) ?>
                                                <? else: ?>
                                                    <span style="color: red;">}</span>
                                                    <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asSvg(['title' => _('falsch')]) ?>
                                                <? endif ?>
                                            </div>
                                        <? endif ?>
                                    <? elseif ($exercise->task['compare'] === 'position'): ?>
                                        <li class="<?= $print_correction && $results[$i]['points'] == 1 ? 'correct_item' : 'mc_item' ?>">
                                            <?= formatReady($answer['text']) ?>

                                            <? if ($print_correction): ?>
                                                <? if ($results[$i]['points'] == 1): ?>
                                                    <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asSvg(['class' => 'correction_marker', 'title' => _('richtig')]) ?>
                                                <? else: ?>
                                                    <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asSvg(['class' => 'correction_marker', 'title' => _('falsch')]) ?>
                                                <? endif ?>
                                            <? endif ?>
                                        </li>
                                    <? else: ?>
                                        <li class="mc_item">
                                            <?= formatReady($answer['text']) ?>
                                        </li>
                                    <? endif ?>
                                <? endif ?>
                            <? endforeach ?>
                        <? endforeach ?>
                    <? else: ?>
                        <? foreach ($exercise->orderedAnswers($response) as $answer): ?>
                            <li class="mc_item">
                                <?= formatReady($answer['text']) ?>
                            </li>
                        <? endforeach ?>
                    <? endif ?>
                </ol>
            </td>

            <? if ($show_solution): ?>
                <td>
                    <ol>
                        <? foreach ($exercise->task['answers'] as $answer): ?>
                            <li class="mc_item">
                                <?= formatReady($answer['text']) ?>
                            </li>
                        <? endforeach ?>
                    </ol>
                </td>
            <? endif ?>
        </tr>
    </tbody>
</table>
