<?php
/**
 * @var ClozeTask $exercise
 */
?>
<? $exercise->sortAnswersById(); ?>

<table class="rh_table inline-content">
    <? foreach ($exercise->task['groups'] as $i => $group): ?>
        <tr style="vertical-align: top">
            <td class="rh_label">
                <?= formatReady($group) ?>
            </td>
            <td class="rh_list <?= htmlReady($exercise->task['select']) ?>" data-group="<?= $i ?>" title="<?= _('Elemente hier ablegen') ?>">
                <? foreach ($exercise->task['answers'] as $answer): ?>
                    <? if (isset($response[$answer['id']]) && $response[$answer['id']] == $i): ?>
                        <div class="rh_item drag-handle" tabindex="0">
                            <?= formatReady($answer['text']) ?>
                            <input type="hidden" name="answer[<?= $answer['id'] ?>]" value="<?= $i ?>">
                        </div>
                    <? endif ?>
                <? endforeach ?>
            </td>
            <? if ($i == 0): ?>
                <td rowspan="<?= count($exercise->task['groups']) ?>" class="rh_list answer_container" data-group="-1">
                    <? foreach ($exercise->task['answers'] as $answer): ?>
                        <? if (!isset($response[$answer['id']]) || $response[$answer['id']] == -1): ?>
                            <div class="rh_item drag-handle" tabindex="0">
                                <?= formatReady($answer['text']) ?>
                                <input type="hidden" name="answer[<?= $answer['id'] ?>]" value="-1">
                            </div>
                        <? endif ?>
                    <? endforeach ?>
                </td>
            <? endif ?>
        </tr>
    <? endforeach ?>
</table>
