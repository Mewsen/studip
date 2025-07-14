<?php
/**
 * @var ClozeTask $exercise
 * @var VipsSolution $solution
 * @var array $response
 * @var array $results
 * @var bool $print_correction
 * @var bool $show_solution
 */
?>
<div class="description">
    <!--
    <? foreach (explode('[[]]', formatReady($exercise->task['text'])) as $blank => $text): ?>
     --><?= $text ?><!--
        <? if (isset($exercise->task['answers'][$blank])) : ?>
            <? if ($solution->id && $response[$blank] !== ''): ?>
             --><span class="math-tex" style="text-decoration: underline;">&nbsp;&nbsp;<?= htmlReady($response[$blank]) ?>&nbsp;&nbsp;</span><!--
                <? if ($print_correction): ?>
                    <? if ($results[$blank]['points'] == 1): ?>
                     --><?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asSvg(['title' => _('richtig')]) ?><!--
                    <? elseif ($results[$blank]['points'] == 0.5): ?>
                     --><?= Icon::create('decline', Icon::ROLE_STATUS_YELLOW)->asSvg(['title' => _('fast richtig')]) ?><!--
                    <? else: ?>
                     --><?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asSvg(['title' => _('falsch')]) ?><!--
                    <? endif ?>
                <? endif ?>
            <? elseif ($exercise->isSelect($blank)): ?>
                <? foreach ($exercise->task['answers'][$blank] as $index => $option) : ?>
                 --><?= $index ? ' | ' : '' ?><!--
                 --><?= Assets::img('choice_unchecked.svg', ['style' => 'vertical-align: text-bottom;']) ?> <!--
                 --><span class="math-tex" style="border-bottom: 1px dotted black;"><?= htmlReady($option['text']) ?></span><!--
                <? endforeach ?>
            <? else: ?>
             --><?= str_repeat('_', $exercise->getInputWidth($blank)) ?><!--
            <? endif ?>
            <? if ($show_solution && (empty($results) || $results[$blank]['points'] < 1) && $exercise->correctAnswers($blank)): ?>
             --><span class="correct_item math-tex"><?= htmlReady(implode(' | ', $exercise->correctAnswers($blank))) ?></span><!--
            <? endif ?>
        <? endif ?>
    <? endforeach ?>
    -->
</div>

<? if ($exercise->interactionType() === 'drag'): ?>
    <div class="label-text">
        <? if ($print_correction): ?>
            <?= _('Nicht zugeordnete Antworten:') ?>
        <? else: ?>
            <?= _('Antwortmöglichkeiten:') ?>
        <? endif ?>
    </div>

    <ol>
        <? foreach ($exercise->availableAnswers($solution) as $item): ?>
            <li>
                <span class="math-tex"><?= htmlReady($item) ?></span>
            </li>
        <? endforeach ?>
    </ol>
<? endif ?>

<?= $this->render_partial('exercises/evaluation_mode_info', ['evaluation_mode' => false]) ?>
