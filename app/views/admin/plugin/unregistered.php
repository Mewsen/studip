<?php
/**
 * @var Admin_PluginController $controller
 * @var array $unknown_plugins
 */
?>
<table class="default sortable-table" data-sortlist="[[0, 0]]">
    <caption>
        <?= _('Im Pluginverzeichnis vorhandene Plugins registrieren') ?>
    </caption>
    <thead>
        <tr>
            <th data-sort="text"><?= _('Name') ?></th>
            <th data-sort="text"><?= _('Pluginklasse') ?></th>
            <th data-sort="digit"><?= _('Version') ?></th>
            <th data-sort="text"><?= _('Ursprung') ?></th>
            <th><?= _('Registrieren') ?></th>
        </tr>
    </thead>
    <tbody>
    <? if (!$unknown_plugins): ?>
        <tr>
            <td colspan="5">
                <?= _('Es sind keine nicht registrierten Plugins vorhanden') ?>
            </td>
        </tr>
    <? endif; ?>
    <? foreach ($unknown_plugins as $n => $plugin): ?>
        <tr>
            <td><?= htmlReady($plugin['pluginname']) ?></td>
            <td><?= htmlReady($plugin['pluginclassname']) ?></td>
            <td><?= htmlReady($plugin['version']) ?></td>
            <td><?= htmlReady($plugin['origin']) ?></td>
            <td class="actions">
                <form action="<?= $controller->link_for('admin/plugin/register/' . $n) ?>" method="post">
                    <?= CSRFProtection::tokenTag() ?>
                    <?= Icon::create('install')->asInput([
                        'title' => _('Plugin registrieren'),
                        'class' => 'middle',
                        'name'  => 'install',
                    ]) ?>
                </form>
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>
