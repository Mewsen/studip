<form class="default" action="<?= $controller->link_for('questionnaire/import_file',
    compact('range_type', 'range_id')) ?>"
      method="POST"
      enctype="multipart/form-data">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= _('Fragebögen aus Datei(en) importieren') ?></legend>
        <label>
            <?= _('Datei(en):') ?>
            <input type="file" name="upload[]" required accept="application/json" multiple style="min-width: 40em;">
        </label>
    </fieldset>
    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Importieren')) ?>
    </footer>
</form>
