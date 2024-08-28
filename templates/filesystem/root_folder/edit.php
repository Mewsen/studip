<?php
/**
 * @var $folder RootFolder
 */
?>
<? if (Config::get()->PREVENT_ROOT_FOLDER_UPLOADS_BY_STUDENTS_IN_COURSES) : ?>
    <label>
        <input type="checkbox"
               name="locked"
            <?= $folder->data_content && $folder->data_content['locked'] === 0 ? 'checked' : '' ?>
               value="0">
        <?= _('Studierenden das Hochladen von Dateien in den Hauptordner erlauben.') ?>
    </label>
    <?= _('Studierenden ist es standardmäßig verboten, Dateien in den Hauptordner einer Veranstaltung hochzuladen.') ?>
<? else: ?>
    <label>
        <input type="checkbox"
               name="locked"
            <?= $folder->data_content && $folder->data_content['locked'] ? 'checked' : '' ?>
               value="1">
        <?= _('Studierenden das Hochladen von Dateien in den Hauptordner verbieten.') ?>
    </label>
    <?= _('Studierenden ist es weiterhin möglich, Dateien in Unterordnern hochzuladen.') ?>
<? endif ?>
