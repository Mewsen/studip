<?php
/**
 * @var User $user
 * @var array $logs
 */
?>
<table class="default" style="min-width: 960px;">
    <caption>
        <?= sprintf(_('Abgabeprotokoll für %s, %s (%s)'), $user->nachname, $user->vorname, $user->username) ?>
    </caption>

    <thead>
        <tr>
            <th>
                <?= _('Ereignis') ?>
            </th>
            <th>
                <?= _('Zeit') ?>
            </th>
            <th>
                <?= _('IP-Adresse') ?>
            </th>
            <th>
                <?= _('Rechnername') ?>
            </th>
            <th>
                <?= _('Sitzungs-ID') ?>
                <?= tooltipIcon(_('Die Sitzungs-ID wird beim Login in Stud.IP vergeben und bleibt bis zum Abmelden gültig.')) ?>
            </th>
        </tr>
    </thead>

    <tbody>
        <? foreach ($logs as $log): ?>
            <tr>
                <td class="<?= $log['archived'] ? 'quiet' : '' ?>">
                    <?= htmlReady($log['label']) ?>
                </td>
                <td>
                    <?= date('d.m.Y, H:i:s', strtotime($log['time'])) ?>
                </td>
                <td>
                    <?= htmlReady($log['ip_address']) ?>
                </td>
                <td>
                    <? if ($log['ip_address']): ?>
                        <?= htmlReady($controller->gethostbyaddr($log['ip_address'])) ?>
                    <? endif ?>
                </td>
                <td>
                    <?= htmlReady($log['session_id']) ?>
                </td>
            </tr>
        <? endforeach ?>
    </tbody>
</table>
