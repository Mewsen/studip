<?php
/**
 * @var Exercise $exercise
 * @var VipsSolution $solution
 * @var array $results
 * @var array $response
 * @var bool $show_solution
 * @var bool $edit_solution
 */
?>
<? if ($exercise->getLayout() !== 'none' && $response[0] != ''): ?>
    <div class="vips_tabs <?= $solution->commented_solution ? '' : 'edit-hidden' ?>">
        <ul>
            <li class="edit-tab">
                <a href="#commented-<?= $exercise->id ?>">
                    <?= _('Kommentierte Lösung') ?>
                </a>
            </li>
            <li>
                <a href="#solution-<?= $exercise->id ?>">
                    <?= _('Lösung') ?>
                </a>
            </li>
            <? if ($exercise->task['template'] != ''): ?>
                <li>
                    <a href="#default-<?= $exercise->id ?>">
                        <?= _('Vorbelegung') ?>
                    </a>
                </li>
            <? endif ?>
        </ul>

        <div id="commented-<?= $exercise->id ?>">
            <? if ($edit_solution): ?>
                <? if ($exercise->getLayout() === 'markup'): ?>
                    <? $answer = $response[0] ?>
                <? elseif ($exercise->getLayout() === 'code'): ?>
                    <? $answer = "[pre][nop]\n{$response[0]}\n[/nop][/pre]" ?>
                <? elseif (Studip\Markup::editorEnabled()): ?>
                    <? $answer = Studip\Markup::markAsHtml(htmlReady($response[0], true, true)) ?>
                <? else: ?>
                    <? $answer = $response[0] ?>
                <? endif ?>
                <textarea <?= $solution->commented_solution ? 'name="commented_solution"' : '' ?> class="character_input size-l wysiwyg" rows="20"
                ><?= wysiwygReady($solution->commented_solution ?: $answer) ?></textarea>

                <?= Studip\Button::create(_('Kommentierte Lösung löschen'), 'delete_commented_solution', ['data-confirm' => _('Wollen Sie die kommentierte Lösung löschen?')]) ?>

                <? if ($solution->commented_solution): ?>
                    <? if (!Studip\Markup::editorEnabled()): ?>
                        <div class="label-text">
                            <?= _('Textvorschau') ?>
                        </div>

                        <div class="vips_output">
                            <?= formatReady($solution->commented_solution) ?>
                        </div>
                    <? endif ?>
                <? endif ?>
            <? else: ?>
                <div class="vips_output">
                    <?= formatReady($solution->commented_solution) ?>
                </div>
            <? endif ?>
        </div>

        <div id="solution-<?= $exercise->id ?>">
            <div class="vips_output">
                <? if ($exercise->getLayout() === 'text'): ?>
                    <?= htmlReady($response[0], true, true) ?>
                <? elseif ($exercise->getLayout() === 'markup'): ?>
                    <?= formatReady($response[0]) ?>
                <? elseif ($exercise->getLayout() === 'code'): ?>
                    <pre><?= htmlReady($response[0]) ?></pre>
                    <input type="hidden" class="download" value="<?= htmlReady($response[0]) ?>">
                <? endif ?>
            </div>

            <? if ($edit_solution): ?>
                <?= Studip\Button::create(_('Lösung bearbeiten'), 'edit_solution', ['class' => 'edit_solution']) ?>

                <? if ($exercise->getLayout() === 'code'): ?>
                    <a hidden download="<?= htmlReady($exercise->title) ?>.txt" target="_blank"></a>
                    <?= Studip\Button::create(_('Lösung herunterladen'), 'download', ['class' => 'vips_file_download']) ?>
                <? endif ?>
            <? endif ?>
        </div>

        <? if ($exercise->task['template'] != ''): ?>
            <div id="default-<?= $exercise->id ?>">
                <div class="vips_output">
                    <? if ($exercise->getLayout() === 'text'): ?>
                        <?= htmlReady($exercise->task['template'], true, true) ?>
                    <? elseif ($exercise->getLayout() === 'markup'): ?>
                        <?= formatReady($exercise->task['template']) ?>
                    <? elseif ($exercise->getLayout() === 'code'): ?>
                        <pre><?= htmlReady($exercise->task['template']) ?></pre>
                    <? endif ?>
                </div>
            </div>
        <? endif ?>
    </div>
<? elseif ($exercise->getLayout() !== 'none'): ?>
    <div class="description" style="font-style: italic;">
        <?= _('Es wurde kein Text als Lösung abgegeben.') ?>
    </div>
<? endif ?>

<? if ($exercise->options['file_upload'] && $solution->folder && count($solution->folder->file_refs)): ?>
    <? foreach ($solution->folder->file_refs as $file_ref): ?>
        <? if ($file_ref->isImage()): ?>
            <div class="label-text">
                <?= htmlReady($file_ref->name) ?>:
            </div>
            <div class="formatted-content">
                <img src="<?= htmlReady($file_ref->getDownloadURL()) ?>">
            </div>
        <? endif ?>
    <? endforeach ?>

    <div class="label-text">
        <?= _('Hochgeladene Dateien') ?>
    </div>

    <table class="default">
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
                <th style="width: 20%;">
                    <?= _('Datum') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($solution->folder->file_refs as $file_ref): ?>
                <tr>
                    <td>
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
                </tr>
            <? endforeach ?>
        </tbody>

        <? if ($solution->folder && count($solution->folder->file_refs) > 1): ?>
            <tfoot>
                <tr>
                    <td colspan="4">
                        <?= Studip\LinkButton::create(_('Alle Dateien herunterladen'), $controller->url_for('file/download_folder', $solution->folder->id)) ?>
                    </td>
                </tr>
            </tfoot>
        <? endif ?>
    </table>
<? endif ?>

<? if ($show_solution && $exercise->task['answers'][0]['text'] != ''): ?>
    <div class="label-text">
        <?= _('Musterlösung') ?>
    </div>
    <div class="vips_output">
        <?= formatReady($exercise->task['answers'][0]['text']) ?>
    </div>
<? endif ?>
