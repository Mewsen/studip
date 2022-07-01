<form action="<?= $controller->link_for('file/oer_post_upload/', $file_ref_id)?>"
       method="post" class="default" data-dialog="reload-on-close">
    <?= CSRFProtection::tokenTag() ?>

    <p><?= _('Die Datei wurde hochgeladen.') ?></p>
    <span><?= _('Wenn Sie möchten, können Sie die hochgeladene Datei für den OER Campus bereitstellen.') ?></span>
    <span><?= sprintf(_('Falls Sie die Datei zu einem späteren Zeitpunkt bereitstellen möchten,
        wird Ihnen am Ende des Semesters (%s) eine Nachricht zugeschickt.'), $semester_ende) ?></span>
    <fieldset>
        <label>
            <input type="radio" name="oer_upload" value="0">
            <?= _('Nicht für den OER Campus bereitstellen.') ?>
        </label>
        <label>
            <input type="radio" name="oer_upload" value="1">
            <?= _('Jetzt für den OER Campus bereitstellen.') ?>
        </label>
        <label>
            <input type="radio" name="oer_upload" value="2">
            <?= _('Zu einem späteren Zeitpunkt für den OER Campus bereitstellen.') ?>
        </label>

        <input type="hidden"
               name="redirect_to_files"
               value="redirect_to_files">
    </fieldset>
    <footer data-dialog-button>
        <?= Studip\Button::create(_("Speichern"))?>
    </footer>
</form>
