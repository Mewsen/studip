<table class="default">
    <caption><?= _('Hinweise zum Login') ?></caption>
    <thead>
    <tr>
        <th><?= _('Titel') ?></th>
        <th class="actions"><?= _('Löschen') ?></th>
    </tr>
    </thead>
    <tbody>
    <? if (count($faq_entries) > 0) : ?>
        <? foreach ($faq_entries as $entry) : ?>
            <tr>
                <td><a href="<?= $controller->link_for("admin/loginstyle/edit_faq", ['entry_id' => $entry->getId()]) ?>" data-dialog="size=medium"><?= htmlReady($entry->title) ?></a></td>
                <td class="actions">
                    <? $actionmenu = ActionMenu::get() ?>
                    <? $actionmenu->addLink(
                        $controller->url_for("admin/loginstyle/delete_faq/{$entry->getId()}"),
                        _('Hinweistext löschen'),
                        Icon::create('trash'),
                        [
                            'data-confirm' => sprintf(
                                _('Wollen Sie den Hinweistext "%s" wirklich löschen?'),
                                $entry->title),
                            'data-dialog'  => 'size=auto',
                        ]
                    ); ?>
                    <?= $actionmenu->render() ?>
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
