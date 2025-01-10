<?php
/**
 * @var VipsSolution $solution
 */
?>
<? if ($solution->feedback_folder && count($solution->feedback_folder->file_refs)): ?>
    <div class="label-text">
        <?= _('Dateien zur Korrektur') ?>
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
            <? foreach ($solution->feedback_folder->file_refs as $file_ref): ?>
                <tr>
                    <td>
                        <a href="<?= htmlReady($file_ref->getDownloadURL()) ?>">
                            <?= Icon::create('file')->asImg(['title' => _('Datei herunterladen')]) ?>
                            <?= htmlReady($file_ref->name) ?>
                        </a>
                    </td>
                    <td>
                        <?= relsize($file_ref->file->size) ?>
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
    </table>
<? endif ?>
