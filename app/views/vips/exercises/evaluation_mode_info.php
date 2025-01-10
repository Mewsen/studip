<?php
/**
 * @var bool $evaluation_mode
 * @var Exercise $exercise
 * @var bool $show_solution
 */
?>
<? if ($evaluation_mode && $exercise->itemCount() > 1): ?>
    <div class="description smaller">
        <? if ($evaluation_mode == VipsAssignment::SCORING_NEGATIVE_POINTS) : ?>
            <?= _('Vorsicht: Falsche Antworten geben Punktabzug!') ?>
        <? elseif ($evaluation_mode == VipsAssignment::SCORING_ALL_OR_NOTHING) : ?>
            <?= _('Vorsicht: Falsche Antworten führen zur Bewertung der Aufgabe mit 0 Punkten.') ?>
        <? endif ?>
    </div>
<? endif ?>

<? if ($show_solution): ?>
    <div class="description smaller">
        <?= sprintf(_('Richtige Antworten %shervorgehoben%s.'), '<span class="correct_item">', '</span>') ?>
    </div>
<? endif ?>
