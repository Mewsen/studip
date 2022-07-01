<?php
if (!$selected_oer_upload) {
    $selected_oer_upload = 0;
}
?>
<form action="<?= $controller->link_for('file/oer_post_upload/', $file_ref_id)?>"
       method="post" class="default" data-dialog="reload-on-close">
    <?= CSRFProtection::tokenTag() ?>

    <p><?= _('Die Datei wurde hochgeladen.') ?></p>
    <span><?= _('Wenn Sie möchten, können Sie die hochgeladene Datei für den OER Campus bereitstellen.') ?></span>
    <span><?= sprintf(_('Falls Sie die Datei zu einem späteren Zeitpunkt bereitstellen möchten,
        wird Ihnen am Ende des Semesters (%s) eine Nachricht zugeschickt.'), $semester_ende) ?></span>
    <fieldset class="select_oer_upload">

        <input type="radio" name="oer_upload" id="oer-upload-no" value="0"
            <? if (0 == $selected_oer_upload) echo 'checked'; ?>>
        <label for="oer-upload-no">
            <div class="icon">
                <?= Icon::create('decline', Icon::ROLE_CLICKABLE)->asImg(32) ?>
            </div>
            <div class="text">
                <?= _('Nicht für den OER Campus bereitstellen.') ?>
            </div>
            <?= Icon::create('arr_1down', Icon::ROLE_CLICKABLE)->asImg(24, ['class' => 'arrow']) ?>
            <?= Icon::create('check-circle', Icon::ROLE_CLICKABLE)->asImg(32, ['class' => 'check']) ?>
        </label>
        <div class="oer_upload_description">
            <div class="description">
                <?= _('Nicht für den OER Campus bereitstellen.') ?>
            </div>
        </div>

    <input type="radio" name="oer_upload" id="oer-upload-yes" value="1"
        <? if (1 == $selected_oer_upload) echo 'checked'; ?>>
        <label for="oer-upload-yes">
            <div class="icon">
                <?= Icon::create('accept', Icon::ROLE_CLICKABLE)->asImg(32) ?>
            </div>
            <div class="text">
                <?= _('Jetzt für den OER Campus bereitstellen.') ?>
            </div>
            <?= Icon::create('arr_1down', Icon::ROLE_CLICKABLE)->asImg(24, ['class' => 'arrow']) ?>
            <?= Icon::create('check-circle', Icon::ROLE_CLICKABLE)->asImg(32, ['class' => 'check']) ?>
        </label>
        <div class="oer_upload_description">
            <div class="description">
                <?= _('Jetzt für den OER Campus bereitstellen.') ?>
            </div>
        </div>

        <input type="radio" name="oer_upload" id="oer-upload-later" value="2"
            <? if (2 == $selected_oer_upload) echo 'checked'; ?>>
            <label for="oer-upload-later">
                <div class="icon">
                    <?= Icon::create('date', Icon::ROLE_CLICKABLE)->asImg(32) ?>
                </div>
                <div class="text">
                    <?= _('Zu einem späteren Zeitpunkt für den OER Campus bereitstellen.') ?>
                </div>
                <?= Icon::create('arr_1down', Icon::ROLE_CLICKABLE)->asImg(24, ['class' => 'arrow']) ?>
                <?= Icon::create('check-circle', Icon::ROLE_CLICKABLE)->asImg(32, ['class' => 'check']) ?>
            </label>
            <div class="oer_upload_description">
                <div class="description">
                    <?= _('Zu einem späteren Zeitpunkt für den OER Campus bereitstellen.') ?>
                </div>
            </div>
    </fieldset>

        <input type="hidden"
               name="redirect_to_files"
               value="redirect_to_files">
    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_("Speichern"))?>
        <?= Studip\Button::createCancel(_("Abbrechen"))?>

    </footer>
</form>
