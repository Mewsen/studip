<?php
/**
 * @var ClozeTask $exercise
 * @var array $optional_answer
 */
?>
<? foreach ($exercise->task as $group => $task): ?>
    <div <?= $group ? 'class="group_separator"' : '' ?>>
        <? if (isset($task['description'])): ?>
            <?= formatReady($task['description']) ?>
        <? endif ?>
    </div>

    <? foreach ($task['answers'] + $optional_answer as $key => $entry): ?>
        <label class="inline-content mc_flex">
            <input type="radio" name="answer[<?= $group ?>]" value="<?= $key ?>"
                <? if (!isset($response[$group]) && $key == -1 || isset($response[$group]) && $response[$group] === "$key"): ?>checked<? endif ?>>
            <?= formatReady($entry['text']) ?>
        </label>
    <? endforeach ?>
<? endforeach ?>

<?= $this->render_partial('exercises/evaluation_mode_info') ?>
