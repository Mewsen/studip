<?php
/**
 * @var Exercise $exercise
 */
?>
<? if ($exercise->folder && count($exercise->folder->file_refs) > 0 && !$exercise->options['files_hidden']): ?>
    <div class="label-text">
        <?= _('Dateien zur Aufgabe:') ?>
    </div>

    <ul>
        <? foreach ($exercise->folder->file_refs as $file_ref): ?>
            <li>
                <a href="<?= htmlReady($file_ref->getDownloadURL()) ?>" <?= $file_ref->getContentDisposition() === 'inline' ? 'target="_blank"' : '' ?>>
                    <?= htmlReady($file_ref->name) ?>
                </a>
            </li>
        <? endforeach ?>
    </ul>
<? endif ?>
