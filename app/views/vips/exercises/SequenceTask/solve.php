<?php
/**
 * @var Exercise $exercise
 * @var array $response
 */
?>
<div class="mc_list rh_list inline-content" title="<?= _('Elemente hier ablegen') ?>">
    <? foreach ($exercise->orderedAnswers($response) as $answer): ?>
        <div class="rh_item drag-handle" tabindex="0">
            <?= formatReady($answer['text']) ?>
            <input type="hidden" name="answer[]" value="<?= $answer['id'] ?>">
        </div>
    <? endforeach ?>
</div>
