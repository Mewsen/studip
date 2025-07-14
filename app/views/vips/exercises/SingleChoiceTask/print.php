<?php
/**
 * @var ClozeTask $exercise
 * @var bool $print_correction
 * @var bool $show_solution
 * @var array $optional_answer
 */
?>
<? foreach ($exercise->task as $group => $task): ?>
    <div <?= $group ? 'class="group_separator"' : '' ?>>
        <? if (isset($task['description'])): ?>
            <?= formatReady($task['description']) ?>
        <? endif ?>
    </div>

    <div class="mc_list inline-content">
        <? foreach ($task['answers'] + $optional_answer as $key => $entry): ?>
            <div class="mc_flex <?= $show_solution && $entry['score'] == 1 ? 'correct_item' : 'mc_item' ?>">
                <? if (isset($response[$group]) && $response[$group] === "$key"): ?>
                    <?= Assets::img('choice_checked.svg') ?>
                <? else: ?>
                    <?= Assets::img('choice_unchecked.svg') ?>
                <? endif ?>

                <?= formatReady($entry['text']) ?>

                <? if ($print_correction): ?>
                    <? if (isset($response[$group]) && $response[$group] === "$key"): ?>
                        <? if ($entry['score'] == 1): ?>
                            <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asSvg(['class' => 'correction_marker', 'title' => _('richtig')]) ?>
                        <? elseif ($key != -1): ?>
                            <?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asSvg(['class' => 'correction_marker', 'title' => _('falsch')]) ?>
                        <? endif ?>
                    <? endif ?>
                <? endif ?>
            </div>
        <? endforeach ?>
    </div>
<? endforeach ?>

<?= $this->render_partial('exercises/evaluation_mode_info') ?>
