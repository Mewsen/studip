<?php
/**
 * @var Admin_RoleController $controller
 * @var Role[] $roles
 * @var array $stats
 */
?>
<table class="default">
    <caption>
        <?= _('Vorhandene Rollen') ?>
    </caption>
    <colgroup>
        <col width="65%">
        <col width="10%">
        <col width="10%">
        <col width="10%">
        <col width="5%">
    </colgroup>
    <thead>
        <tr>
            <th ><?= _('Name') ?></th>
            <th style="text-align: right;">
                <abbr title="<?= _('Direkte Zuweisung') ?>">
                    <?= _('Benutzer explizit') ?>
                </abbr>
            </th>
            <th style="text-align: right;">
                <abbr title="<?= _('Indirekte Zuweisung durch Berechtigungsstufe') ?>">
                    <?= _('Benutzer implizit') ?>
                </abbr>
            </th>
            <th style="text-align: right;"><?= _('Plugins') ?></th>
            <th></th>
        </tr>
        <tr>
            
        </tr>
    </thead>
    <tbody>
    <? foreach ($roles as $role): ?>
        <? $role_id = $role->getRoleid() ?>
        <tr>
            <td>
                <a href="<?= $controller->link_for("admin/role/show_role/{$role_id}") ?>">
                    <?= htmlReady($role->getRolename()) ?>
                <? if ($role->getSystemtype()): ?>
                    [<?= _('Systemrolle') ?>]
                <? endif ?>
                </a>
            </td>
            <td style="text-align: right;">
                <?= $stats[$role_id]['explicit'] ?>
            </td>
            <td style="text-align: right;">
                <?= $stats[$role_id]['implicit'] ?>
            </td>
            <td style="text-align: right;">
                <?= $stats[$role_id]['plugins'] ?>
            </td>
            <td class="actions">
            <? if (!$role->getSystemtype()): ?>
                <a href="<?= $controller->link_for('admin/role/ask_remove_role', $role_id) ?>">
                    <?= Icon::create('trash')->asImg(tooltip2(_('Rolle löschen'))) ?>
                </a>
            <? endif ?>
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>
