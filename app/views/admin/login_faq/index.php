<?php

?>

<table class="default">
    <caption><?= _('Hilfetexte zum Login') ?></caption>
    <thead>
    <tr>
        <th><?= _('Titel') ?></th>
        <th><?= _('Text') ?></th>

        <th class="actions"><?= _('Aktionen') ?></th>
    </tr>
    </thead>
    <tbody>
        <? if (count($faq_entries) > 0) : ?>
        <? foreach ($faq_entries as $entry) : ?>
        <tr>
            <td><?= htmlReady($entry->title) ?></td>
            <td><?= htmlReady($entry->description) ?></td>
            <td class="actions"></td>
        </tr>
        <? endforeach ?>
    <? else : ?>
            <tr>
                <td colspan="3" style="text-align: center">
                    <?=_('Keine Hilfetexte vorhanden')?>
                </td>
            </tr>
    <? endif ?>
    </tbody>

</table>
