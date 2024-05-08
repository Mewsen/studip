<table class="default">
    <caption>
        <?= sprintf(_('Treffer für Suche nach <em>%s</em> auf Seite %s'), htmlReady(Request::get('search')), htmlReady($page->name)) ?>
    </caption>
    <thead>
    <tr>
        <th><?= _('Seite') ?></th>
        <th><?= _('Treffer') ?></th>
        <th><?= _('Datum') ?></th>
    </tr>
    </thead>
    <tbody>
    <? $pos_name = mb_stripos($page->name, Request::get('search')) ?>
    <? $pos_content = mb_stripos($page->content, Request::get('search')) ?>
    <? if ($pos_name !== false || $pos_content !== false) : ?>
        <tr>
            <td>
                <a href="<?= $controller->page($page) ?>">
                    <?= htmlReady($page->name) ?>
                </a>
            </td>
            <td>
                <?= $controller->findTextualHits($page->content, Request::get('search'), 200) ?>
            </td>
            <td>
                <?= $page->chdate > 0 ? date('d.m.Y H:i:s', $page->chdate) : _('unbekannt') ?>
                (<?= _('Version').' '.htmlReady($page->versionnumber) ?>)
            </td>
        </tr>
    <? endif ?>
    <? foreach ($versions as $version) : ?>
        <tr>
            <td>
                <a href="<?= $controller->version($version) ?>">
                    <?= htmlReady($version->name) ?>
                </a>
            </td>
            <td>
                <?= $controller->findTextualHits($version->content, Request::get('search'), 200) ?>
            </td>
            <td>
                <?= $version->mkdate > 0 ? date('d.m.Y H:i:s', $version->mkdate) : _('unbekannt') ?>
                (<?= _('Version').' '.htmlReady($version->versionnumber) ?>)
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>
