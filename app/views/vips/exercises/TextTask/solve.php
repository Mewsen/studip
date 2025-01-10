<?php
/**
 * @var ClozeTask $exercise
 * @var VipsSolution $solution
 * @var VipsAssignment $assignment
 */
?>
<? if ($exercise->getLayout() !== 'none'): ?>
    <? if ($exercise->task['template'] != ''): ?>
        <div class="vips_tabs">
            <ul>
                <li>
                    <a href="#solution-<?= $exercise->id ?>">
                        <?= _('Antwort') ?>
                    </a>
                </li>
                <li>
                    <a href="#default-<?= $exercise->id ?>">
                        <?= _('Vorbelegung') ?>
                    </a>
                </li>
            </ul>
    <? else: ?>
        <label>
            <?= _('Antwort') ?>
    <? endif ?>

            <? /* student answer */ ?>
            <div id="solution-<?= $exercise->id ?>">
                <? $answer = isset($response) ? $response[0] : $exercise->task['template'] ?>
                <? if ($exercise->getLayout() === 'markup'): ?>
                    <textarea name="answer[0]" class="character_input size-l wysiwyg" data-editor="removePlugins=studip-quote,studip-upload,ImageUpload" rows="20"><?= wysiwygReady($answer) ?></textarea>
                <? elseif ($exercise->getLayout() === 'code'): ?>
                    <textarea name="answer[0]" class="character_input size-l monospace download" rows="20"><?= htmlReady($answer) ?></textarea>

                    <a hidden download="<?= htmlReady($exercise->title) ?>.txt" target="_blank"></a>
                    <?= Studip\Button::create(_('Antwort herunterladen'), 'download', ['class' => 'vips_file_download']) ?>
                    <input hidden class="file_upload inline" type="file">
                    <?= Studip\Button::create(_('Text in das Eingabefeld hochladen'), 'upload', ['class' => 'vips_file_upload']) ?>
                <? else: ?>
                    <textarea name="answer[0]" class="character_input size-l" rows="20"><?= htmlReady($answer) ?></textarea>
                <? endif ?>
            </div>

    <? if ($exercise->task['template'] == ''): ?>
        </label>
    <? else: ?>
            <? /* default answer */ ?>
            <div id="default-<?= $exercise->id ?>">
                <? if ($exercise->getLayout() === 'markup'): ?>
                    <textarea readonly class="size-l wysiwyg" rows="20"><?= wysiwygReady($exercise->task['template']) ?></textarea>
                <? elseif ($exercise->getLayout() === 'code'): ?>
                    <textarea readonly class="size-l monospace" rows="20"><?= htmlReady($exercise->task['template']) ?></textarea>
                <? else: ?>
                    <textarea readonly class="size-l" rows="20"><?= htmlReady($exercise->task['template']) ?></textarea>
                <? endif ?>
            </div>
        </div>
    <? endif ?>
<? endif ?>

<? if ($exercise->options['file_upload']): ?>
    <div class="label-text">
        <? if ($solution && $solution->folder && count($solution->folder->file_refs)): ?>
            <?= _('Hochgeladene Dateien') ?>
        <? else: ?>
            <?= _('Keine Dateien hochgeladen') ?>
        <? endif ?>
        (<?= sprintf(_('max. %g MB pro Datei'), FileManager::getUploadTypeConfig($assignment->range_id)['file_size'] / 1048576) ?>)
    </div>

    <table class="default">
        <? if ($solution && $solution->folder && count($solution->folder->file_refs)): ?>
            <thead>
                <tr>
                    <th style="width: 50%;">
                        <?= _('Name') ?>
                    </th>
                    <th style="width: 10%;">
                        <?= _('Größe') ?>
                    </th>
                    <th style="width: 20%;">
                        <?= _('Autor/-in') ?>
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
                <? foreach ($solution->folder->file_refs as $file_ref): ?>
                    <tr class="dynamic_row">
                        <td>
                            <input type="hidden" name="file_ids[]" value="<?= $file_ref->id ?>">
                            <a href="<?= htmlReady($file_ref->getDownloadURL()) ?>">
                                <?= Icon::create('file')->asImg(['title' => _('Datei herunterladen')]) ?>
                                <?= htmlReady($file_ref->name) ?>
                            </a>
                        </td>
                        <td>
                            <?= sprintf('%.1f KB', $file_ref->file->size / 1024) ?>
                        </td>
                        <td>
                            <?= htmlReady(get_fullname($file_ref->file->user_id, 'no_title')) ?>
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
                    <?= Studip\Button::create(_('Datei als Lösung hochladen'), '', ['class' => 'vips_file_upload', 'data-label' => _('%d Dateien ausgewählt')]) ?>
                    <span class="file_upload_hint" style="display: none;"><?= _('Klicken Sie auf „Speichern“, um die gewählten Dateien hochzuladen.') ?></span>
                    <input class="file_upload attach" style="display: none;" type="file" name="upload[]" multiple>
                </td>
            </tr>
        </tfoot>
    </table>
<? endif ?>
