<form class="default" action="<?= $controller->link_for('my_ilias_accounts/add_workgroup/'.$ilias->index) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <label>
        <span class="required"><?= _('Name des Arbeitsbereichs') ?></span>
        <input type="text" name="ilias_workgroup_name" size="50" maxlength="255" value="<?=htmlReady($ilias_workgroup_name)?>" required>
    </label>
    <footer data-dialog-button>
        <? if ($ilias->isActive()) : ?>
        <?= Studip\Button::createAccept(_('Erstellen'), 'add_workgroup') ?>
        <? endif ?>
        <?= Studip\Button::createCancel(_('Abbrechen'), 'cancel', ['data-dialog' => 'close']) ?>
    </footer>
</form>