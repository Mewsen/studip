<div class="contact-legend">
    <?= _('Bedienung:') ?>
    <ul>
        <li>
            <?= Icon::create('mail') ?>
            <?= _('Nachricht an Kontakt') ?>
        </li>
    <? if ($open): ?>
        <li>
            <?= Icon::create('arr_1up') ?>
            <?= _('Kontakt zuklappen') ?>
        </li>
        <li>
            <?= Icon::create('person') ?>
            <?= _('Buddystatus') ?>
        </li>
        <li>
            <?= Icon::create('edit') ?>
            <?= _('Eigene Rubriken') ?>
        </li>
        <li>
            <?= Icon::create('trash') ?>
            <?= _('Kontakt löschen') ?>
        </li>
    <? else: ?>
        <li>
            <?= Icon::create('arr_1down') ?>
            <?= _('Kontakt aufklappen') ?>
        </li>
    <? endif; ?>

    <? if ($open || $contact['view'] === 'gruppen'): ?>
        <li>
            <?= Icon::create('export') ?>
            <?= _('als vCard exportieren') ?>
        </li>
    <? endif; ?>
    </ul>
</div>
