<form class="default" method="post" data-dialog="reload-on-close"
      action="<?= $controller->link_for('course/lti/share') ?>">
    <?= CSRFProtection::tokenTag() ?>
    <?= MessageBox::warning(_('Soll die Veranstaltung über LTI freigegeben werden?')) ?>
    <fieldset>
        <legend><?= _('Hinweise zur Freigabe über LTI') ?></legend>
        <section>
            <?= _('Die Freigabe über LTI hat die folgenden Auswirkungen:') ?>
            <ul>
                <li><?= _('Die Inhalte der Veranstaltung können von externen Personen eingesehen werden.') ?></li>
                <li><?= _('Die Teilnehmendenliste wird deaktiviert, um externen Personen keinen Einblick in die Liste zu geben.') ?></li>
                <li>weitere Punkte TODO</li>
            </ul>
        </section>
    </fieldset>
    <fieldset>
        <legend><?= _('Bestätigung') ?></legend>
        <label>
            <input type="checkbox" name="confirmed" value="1">
            <?= _('Ich habe die Hinweise zur Freigabe über LTI gelesen und möchte die Veranstaltung freigeben.') ?>
        </label>
    </fieldset>
    <div data-dialog-button>
        <?= \Studip\Button::create(_('Freigeben'), 'share') ?>
    </div>
</form>
