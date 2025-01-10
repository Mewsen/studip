<?php
/**
 * @var VipsAssignment $assignment
 * @var string[] $lecturers
 * @var string[]|null $students
 * @var bool $print_student_ids
 * @var string $user_id
 * @var bool $print_sample_solution
 * @var bool $print_correction
 */
?>
<div class="assignment">
    <h1>
        <?= htmlReady($assignment->test->title) ?>
    </h1>

    <div class="description">
        <?= formatReady($assignment->test->description) ?>
    </div>

    <p class="description">
        <?= _('Beginn') ?>: <?= date('d.m.Y, H:i', $assignment->start) ?><br>
        <? if (!$assignment->isUnlimited()): ?>
            <?= _('Ende') ?>: <?= date('d.m.Y, H:i', $assignment->end) ?>
        <? endif ?>
    </p>

    <? if ($assignment->range_type === 'course'): ?>
        <p>
            <?= _('Kurs') ?>: <?= htmlReady($assignment->course->name) ?>
            <? if ($assignment->course->veranstaltungsnummer): ?>
                (<?= htmlReady($assignment->course->veranstaltungsnummer) ?>)
            <? endif ?>
            <br>
            <?= _('Semester') ?>: <?= htmlReady($assignment->course->start_semester->name) ?><br>
            <?= _('Lehrende') ?>: <?= htmlReady(join(', ', $lecturers)) ?>
        </p>

        <p class="label-text">
            <? if (isset($students)): ?>
                <?= _('Name') ?>: <?= htmlReady(join(', ', $students)) ?><br>
            <? else :?>
                <?= _('Name') ?>: ________________________________________<br>
            <? endif ?>
            <? if ($assignment->type == 'exam'): ?>
                <? if (isset($stud_ids) && $print_student_ids): ?>
                    <?= _('Matrikelnummer') ?>: <?= htmlReady(join(', ', $stud_ids)) ?>
                <? else :?>
                    <br>
                    <?= _('Matrikelnummer') ?>: _______________________________
                <? endif ?>
            <? endif ?>
        </p>
    <? endif ?>

    <? foreach ($assignment->getExerciseRefs($user_id) as $i => $exercise_ref): ?>
        <? $exercise = $exercise_ref->exercise ?>
        <? $solution = null ?>

        <? if ($user_id): ?>
            <? $solution = $assignment->getSolution($user_id, $exercise->id); ?>
        <? endif ?>

        <? if (!$solution): ?>
            <? $solution = new VipsSolution(); ?>
            <? $solution->assignment = $assignment; ?>
        <? endif ?>

        <?= $this->render_partial('vips/exercises/print_exercise', [
            'exercise'          => $exercise,
            'exercise_position' => $i + 1,
            'max_points'        => $exercise_ref->points,
            'solution'          => $solution,
            'show_solution'     => $print_sample_solution
        ]) ?>
    <? endforeach ?>

    <? if ($print_correction): ?>
        <? setlocale(LC_NUMERIC, $_SESSION['_language'] . '.UTF-8') ?>
        <? $max_points = $assignment->test->getTotalPoints(); ?>
        <? $reached_points = $assignment->getUserPoints($user_id); ?>
        <? $feedback = $assignment->getUserFeedback($user_id); ?>
        <div class="exercise">
            <h2>
                <?= _('Gesamtpunktzahl') ?>

                <div class="points">
                    <?= sprintf(_('%g Punkte'), $max_points) ?>
                </div>
            </h2>

            <div class="label-text">
                <?= sprintf(_('Erreichte Punkte: %g / %g'), $reached_points, $max_points) ?>
            </div>

            <? if ($feedback != ''): ?>
                <div class="label-text">
                    <?= _('Kommentar zur Bewertung') ?>
                </div>

                <?= formatReady($feedback) ?>
            <? endif ?>
        </div>
        <? setlocale(LC_NUMERIC, 'C') ?>
    <? endif ?>
</div>
