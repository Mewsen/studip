<form class="default" action="<?= $controller->link_for('questionnaire/import_file') ?>"
      method="POST"
      enctype="multipart/form-data">
    <?= CSRFProtection::tokenTag() ?>

    <h4>
        <?= _('Fragebögen aus Datei(en) importieren') ?>
    </h4>

    <label>
        <?= _('Datei(en):') ?>
        <input type="file" name="upload[]" multiple style="min-width: 40em;">
    </label>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Importieren')) ?>
    </footer>
</form>
