<form action='<?= $controller->url_for('file/oer_post_upload/' . $file_ref_id)?>'
      class='default' method='POST' data-dialog="reload-on-close">
    <?= CSRFProtection::tokenTag() ?>

    <p><?= _('Wenn Sie möchten, können Sie die hochgeladene Datei für den OER Campus bereitstellen.') ?></p>

    <label>
        <input type="radio" name="oer_upload" value="0"/>
        <?= _('Nicht für den OER Campus bereitstellen') ?>
    </label>
    <label>
        <input type="radio" name="oer_upload" value="1"/>
        <?= _('Jetzt für den OER Campus bereitstellen') ?>
    </label>
    <label>
        <input type="radio" name="oer_upload" value="2"/>
        <?= _('Zu einem späteren Zeitpunkt für den OER Campus bereitstellen') ?>
    </label>
    <footer data-dialog-button>
        <?= Studip\Button::create(_("Speichern"))?>
    </footer>
</form>
