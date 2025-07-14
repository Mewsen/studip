<?php
/**
 * @var Vips_SheetsController $controller
 * @var int $assignment_id
 * @var VipsAssignment $assignment
 * @var Exercise $exercise
 * @var int $exercise_position
 * @var int $max_points
 */
?>

<?= $contentbar->render() ?>

<form class="default width-1200" action="<?= $controller->store_exercise() ?>" data-secure method="POST" enctype="multipart/form-data">
    <?= CSRFProtection::tokenTag() ?>
    <input type="hidden" name="exercise_type" value="<?= htmlReady($exercise->type) ?>">
    <? if ($exercise->id) : ?>
        <input type="hidden" name="exercise_id" value="<?= $exercise->id ?>">
    <? endif ?>
    <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">
    <button hidden name="store_exercise"></button>

    <fieldset>
        <legend>
            <? if ($exercise->id): ?>
                <?= $exercise_position ?>.
            <? endif ?>
            <?= htmlReady($exercise->getTypeName()) ?>
            <? if ($exercise->id): ?>
                <div style="float: right;">
                    <? if ($max_points == (int) $max_points): ?>
                        <?= sprintf(ngettext('%d Punkt', '%d Punkte', $max_points), $max_points) ?>
                    <? else: ?>
                        <?= sprintf(_('%g Punkte'), $max_points) ?>
                    <? endif ?>
                </div>
            <? endif ?>
        </legend>

        <label>
            <span class="required"><?= _('Titel') ?></span>
            <input type="text" name="exercise_name" class="character_input size-l" value="<?= htmlReady($exercise->title) ?>" required>
        </label>

        <label>
            <?= _('Aufgabentext') ?>
            <textarea name="exercise_question" class="character_input size-l wysiwyg" rows="<?= $exercise->textareaSize($exercise->description) ?>"><?= wysiwygReady($exercise->description) ?></textarea>
        </label>

        <table class="default">
            <? if ($exercise->folder && count($exercise->folder->file_refs)): ?>
                <thead>
                    <tr>
                        <th style="width: 60%;">
                            <?= _('Dateien zur Aufgabe') ?>
                        </th>
                        <th style="width: 10%;">
                            <?= _('Vorschau') ?>
                        </th>
                        <th style="width: 10%;">
                            <?= _('Größe') ?>
                        </th>
                        <th style="width: 15%;">
                            <?= _('Datum') ?>
                        </th>
                        <th class="actions">
                            <?= _('Aktionen') ?>
                        </th>
                    </tr>
                </thead>

                <tbody class="dynamic_list">
                    <? foreach ($exercise->folder->file_refs as $file_ref): ?>
                        <tr class="dynamic_row">
                            <td>
                                <input type="hidden" name="file_ids[]" value="<?= $file_ref->id ?>">
                                <a href="<?= htmlReady($file_ref->getDownloadURL()) ?>" <?= $file_ref->getContentDisposition() === 'inline' ? 'target="_blank"' : '' ?>>
                                    <?= Icon::create('file')->asSvg(['title' => _('Datei herunterladen')]) ?>
                                    <?= htmlReady($file_ref->name) ?>
                                </a>
                            </td>
                            <td>
                                <? if ($file_ref->isImage()): ?>
                                    <img alt="<?= htmlReady($file_ref->name) ?>" src="<?= htmlReady($file_ref->getDownloadURL()) ?>"
                                         style="max-height: 20px; vertical-align: bottom;">
                                <? endif ?>
                            </td>
                            <td>
                                <?= sprintf('%.1f KB', $file_ref->file->size / 1024) ?>
                            </td>
                            <td>
                                <?= date('d.m.Y, H:i', $file_ref->file->mkdate) ?>
                            </td>
                            <td class="actions">
                                <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Datei löschen')]) ?>
                            </td>
                        </tr>
                    <? endforeach ?>
                </tbody>
            <? endif ?>

            <tfoot>
                <tr>
                    <td colspan="5">
                        <?= Studip\Button::create(_('Dateien zur Aufgabe hochladen'), '', ['class' => 'upload vips_file_upload', 'data-label' => _('%d Dateien ausgewählt')]) ?>
                        <span class="file_upload_hint" style="display: none;"><?= _('Klicken Sie auf „Speichern“, um die gewählten Dateien hochzuladen.') ?></span>
                        <?= tooltipIcon(sprintf(_('max. %g MB pro Datei'), FileManager::getUploadTypeConfig($assignment->range_id)['file_size'] / 1048576)) ?>
                        <input class="file_upload attach" style="display: none;" type="file" name="upload[]" multiple>
                    </td>
                </tr>
            </tfoot>
        </table>

        <? if ($exercise->folder && count($exercise->folder->file_refs)): ?>
            <label>
                <input type="checkbox" name="files_visible" value="1" <?= !$exercise->options['files_hidden'] ? 'checked' : '' ?>>
                <?= _('Liste der Dateien unter dem Aufgabentext anzeigen') ?>
            </label>
        <? endif ?>

        <?= $this->render_partial($exercise->getEditTemplate($assignment)) ?>

        <input id="options-toggle" class="options-toggle" type="checkbox" value="on">
        <a class="caption" href="#" role="button" data-toggles="#options-toggle" aria-controls="options-panel" aria-expanded="false">
            <?= Icon::create('arr_1down')->asSvg(['class' => 'toggle-open']) ?>
            <?= Icon::create('arr_1right')->asSvg(['class' => 'toggle-closed']) ?>
            <?= _('Weitere Einstellungen') ?>
        </a>

        <div class="toggle-box" id="options-panel">
            <label>
                <?= _('Hinweise zur Bearbeitung der Aufgabe') ?>
                <textarea name="exercise_hint" class="character_input size-l wysiwyg"><?= wysiwygReady($exercise->options['hint']) ?></textarea>
            </label>

            <label>
                <? if ($assignment->type === 'selftest') : ?>
                    <?= _('Automatisches Feedback bei falscher Antwort') ?>
                <? else : ?>
                    <?= _('Vorlage für den Bewertungskommentar (manuelle Korrektur)') ?>
                <? endif ?>
                <textarea name="feedback" class="character_input size-l wysiwyg"><?= wysiwygReady($exercise->options['feedback']) ?></textarea>
            </label>

            <? if ($assignment->type !== 'selftest') : ?>
                <label>
                    <input type="checkbox" name="exercise_comment" value="1" <?= $exercise->options['comment'] ? 'checked' : '' ?>>
                    <?= _('Eingabe eines Kommentars durch Studierende erlauben') ?>
                </label>
            <? endif ?>
        </div>
    </fieldset>

    <footer>
        <?= Studip\Button::createAccept(_('Speichern'), 'store_exercise') ?>
    </footer>
</form>
