<?php
/**
 * @var ClozeTask $exercise
 * @var bool $print_correction
 * @var bool $show_solution
 */
?>
<div class="mc_list inline-content">
    <? foreach ($exercise->task['answers'] as $key => $entry): ?>
        <div class="mc_flex <?= $show_solution && $entry['score'] ? 'correct_item' : 'mc_item' ?>">
            <? if (isset($response[$key]) && $response[$key]): ?>
                <?= Assets::img('choice_checked.svg') ?>
            <? else: ?>
                <?= Assets::img('choice_unchecked.svg') ?>
            <? endif ?>

            <?= formatReady($entry['text']) ?>

            <? if (isset($response[$key]) && $print_correction): ?>
                <? if ((int) $response[$key] == $entry['score']): ?>
                    <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asImg(['class' => 'correction_marker', 'title' => _('richtig')]) ?>
                <? else: ?>
                    <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asImg(['class' => 'correction_marker', 'title' => _('falsch')]) ?>
                <? endif ?>
            <? endif ?>
        </div>
    <? endforeach ?>
</div>

<?= $this->render_partial('exercises/evaluation_mode_info') ?>
