<div role="region" aria-label="<?= $label ?>" aria-describedby="messagebox-<?= $counter ?>" class="messagebox  messagebox_<?= $class ?> <? if (count($details) > 0 && $close_details): ?>details_hidden<? endif; ?>">
    <div class="messagebox-icon">
    </div>
    <div class="messagebox-content" role="status" id="messagebox-<?= $counter ?>">
        <p class="messagebox-message"><?= $message ?></p>
        <? if (count($details) > 0 && $close_details) : ?>
            <button class="messagebox-button messagebox-details-toggle" title="<?=_('Detailanzeige umschalten')?>"><?= _('Details')?></button>
        <? endif ?>
        <? if (count($details) > 0) : ?>
            <div class="messagebox-details">
                <ul>
                    <? foreach ($details as $li) : ?>
                        <li><?= $li ?></li>
                    <? endforeach ?>
                </ul>
            </div>
        <? endif ?>
    </div>
    <? if (!$hide_close): ?>
        <button class="messagebox-button messagebox-close" title="<?= _('Nachrichtenbox schließen') ?>"></button>
    <? endif; ?>
</div>