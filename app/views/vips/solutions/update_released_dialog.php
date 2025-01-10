<?php
/**
 * @var Vips_SolutionsController $controller
 * @var int[] $assignment_ids
 * @var int $default
 */
?>
<form class="default" action="<?= $controller->update_released() ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <? foreach ($assignment_ids as $assignment_id): ?>
        <input type="hidden" name="assignment_ids[]" value="<?= $assignment_id ?>">
    <? endforeach ?>

    <label>
        <input type="radio" name="released" value="0" <?= $default == VipsAssignment::RELEASE_STATUS_NONE ? 'checked' : '' ?>>
        <?= _('Nichts') ?>
    </label>

    <label>
        <input type="radio" name="released" value="1" <?= $default == VipsAssignment::RELEASE_STATUS_POINTS ? 'checked' : '' ?>>
        <?= _('Vergebene Punkte') ?>
    </label>

    <label>
        <input type="radio" name="released" value="2" <?= $default == VipsAssignment::RELEASE_STATUS_COMMENTS ? 'checked' : '' ?>>
        <?= _('Punkte und Kommentare') ?>
    </label>

    <label>
        <input type="radio" name="released" value="3" <?= $default == VipsAssignment::RELEASE_STATUS_CORRECTIONS ? 'checked' : '' ?>>
        <?= _('… zusätzlich Aufgaben und Korrektur') ?>
    </label>

    <label>
        <input type="radio" name="released" value="4" <?= $default == VipsAssignment::RELEASE_STATUS_SAMPLE_SOLUTIONS ? 'checked' : '' ?>>
            <?= _('… zusätzlich Musterlösungen') ?>
    </label>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), 'save') ?>
    </footer>
</form>
