<?php
/**
 * @var int $exercise_position
 * @var Exercise $exercise
 * @var float|int $max_points
 * @var VipsSolution $solution
 * @var VipsAssignment $assignment
 * @var string $user_id
 * @var bool $print_correction
 */
?>
<div class="exercise">
    <h3>
        <?= $exercise_position ?>.
        <?= htmlReady($exercise->title) ?>

        <div class="points">
            <? if ($max_points == (int) $max_points): ?>
                <?= sprintf(ngettext('%d Punkt', '%d Punkte', $max_points), $max_points) ?>
            <? else: ?>
                <?= sprintf(_('%g Punkte'), $max_points) ?>
            <? endif ?>
        </div>
    </h3>

    <div class="description">
        <?= formatReady($exercise->description) ?>
    </div>

    <?= $this->render_partial('vips/exercises/show_exercise_hint') ?>
    <?= $this->render_partial('vips/exercises/show_exercise_files') ?>

    <?= $this->render_partial($exercise->getPrintTemplate($solution, $assignment, $user_id)) ?>

    <? if ($solution && $solution->student_comment != '') : ?>
        <div class="label-text">
            <?= _('Bemerkungen zur Lösung:') ?>
        </div>

        <?= htmlReady($solution->student_comment, true, true) ?>
    <? endif ?>

    <? if ($print_correction): ?>
        <? if ($solution): ?>
            <? if ($solution->feedback != ''): ?>
                <div class="label-text">
                    <?= _('Anmerkung des Korrektors:') ?>
                </div>

                <?= formatReady($solution->feedback) ?>
            <? endif ?>

            <?= $this->render_partial('vips/solutions/feedback_files') ?>
        <? endif ?>

        <div class="label-text">
            <?= sprintf(_('Erreichte Punkte: %g / %g'), $solution->points, $max_points) ?>
        </div>
    <? endif ?>
</div>
