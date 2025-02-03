<?php
/**
 * @var int $tries_left
 * @var bool $show_solution
 * @var Exercise $exercise
 * @var float|int $max_points
 * @var VipsSolution $solution
 * @var bool $sample_solution
 * @var VipsAssignment $assignment
 * @var string $user_id
 */
?>
<? if ($tries_left > 0 && !$show_solution): ?>
    <?= MessageBox::warning(sprintf(ngettext(
            'Ihr Lösungsversuch war nicht korrekt. Sie haben noch %d weiteren Versuch.',
            'Ihr Lösungsversuch war nicht korrekt. Sie haben noch %d weitere Versuche.', $tries_left), $tries_left)) ?>
<? endif ?>

<h4 class="exercise">
    <?= htmlReady($exercise->title) ?>

    <div class="points">
        <? if ($max_points == (int) $max_points): ?>
            <?= sprintf(ngettext('%d Punkt', '%d Punkte', $max_points), $max_points) ?>
        <? else: ?>
            <?= sprintf(_('%g Punkte'), $max_points) ?>
        <? endif ?>
    </div>
</h4>

<div class="description">
    <?= formatReady($exercise->description) ?>
</div>

<?= $this->render_partial('vips/exercises/show_exercise_hint') ?>
<?= $this->render_partial('vips/exercises/show_exercise_files') ?>

<? if ($show_solution): ?>
    <?= $this->render_partial($exercise->getCorrectionTemplate($solution), ['show_solution' => $sample_solution]) ?>

    <? if ($exercise->options['comment'] && $solution->student_comment != ''): ?>
        <div class="label-text">
            <?= _('Bemerkungen zur Lösung') ?>
        </div>
        <div class="vips_output">
            <?= htmlReady($solution->student_comment, true, true) ?>
        </div>
    <? endif ?>

    <header>
        <?= _('Bewertung') ?>
    </header>

    <? if ($solution->feedback != ''): ?>
        <div class="label-text">
            <?= _('Anmerkungen zur Lösung') ?>
        </div>
        <div class="vips_output">
            <?= formatReady($solution->feedback) ?>
        </div>
    <? endif ?>

    <div class="description">
        <?= sprintf(_('Erreichte Punkte: %g von %g'), $solution->points, $max_points) ?>
    </div>
<? else: ?>
    <?= $this->render_partial($exercise->getSolveTemplate($solution, $assignment, $user_id)) ?>

    <? if (!empty($exercise->options['comment'])): ?>
        <label>
            <?= _('Bemerkungen zur Lösung (optional)') ?>
            <textarea name="student_comment"><?= htmlReady($solution->student_comment) ?></textarea>
        </label>
    <? endif ?>
<? endif ?>
