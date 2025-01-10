<?php
/**
 * @var ClozeTask $exercise
 * @var VipsSolution $solution
 * @var array $response
 */
?>
<div class="description">
    <!--
    <? foreach (explode('[[]]', formatReady($exercise->task['text'])) as $blank => $text): ?>
     --><?= $text ?><!--
        <? if (isset($exercise->task['answers'][$blank])) : ?>
            <? if ($exercise->interactionType() === 'drag'): ?>
             --><span class="cloze_drop math-tex" title="<?= _('Elemente hier ablegen') ?>">
                    <input type="hidden" name="answer[<?= $blank ?>]" value="<?= htmlReady($response[$blank] ?? '') ?>">
                    <? if (isset($response[$blank]) && $response[$blank] !== ''): ?>
                        <span class="cloze_item drag-handle" data-value="<?= htmlReady($response[$blank]) ?>"><?= htmlReady($response[$blank]) ?></span>
                    <? endif ?>
                </span><!--
            <? elseif ($exercise->isSelect($blank)): ?>
             --><select class="cloze_select" name="answer[<?= $blank ?>]">
                    <? if ($exercise->task['answers'][$blank][0]['text'] !== ''): ?>
                        <option value="">&nbsp;</option>
                    <? endif ?>
                    <? foreach ($exercise->task['answers'][$blank] as $option): ?>
                        <option value="<?= htmlReady($option['text']) ?>" <?= trim($option['text']) === ($response[$blank] ?? '') ? ' selected' : '' ?>>
                            <?= htmlReady($option['text']) ?>
                        </option>
                    <? endforeach ?>
                </select><!--
            <? else: ?>
             --><input type="text" class="character_input cloze_input" name="answer[<?= $blank ?>]"
                       style="width: <?= $exercise->getInputWidth($blank) ?>em;" value="<?= htmlReady($response[$blank] ?? '') ?>"><!--
            <? endif ?>
        <? endif ?>
    <? endforeach ?>
    -->
</div>

<? if ($exercise->interactionType() === 'drag'): ?>
    <span class="cloze_drop cloze_items math-tex">
        <? foreach ($exercise->availableAnswers($solution) as $item): ?>
            <span class="cloze_item drag-handle" data-value="<?= htmlReady($item) ?>"><?= htmlReady($item) ?></span>
        <? endforeach ?>
    </span>
<? endif ?>
