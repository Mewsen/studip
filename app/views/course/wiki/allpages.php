<?php
/**
 * @var WikiPage[] $pages
 * @var Course_WikiController $controller
 */
?>
<form action="<?= $controller->link_for('course/wiki/page_bulk') ?>" method="POST" data-dialog="width=700" class="default">
    <?= CSRFProtection::tokenTag() ?>
    <table class="default sortable-table" data-sortlist="[[0, 0]]">
        <caption>
            <?= _('Alle Seiten des Wikis') ?>
        </caption>
        <thead>
            <tr>
                <th>
                    <input
                        aria-label="<?= _('Alle Seiten auswählen') ?>"
                        type="checkbox"
                        name="all"
                        value="1"
                        data-proxyfor=":checkbox[name^=pages]"
                    >
                </th>
                <th data-sort="text"><?= _('Seitenname') ?></th>
                <th data-sort="digit"><?= _('Änderungen') ?></th>
                <th data-sort="htmldata"><?= _('Letzte Änderung') ?></th>
                <th data-sort="text"><?= _('Zuletzt bearbeitet von') ?></th>
                <th class="actions"><?= _('Aktionen') ?></th>
            </tr>
        </thead>
        <tbody>
            <? foreach ($pages as $page) : ?>
            <? if ($page->isEditable()) : ?>
                <form action="<?= $controller->delete($page) ?>" method="post" id="delete_page">
                    <?= CSRFProtection::tokenTag() ?>
                </form>
            <? endif ?>
            <tr>
                <td>
                    <input
                        aria-label="<?= sprintf(_('Seite "%s" auswählen'), htmlReady($page->name)) ?>"
                        type="checkbox"
                        <?= $page->write_permission === 'dozent' && !$GLOBALS['perm']->have_studip_perm('dozent', Context::getId()) ? 'disabled' : '' ?>
                        name="pages_id[]"
                        value="<?= $page->page_id ?>"
                    />
                </td>
                <td data-text="<?= htmlReady($page->name) ?>">
                    <a href="<?= $controller->page($page) ?>">
                        <?= htmlReady($page->name) ?>
                    </a>
                </td>
                <td><?= count($page->versions) + 1 ?></td>
                <td data-sort-value="<?= $page->chdate ?>">
                    <?= $page->chdate > 0 ? date('d.m.Y H:i:s', $page->chdate) : _('unbekannt') ?>
                </td>
                <td data-text="<?= htmlReady($page->user ? $page->user->getFullName() : _('unbekannt')) ?>">
                    <?= Avatar::getAvatar($page->user_id)->getImageTag(Avatar::SMALL) ?>
                    <?= htmlReady($page->user ? $page->user->getFullName() : _('unbekannt')) ?>
                </td>
                <td class="actions">
                    <?= $controller->getActionMenu($page, 'allpages') ?>
                </td>
            </tr>
            <? endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">
                    <select name="action" id="bulk_action" aria-label="<?= _('Aktion auswählen') ?>" required>
                        <option value="">- <?= _('Aktion auswählen') ?></option>
                        <option value="page_setting"><?= _('Seiteneinstellungen') ?></option>
                    </select>
                    <?= \Studip\Button::create(_('Ausführen'), 'render_form') ?>
                </td>
            </tr>
        </tfoot>
    </table>
</form>
