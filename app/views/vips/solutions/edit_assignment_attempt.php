<?php
/**
 * @var Vips_SolutionsController $controller
 * @var VipsAssignment $assignment
 * @var string $solver_id
 * @var string $view
 * @var VipsAssignmentAttempt $assignment_attempt
 */
?>
<form class="default" action="<?= $controller->store_assignment_attempt() ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <input type="hidden" name="assignment_id" value="<?= $assignment->id ?>">
    <input type="hidden" name="solver_id" value="<?= htmlReady($solver_id) ?>">
    <input type="hidden" name="view" value="<?= htmlReady($view) ?>">

    <label>
        <?= _('Teilnehmer/-in') ?>
        <input type="text" disabled value="<?= htmlReady(get_fullname($solver_id, 'no_title_rev')) ?>">
    </label>

    <label>
        <?= _('Startzeitpunkt') ?>
        <input type="text" disabled value="<?= date('H:i:s', $assignment_attempt->start) ?>">
    </label>

    <label>
        <span class="required"><?= _('Abgabezeitpunkt') ?></span>
        <input type="text" name="end_time" value="<?= date('H:i:s', $assignment->getUserEndTime($solver_id)) ?>" required>
    </label>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), 'submit') ?>
    </footer>
</form>
