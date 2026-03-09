<?php
/**
 * @var ClozeTask $exercise
 * @var VipsSolution $solution
 * @var bool $print_correction
 * @var bool $show_solution
 * @var array $optional_choice
 */
?>
<table class="description inline-content">
    <? foreach ($exercise->task['answers'] as $key => $entry): ?>
        <tr class="mc_row">
            <td class="mc_item">
                <?= formatReady($entry['text']) ?>
            </td>

            <td style="white-space: nowrap;">
                <? if (isset($response[$key]) && $response[$key] !== '' && $response[$key] != -1 && $print_correction): ?>
                    <? if ($response[$key] == $entry['choice']): ?>
                        <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asImg(['class' => 'correction_marker', 'title' => _('richtig')]) ?>
                    <? else: ?>
                        <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asImg(['class' => 'correction_marker', 'title' => _('falsch')]) ?>
                    <? endif ?>
                <? endif ?>

                <? foreach ($exercise->task['choices'] + $optional_choice as $val => $label): ?>
                    <span class="<?= $show_solution && $entry['choice'] == $val ? 'correct_item' : 'mc_item' ?>">
                        <? if (isset($response[$key]) && $response[$key] === "$val"): ?>
                            <?= Assets::img('choice_checked.svg', ['style' => 'margin-left: 1ex;']) ?>
                        <? else: ?>
                            <?= Assets::img('choice_unchecked.svg', ['style' => 'margin-left: 1ex;']) ?>
                        <? endif ?>
                        <?= htmlReady($label) ?>
                    </span>
                <? endforeach ?>
            </td>
        </tr>
    <? endforeach ?>
</table>

<?= $this->render_partial('exercises/evaluation_mode_info') ?>
