<?php
/**
 * @var Vips_SheetsController $controller
 * @var int $assignment_id
 * @var VipsAssignment $assignment
 */
?>
<form class="default" action="<?= $controller->link_for('vips/sheets/start_assignment') ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <input type="hidden" name="assignment_id" value="<?= $assignment->id ?>">

    <div class="description">
        <?= _('Bitte bestätigen Sie den Endzeitpunkt:') ?>
    </div>

    <label class="undecorated">
        <div class="label-text">
            <span class="required"><?= _('Startzeitpunkt') ?></span>
        </div>

        <input type="text" name="start_date" class="size-s" value="<?= date('d.m.Y') ?>" disabled>
        <input type="text" name="start_time" class="size-s" value="<?= date('H:i') ?>" disabled>
    </label>

    <? $required = $assignment->type !== 'selftest' ? 'required' : '' ?>

    <label class="undecorated">
        <div class="label-text">
            <span class="<?= $required ?>"><?= _('Endzeitpunkt') ?></span>
        </div>

        <input type="text" name="end_date" class="size-s" value="<?= $assignment->isUnlimited() ? '' : date('d.m.Y', $assignment->end) ?>" <?= $required ?>>
        <input type="text" name="end_time" class="size-s" value="<?= $assignment->isUnlimited() ? '' : date('H:i', $assignment->end) ?>" <?= $required ?>>
    </label>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), 'submit') ?>
        <?= Studip\Button::createCancel(_('Abbrechen'), 'cancel') ?>
    </footer>
</form>
