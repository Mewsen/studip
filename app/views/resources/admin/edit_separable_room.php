<?php
/**
 * @var Resources_AdminController $controller
 * @var ?SeparableRoom $separable_room
 */
?>
<form class="default" method="post" data-dialog="reload-on-close"
      action="<?= $controller->link_for('resources/admin/save_separable_room/'. ($separable_room->id ?? '')) ?>">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= _('Grunddaten') ?></legend>
        <label>
            <?= _('Name') ?>
            <input type="text" name="name" value="<?= htmlReady($separable_room->name) ?>" maxlength="256">
        </label>
        <label>
            <?= _('Beschreibung') ?>
            <textarea name="description"><?= htmlReady($separable_room->description) ?></textarea>
        </label>
    </fieldset>
    <div data-dialog-button>
        <?= \Studip\Button::createAccept(_('Speichen'), 'save') ?>
        <?= \Studip\Button::createCancel(_('Abbrechen')) ?>
    </div>
</form>
