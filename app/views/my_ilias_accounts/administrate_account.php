<form class="default" action="<?= $controller->link_for('my_ilias_accounts/administrate_account/' . $user->studip_id . '/' . $ilias_index) ?>" method="post" data-dialog="reload-on-close">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend>
            <?= _('Bestehenden Account zuordnen') ?>
        </legend>
        <? if (!$matched_user) : ?>
            <label>
                <span><?= _('Loginname') ?></span>
                <input type="text" name="ilias_login" size="50" maxlength="50" value="<?= htmlReady($ilias_login) ?>">
                <?= Studip\Button::createAccept(_('Account Suchen'), 'lookup_account') ?>
            </label>
        <? else : ?>
            <label>
                <span><?= htmlReady(sprintf(_('ILIAS Account %s (ID %s)'), $ilias_login, $matched_user)) ?></span>
            </label>
            <input type="hidden" name="ilias_user_id" value="<?= htmlReady($matched_user) ?>">
            <?= Studip\Button::createAccept(_('Account zuordnen'), 'connect_account') ?>
        <? endif ?>
    </fieldset>
    <? if ($user->isConnected()) : ?>
        <fieldset>
            <legend>
                <?= _('Verknüpfter Account') ?>
            </legend>
            <table class="default nohover">
                <tr>
                    <td><?= _('Loginname des verknüpften Accounts:') ?></td>
                    <td>
                        <?= htmlReady($user->getUsername()) ?>
                        <? if (!$user_exists): ?>
                            - <?= _('Der verknüpfte Account wurde im angebundenen ILIAS-System nicht gefunden!') ?>
                        <? endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><?= _('Eigene Kategorie:') ?></td>
                    <td><?= !empty($user->getCategory()) ? _('ID') . ' ' . htmlReady($user->getCategory()) : _('nicht vorhanden') ?></td>
                </tr>
                <tr>
                    <td><?= _('Account-Typ:') ?></td>
                    <td><?= $user->getUserType() == IliasUser::USER_TYPE_ORIGINAL ? _('Lokaler ILIAS-Account') : _('Automatisch erstellter Account') ?></td>
                </tr>
            </table>
            <?= Studip\Button::createCancel(_('Verknüpfung aufheben'), 'disconnect_account') ?>
        </fieldset>
    <? else : ?>
        <fieldset>
            <legend>
                <?= _('Kein Account verknüpft') ?>
            </legend>
            <? if ($external_account_id) : ?>
                <label>
                    <?= sprintf(_('Es existiert bereits ein ILIAS-Account mit dem Loginnamen %s.'), htmlReady($external_account_login)) ?>
                    <input type="hidden" name="ilias_user_id" value="<?= htmlReady($external_account_id) ?>">
                    <?= Studip\Button::createAccept(_('Mit externem Account verknüpfen'), 'connect_account') ?>
                </label>
            <? else : ?>
                <label>
                    <?= Studip\Button::createAccept(_('Neuen Account anlegen'), 'new_account') ?>
                </label>
            <? endif ?>
        </fieldset>
    <? endif ?>
</form>