<?php
/**
 * @var Vips_SolutionsController $controller
 * @var int $assignment_id
 * @var string $view
 * @var string $expand
 * @var VipsAssignment $assignment
 * @var int $weights
 */
?>
<form class="default gradebook-lecturer-weights" action="<?= $controller->gradebook_publish() ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>

    <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">
    <input type="hidden" name="view" value="<?= htmlReady($view) ?>">
    <input type="hidden" name="expand" value="<?= htmlReady($expand) ?>">

    <label>
        <span class="required"><?= _('Name im Gradebook') ?></span>
        <input name="title" type="text" required value="<?= htmlReady($assignment->test->title) ?>">
    </label>

    <div hidden>
        <input type="number" disabled value="<?= $weights ?>">
        <output></output>
    </div>

    <label class="gradebook-weight">
        <span class="required"><?= _('Gewichtung') ?></span>
        <div>
            <input name="weight" type="number" required min="0" value="1">
            <output><?= round(100 / ($weights + 1), 1) ?></output>
        </div>
    </label>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Eintragen'), 'publish') ?>
    </footer>
</form>
