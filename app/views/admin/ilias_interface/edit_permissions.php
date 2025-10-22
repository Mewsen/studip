<?php
/**
 * @var Admin_IliasInterfaceController $controller
 * @var string $ilias_index
 * @var array $ilias_config
 */
?>
<form class="default" action="<?= $controller->url_for('admin/ilias_interface/save/'.$ilias_index) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend>
            <?= _('Anlegen von Inhalten') ?>
        </legend>
        <label>
            <span class="required"><?= _('Name oder ID des Rollen-Templates zum Erstellen von Lernobjekten') ?></span>
            <input type="text" name="ilias_author_role_name" size="50" maxlength="255" value="<?= !empty($ilias_config['author_role']) ? htmlReady($ilias_config['author_role_name']) : 'Author' ?>" required>
        </label>
        <label>
            <span class="required"><?= _('Erforderliche Rechtestufe zum Erstellen von Lernobjekten') ?></span>
            <select name="ilias_author_perm">
                <option value="autor" <?= $ilias_config['author_perm'] == 'autor' ? 'selected' : '' ?>><?= _('autor') ?></option>
                <option value="tutor" <?= $ilias_config['author_perm'] == 'tutor' ? 'selected' : '' ?>><?= _('tutor') ?></option>
                <option value="dozent" <?= (($ilias_config['author_perm'] == 'dozent') OR ! $ilias_config['author_perm']) ? 'selected' : '' ?>><?= _('dozent') ?></option>
                <option value="admin" <?= $ilias_config['author_perm'] == 'admin' ? 'selected' : '' ?>><?= _('admin') ?></option>
                <option value="root" <?= $ilias_config['author_perm'] == 'root' ? 'selected' : '' ?>><?= _('root') ?></option>
            </select>
        </label>
        <label>
            <input type="checkbox" name="ilias_allow_change_account" value="1" <?= $ilias_config['allow_change_account'] ? 'checked' : '' ?>>
            <span><?= _('Stud.IP-User können sich bestehende ILIAS-Accounts manuell zuordnen') ?></span>
        </label>
    </fieldset>
    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), 'submit') ?>
        <?= Studip\Button::createCancel(_('Abbrechen'), 'cancel', ['data-dialog' => 'close']) ?>
    </footer>
    <fieldset>
        <legend>
            <?= _('Rollenzuweisungen') ?>
        </legend>
        <? if (array_key_exists('additional_roles', $ilias_config) && is_array($ilias_config['additional_roles']) && is_array($global_roles)) : ?>
            <? foreach ($ilias_config['additional_roles'] as $studip_role => $ilias_roles) : ?>
                <? if (count($ilias_roles) > 0) : ?>
                    <div id="ilias_studip_role_<?=htmlReady($studip_role)?>"><?= sprintf(_('Rechtestufe %s erhält zusätzliche globale Rolle(n):'), htmlReady($studip_role)) ?>
                    <ul>
                        <? foreach ($ilias_roles as $role_data) : ?>
                            <li><?= htmlReady(sprintf(_('%s (ID %s)'), $role_data['name'], $role_data['id'])) ?>
                            <?= Icon::create('trash')->asInput([
                                'class' => 'text-bottom',
                                'title' => _('Diese Rollenzuweisung entfernen'),
                                'data-confirm' => _('Sind Sie sicher, dass Sie diese ILIAS-Rollenzuweisung entfernen wollen?'),
                                'formaction' => $controller->url_for(
                                    'admin/ilias_interface/save/'.$ilias_index,
                                    [
                                        'remove_additional_role' => $role_data['id'],
                                        'studip_role' => $studip_role,
                                    ]
                                )
                            ])?></li>
                        <? endforeach ?>
                    </ul></div>
                    <br>
                <? endif ?>
            <? endforeach ?>
        <? endif ?>
        <? if (is_array($global_roles) && is_array($studip_roles)) : ?>
            <section>
                <span><?= _('Stud.IP-Rechtestufe') ?></span>
                <label>
                <select name="add_studip_role" aria-label="<?= _('Stud.IP-Rechtestufe')?>">
                    <option>-- <?= _('Bitte auswählen')?> --</option>
                    <? foreach ($studip_roles as $studip_role) : ?>
                        <option><?= htmlReady($studip_role) ?></option>
                    <? endforeach ?>
                    </select>
                </label>
                <span><?= _('ILIAS-Rolle') ?></span>
                <label>
                    <select name="add_ilias_role" aria-label="<?= _('ILIAS-Rolle')?>">
                    <option>-- <?=_('Bitte auswählen') ?> --</option>
                    <? foreach ($global_roles as $role_data) : ?>
                        <option value="<?= htmlReady($role_data['id']) ?>">
                            <?= htmlReady(sprintf(_('%s (ID %s)'), $role_data['name'], $role_data['id'])) ?>
                        </option>
                    <? endforeach ?>
                    </select>
                </label>
                <?= Studip\Button::create(_('Zusätzliche Rolle zuweisen und speichern'), 'add_additional_role') ?>
            </section>
        <? endif ?>
    </fieldset>
</form>
