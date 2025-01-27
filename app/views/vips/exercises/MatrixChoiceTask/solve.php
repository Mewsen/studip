<?php
/**
 * @var ClozeTask $exercise
 * @var array $optional_choice
 */
?>
<table class="description inline-content">
    <? foreach ($exercise->task['answers'] as $key => $entry): ?>
        <tr>
            <td>
                <?= formatReady($entry['text']) ?>
            </td>

            <td style="white-space: nowrap;">
                <? foreach ($exercise->task['choices'] + $optional_choice as $val => $label): ?>
                    <label class="undecorated" style="padding: 1ex;">
                        <input type="radio" name="answer[<?= $key ?>]" value="<?= $val ?>"
                            <? if (!isset($response[$key]) && $val == -1 || isset($response[$key]) && $response[$key] === "$val"): ?>checked<? endif ?>>
                        <?= htmlReady($label) ?>
                    </label>
                <? endforeach ?>
            </td>
        </tr>
    <? endforeach ?>
</table>

<?= $this->render_partial('exercises/evaluation_mode_info') ?>
