<?php
/**
 * @var AuthenticatedController $controller
 * @var LtiResourceLink $resourceLink
 * @var LtiToolPrivacySettings $privacySettings
 */
?>

<? if ($resourceLink) : ?>
    <form class="default" method="post" action="<?= $controller->link_for('course/lti/consent/' . $resourceLink->id) ?>">
        <?= CSRFProtection::tokenTag() ?>
        <?
            $dataProtectionWarning = CourseConfig::get(Context::getId())->LTI_DATA_PROTECTION_COURSE_WARNING;
            if (empty($dataProtectionWarning)) {
                $dataProtectionWarning = Config::get()->LTI_DATA_PROTECTION_DEFAULT_WARNING;
            }
        ?>
        <input type="hidden" name="redirect" value="<?= Request::option('redirect') ?>" />
        <fieldset>
            <legend><?= _('Datenschutzhinweise')  ?></legend>
            <section>
                <p><?= htmlReady($dataProtectionWarning) ?></p>
                <? if (isset($resourceLink->deployment->registration->config_values['data_protection_notes'])) : ?>
                    <?= formatReady($resourceLink->deployment->registration->config_values['data_protection_notes']) ?>
                <? endif ?>
            </section>
        </fieldset>
        <fieldset>
            <?
                $optionalFieldList = explode(',', $privacySettings->allowed_optional_fields ?? '');
            ?>
            <legend><?= _('Die folgenden Daten werden übertragen') ?></legend>
            <?= _('Beim Wechsel in das LTI-Tool werden die folgenden personenbezogenen Daten übertragen:') ?>
            <label>
                <input type="checkbox" checked disabled>
                <?= _('Die ID ihres Stud.IP-Kontos') ?>
            </label>
            <label>
                <input type="checkbox" checked disabled>
                <?= _('Ihr Vor- und Nachname, sowie gegebenenfalls vorhandene Titel') ?>
            </label>
            <label>
                <input type="checkbox" checked disabled>
                <?= _('Ihre E-Mail Adresse') ?>
            </label>
            <label>
                <input type="checkbox" name="submit_optional_field[lang]" value="1"
                    <?= in_array('lang', $optionalFieldList) ? 'checked' : '' ?>>
                <?= _('Ihre in Stud.IP eingestellte Sprache') ?>
            </label>
            <label>
                <input type="checkbox" name="submit_optional_field[avatar_url]" value="1"
                    <?= in_array('avatar_url', $optionalFieldList) ? 'checked' : '' ?>>
                <?= _('Ihr Profilbild') ?>
            </label>
        </fieldset>
        <?= $this->render_partial('lti/_link_user_info', ['link' => $resourceLink]) ?>
        <fieldset>
            <legend><?= _('Bestätigung') ?></legend>
            <label>
                <input type="checkbox" name="confirmed" value="1" required>
                <?= _(
                    'Ich habe die Datenschutzhinweise zur Benutzung des LTI-Tools zur Kenntnis genommen und stimme der Weitergabe meiner personenbezogenen Daten zu. '
                    . 'Mir ist bewusst, dass ich ohne die Zustimmung das LTI-Tool nicht nutzen kann.'
                ) ?>
            </label>
            <div data-dialog-button>
                <?= \Studip\Button::createAccept(_('Speichern'), 'save') ?>
                <?= \Studip\Button::createCancel(_('Abbrechen')) ?>
            </div>
        </fieldset>
    </form>
<? endif ?>
