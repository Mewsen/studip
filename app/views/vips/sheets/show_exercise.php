<?php
/**
 * @var int $assignment_id
 * @var VipsAssignment $assignment
 * @var int $exercise_id
 * @var Exercise $exercise
 * @var VipsSolution $solution
 * @var int $remaining_time
 * @var int $user_end_time
 * @var Vips_SheetsController $controller
 * @var string|null $solver_id
 * @var bool $show_solution
 * @var float $max_points
 * @var int|null $exercise_position
 * @var int $tries_left
 */
?>

<? setlocale(LC_NUMERIC, $_SESSION['_language'] . '.UTF-8') ?>

<? if ($assignment->type == 'exam' && !$assignment->checkEditPermission()) : ?>
    <div id="exam_timer" data-time="<?= $remaining_time ?>">
        <?= _('Restzeit') ?>: <span class="time"><?= round($remaining_time / 60) ?></span> min
    </div>

    <div class="width-1200" style="font-weight: bold; text-align: center;">
        <?= _('Abgabezeitpunkt:') ?>
        <?= sprintf(_('%s Uhr'), date('H:i', $user_end_time)) ?>
    </div>
<? endif ?>

<?= $contentbar->render() ?>

<? if ($show_solution) : ?>
    <form class="default width-1200">
        <!-- show feedback for selftest -->
        <?= $this->render_partial('vips/exercises/correct_exercise') ?>

        <fieldset>
            <legend>
                <?= sprintf(_('Bewertung der Aufgabe „%s“'), htmlReady($exercise->title)) ?>
            </legend>

            <? if ($solution->feedback != '') : ?>
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
        </fieldset>
    </form>
<? else : ?>
    <!-- solve and submit exercise -->
    <form class="default width-1200" name="jsfrm" action="<?= $controller->link_for('vips/sheets/submit_exercise') ?>" autocomplete="off" data-secure method="POST" enctype="multipart/form-data">
        <?= CSRFProtection::tokenTag() ?>
        <input type="hidden" name="solver_id" value="<?= $solver_id ?>">
        <input type="hidden" name="exercise_id" value="<?= $exercise_id ?>">
        <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">
        <input type="hidden" name="forced" value="0">

        <fieldset>
            <legend>
                <?= $exercise_position ?>.
                <?= htmlReady($exercise->title) ?>
                <div style="float: right;">
                    <? if ($max_points == (int) $max_points): ?>
                        <?= sprintf(ngettext('%d Punkt', '%d Punkte', $max_points), $max_points) ?>
                    <? else: ?>
                        <?= sprintf(_('%g Punkte'), $max_points) ?>
                    <? endif ?>
                </div>
            </legend>

            <? if ($tries_left > 0): ?>
                <?= MessageBox::warning(sprintf(ngettext(
                        'Ihr Lösungsversuch war nicht korrekt. Sie haben noch %d weiteren Versuch.',
                        'Ihr Lösungsversuch war nicht korrekt. Sie haben noch %d weitere Versuche.', $tries_left), $tries_left)) ?>
            <? endif ?>

            <div class="description">
                <?= formatReady($exercise->description) ?>
            </div>

            <?= $this->render_partial('vips/exercises/show_exercise_hint') ?>
            <?= $this->render_partial('vips/exercises/show_exercise_files') ?>

            <?= $this->render_partial($exercise->getSolveTemplate($solution, $assignment, $solver_id)) ?>

            <? if (!empty($exercise->options['comment'])) : ?>
                <label>
                    <?= _('Bemerkungen zur Lösung (optional)') ?>
                    <textarea name="student_comment"><?= $solution ? htmlReady($solution->student_comment) : '' ?></textarea>
                </label>
            <? endif ?>
        </fieldset>

        <footer>
            <?= Studip\Button::createAccept(_('Speichern'), 'submit_exercise', $exercise->itemCount() ? [] : ['disabled' => 'disabled']) ?>
        </footer>
    </form>
<? endif ?>

<? setlocale(LC_NUMERIC, 'C') ?>
