<? if ($short_urls) : ?>
    <table class="default">
        <thead>
            <tr>
                <th><?= _('Alias') ?></th>
                <th><?= _('Ziel') ?></th>
                <th class="actions"><?= _('Aktionen') ?></th>
            </tr>
        </thead>
        <tbody>
            <? foreach($short_urls as $url) : ?>
                <tr>
                    <td><?= htmlReady($url->alias) ?></td>
                    <td><?= htmlReady($url->url) ?></td>
                    <td class="actions">
                        <?
                        $actions = ActionMenu::get();
                        $actions->addLink(
                            $controller->url_for('u/alias/' . $url->id),
                            _('Bezeichnung ändern'),
                            Icon::create('edit')
                        );
                        $actions->addLink(
                            $controller->url_for('u/delete/' . $url->id),
                            _('Löschen'),
                            Icon::create('trash')
                        );
                        ?>
                        <?= $actions->render() ?>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>
    </table>
<? else : ?>
    <?= MessageBox::info(_('Sie haben noch keine Kurz-URL angelegt.')) ?>
<? endif ?>
