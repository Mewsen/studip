<?php
/**
 * @var VipsSolution $solution
 */
?>
<? if ($solution->feedback_folder && count($solution->feedback_folder->file_refs) > 0): ?>
    <div class="label-text">
        <?= _('Dateien zur Korrektur') ?>
    </div>

    <ul>
        <? foreach ($solution->feedback_folder->file_refs as $file_ref): ?>
            <li>
                <a href="<?= htmlReady($file_ref->getDownloadURL()) ?>">
                    <?= htmlReady($file_ref->name) ?>
                </a>
            </li>
        <? endforeach ?>
    </ul>
<? endif ?>
