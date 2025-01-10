<?php
/**
 * @var ClozeTask $exercise
 */
?>
<? foreach ($exercise->task['answers'] as $key => $entry): ?>
    <label class="inline-content mc_flex">
        <input type="checkbox" name="answer[<?= $key ?>]" value="1"<? if (isset($response[$key]) && $response[$key]): ?> checked<? endif ?>>
        <?= formatReady($entry['text']) ?>
    </label>
<? endforeach ?>

<?= $this->render_partial('exercises/evaluation_mode_info') ?>
