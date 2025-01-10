<?php
/**
 * @var Vips_SheetsController $controller
 */
?>
<form class="default" action="<?= $controller->link_for('vips/sheets/import_test') ?>" method="POST" enctype="multipart/form-data">
    <?= CSRFProtection::tokenTag() ?>

    <h4>
        <?= _('Aufgabenblätter aus Datei(en) importieren') ?>
    </h4>

    <label>
        <?= _('Datei(en):') ?>
        <input type="file" name="upload[]" multiple style="min-width: 40em;">
    </label>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Importieren'), 'import') ?>
    </footer>
</form>
