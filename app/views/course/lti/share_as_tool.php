<?
/**
 * @var AuthenticatedController $controller
 * @var string $course_id
 * @var bool $share_as_tool
 * @var array $plugin_data
 * @var int $lti_entry_point
 * @var ?LtiPlatform[] $platforms
 */
?>
<section class="contentbox">
    <header><h1><?= _('Einstellungen') ?></h1></header>
    <section>
        <form class="default" method="post"
              action="<?= $controller->link_for('course/lti/save_share_as_tool_settings', ['cid' => $course_id]) ?>">
            <?= CSRFProtection::tokenTag() ?>
            <?= MessageBox::info(
                $share_as_tool
                    ? _('Die Veranstaltung ist als LTI-Tool freigegeben.')
                    : _('Die Veranstaltung ist nicht als LTI-Tool freigegeben.')
            ) ?>
            <? if ($share_as_tool) : ?>
                <label>
                    <input type="checkbox" name="share_as_tool" value="0">
                    <?= _('Freigabe als LTI-Tool beenden') ?>
                </label>
                <label>
                    <?= _('Einstiegsseite für Personen, die via LTI in die Veranstaltung wechseln:') ?>
                    <select name="lti_entry_point">
                        <option value="" <?= empty($lti_entry_point) ? 'selected' : '' ?>>
                            <?= _('Keine besondere Einstiegsseite') ?>
                        </option>
                        <? foreach ($plugin_data as $plugin_id => $plugin_name) : ?>
                            <option value="<?= htmlReady($plugin_id) ?>" <?= $lti_entry_point === $plugin_id ? 'selected' : '' ?>>
                                <?= htmlReady($plugin_name) ?>
                            </option>
                        <? endforeach ?>
                    </select>
                </label>
            <? else : ?>
                <p><?= _('Sie können die Veranstaltung als LTI-Tool freigeben. Hierzu sind folgende Dinge zu beachten:') ?></p>
                <ul>
                    <li><?= _('Die Teilnehmendenseite wird aus Datenschutzgründen unsichtbar geschaltet.') ?></li>
                    <li><?= _('Über eine angebundene LTI-Plattform können externe Personen auf die Inhalte dieser Veranstaltung zugreifen.') ?></li>
                    <li><?= _('Angebundene LTI-Plattformen haben gegebenenfalls nicht das Datenschutzniveau von Stud.IP.') ?></li>
                    <li><?= 'Weitere Punkte? TODO' ?></li>
                </ul>
                <label>
                    <input type="checkbox" name="share_as_tool" value="1">
                    <?= _('Ich habe die Hinweise zur Kenntnis genommen und möchte die Veranstaltung als LTI-Tool freigeben.') ?>
                </label>
            <? endif ?>
            <?= \Studip\Button::create(_('Speichern'), 'save') ?>
        </form>
    </section>
</section>
<? if ($share_as_tool) : ?>
    <section class="contentbox">
        <header><h1><?= _('Angebundene LTI-Plattformen') ?></h1></header>
        <section>
            <? if ($platforms) : ?>
                <table class="default">
                    <thead>
                        <tr>
                            <th><?= _('Plattform') ?></th>
                            <th><?= _('Verantwortliche Person') ?></th>
                            <th class="actions"><?= _('Aktionen') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach ($platforms as $platform) : ?>
                            <tr>
                                <td>
                                    <a href="<?= htmlReady($platform->url) ?>" target="_blank">
                                        <?= htmlReady($platform->name) ?>
                                        <?= Icon::create('link-extern')->asImg(16, ['class' => 'text-bottom', 'aria-hidden' => 'true']) ?>
                                    </a>
                                </td>
                                <td><?= htmlReady($platform->creator?->getFullName() ?? _('unbekannt')) ?></td>
                                <td class="actions">
                                    <form method="post" action="">
                                        <?= CSRFProtection::tokenTag() ?>
                                        <?
                                        $menu = ActionMenu::get();
                                        if ($platform->creator_id !== $GLOBALS['user']->id) {
                                            $menu->addLink(
                                                $controller->url_for('messages/write', ['rec_uname' => $platform->creator->username]),
                                                _('Nachricht an verantwortliche Person schreiben'),
                                                Icon::create('mail'),
                                                ['data-dialog' => 'size=auto']
                                            );
                                        }
                                        $menu->addLink(
                                            $controller->url_for('course/lti/edit_platform/' . $platform->id),
                                            _('Bearbeiten'),
                                            Icon::create('edit'),
                                            ['data-dialog' => 'reload-on-close']
                                        );
                                        $menu->addButton(
                                            'delete',
                                            _('Löschen'),
                                            Icon::create('trash'),
                                            [
                                                'data-confirm' => studip_interpolate(
                                                    _('Soll die Plattform %{name} wirklich gelöscht werden?'),
                                                    ['name' => $platform->name]
                                                ),
                                                'formaction' => $controller->url_for('course/lti/delete_platform/' . $platform->id)
                                            ]
                                        );
                                        ?>
                                        <?= $menu->render() ?>
                                    </form>
                                </td>
                            </tr>
                        <? endforeach ?>
                    </tbody>
                </table>
            <? else : ?>
                <?= MessageBox::info(_('Es sind keine LTI-Plattformen an diese Veranstaltung angebunden.')) ?>
            <? endif ?>
        </section>
    </section>
<? endif ?>
