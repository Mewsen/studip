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
            <td><?= formatReady($entry->description) ?></td>
            <td class="actions">
                <a href="<?= $controller->url_for("admin/login_faq/edit", ['entry_id' => $entry->getId()] ) ?>" data-dialog>
                    <?= Icon::create("edit")->asImg(20) ?>
                </a>
                <form action="<?= $controller->url_for("admin/login_faq/delete/" . $entry->getId()) ?>"
                      method="post"
                      data-confirm="<?= _("Wirklich löschen?") ?>"
                      class="inline">
                    <?= Icon::create("trash")->asInput(20) ?>
                </form>
            </td>
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
