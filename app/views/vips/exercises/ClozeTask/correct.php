<?php
/**
 * @var Exercise $exercise
 * @var VipsSolution $solution
 * @var array $results
 * @var array $response
 * @var bool $show_solution
 */
?>
<div class="description">
    <!--
    <? foreach (explode('[[]]', formatReady($exercise->task['text'])) as $blank => $text) : ?>
     --><?= $text ?><!--
        <? if (isset($exercise->task['answers'][$blank])) : ?>
            <? if ($solution->id): ?>
                <? if ($results[$blank]['points'] == 1): ?>
                 --><span class="correct_item math-tex"><?= htmlReady($response[$blank]) ?><!--
                     --><?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asSvg(['class' => 'correction_inline', 'title' => _('richtig')]) ?><!--
                 --></span><!--
                <? elseif ($results[$blank]['points'] == 0.5): ?>
                 --><span class="fuzzy_item math-tex"><?= htmlReady($response[$blank]) ?><!--
                     --><?= Icon::create('decline', Icon::ROLE_STATUS_YELLOW)->asSvg(['class' => 'correction_inline', 'title' => _('fast richtig')]) ?><!--
                 --></span><!--
                <? elseif (empty($edit_solution) || $results[$blank]['safe']): ?>
                 --><span class="wrong_item math-tex"><?= htmlReady($response[$blank]) ?><!--
                     --><?= Icon::create('decline', Icon::ROLE_STATUS_RED)->asSvg(['class' => 'correction_inline', 'title' => _('falsch')]) ?><!--
                 --></span><!--
                <? else: ?>
                 --><span class="wrong_item math-tex"><?= htmlReady($response[$blank]) ?><!--
                     --><?= Icon::create('question', Icon::ROLE_STATUS_RED)->asSvg(['class' => 'correction_inline', 'title' => _('Unbekannte Antwort')]) ?><!--
                 --></span><!--
                <? endif ?>
            <? endif ?>
            <? if ($show_solution && (empty($results) || $results[$blank]['points'] < 1) && $exercise->correctAnswers($blank)): ?>
             --><span class="correct_item math-tex"><?= htmlReady(implode(' | ', $exercise->correctAnswers($blank))) ?></span><!--
            <? endif ?>
        <? endif ?>
    <? endforeach ?>
    -->
</div>

<?= $this->render_partial('exercises/evaluation_mode_info', ['evaluation_mode' => false]) ?>
