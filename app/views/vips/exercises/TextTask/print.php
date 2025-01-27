<?php
/**
 * @var ClozeTask $exercise
 * @var VipsSolution $solution
 * @var array $response
 * @var array $results
 * @var bool $print_correction
 * @var bool $show_solution
 * @var bool $print_files
 */
?>
<? if ($exercise->getLayout() !== 'none'): ?>
    <? if ($print_correction && $solution->commented_solution != '') : ?>
        <div class="label-text">
            <?= _('Kommentierte Lösung:') ?>
        </div>

        <?= formatReady($solution->commented_solution) ?>
    <? elseif ($solution->id && $response[0] != '') : ?>
        <div class="label-text">
            <?= _('Lösung des Teilnehmers:') ?>
        </div>

        <div class="vips_output">
            <? if ($exercise->getLayout() === 'markup'): ?>
                <?= formatReady($response[0]) ?>
            <? elseif ($exercise->getLayout() === 'code'): ?>
                <pre><?= htmlReady($response[0]) ?></pre>
            <? else: ?>
                <?= htmlReady($response[0], true, true) ?>
            <? endif ?>
        </div>
    <? elseif ($print_correction) : ?>
        <div class="description" style="font-style: italic;">
            <?= _('Es wurde kein Text als Lösung abgegeben.') ?>
        </div>
    <? else : ?>
        <div class="vips_output" style="min-height: 30em;">
            <? if ($exercise->getLayout() === 'markup'): ?>
                <?= formatReady($exercise->task['template']) ?>
            <? elseif ($exercise->getLayout() === 'code'): ?>
                <pre><?= htmlReady($exercise->task['template']) ?></pre>
            <? else: ?>
                <?= htmlReady($exercise->task['template'], true, true) ?>
            <? endif ?>
        </div>
    <? endif ?>
<? endif ?>

<? if ($exercise->options['file_upload'] && $solution && $solution->folder && count($solution->folder->file_refs)): ?>
    <? foreach ($solution->folder->file_refs as $file_ref): ?>
        <? if ($print_files && $file_ref->isImage()): ?>
            <div class="label-text">
                <?= htmlReady($file_ref->name) ?>:
            </div>
            <div class="formatted-content">
                <img src="<?= htmlReady($file_ref->getDownloadURL()) ?>">
            </div>
        <? endif ?>
    <? endforeach ?>

    <div class="label-text">
        <?= _('Hochgeladene Dateien:') ?>
    </div>

    <ul>
        <? foreach ($solution->folder->file_refs as $file_ref): ?>
            <li>
                <a href="<?= htmlReady($file_ref->getDownloadURL()) ?>">
                    <?= htmlReady($file_ref->name) ?>
                </a>
            </li>
        <? endforeach ?>
    </ul>
<? endif ?>

<? if ($show_solution && $exercise->task['answers'][0]['text'] != '') : ?>
    <div class="label-text">
        <?= _('Musterlösung:') ?>
    </div>

    <?= formatReady($exercise->task['answers'][0]['text']) ?>
<? endif ?>
