<?php
/**
 * @var Course_LtiController $controller
 * @var LtiData $lti_data
 * @var LtiTool[] $tools
 */
?>
<form class="default" action="<?= $controller->link_for('course/lti/save', $lti_data->isNew() ? '' : $lti_data->position) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend>
            <?= _('Grunddaten') ?>
        </legend>

        <label>
            <span class="required">
                <?= _('Titel') ?>
            </span>
            <input type="text" name="title" value="<?= htmlReady($lti_data->title) ?>" required>
        </label>

        <label>
            <?= _('Beschreibung') ?>
            <textarea name="description" class="wysiwyg"><?= wysiwygReady($lti_data->description) ?></textarea>
        </label>

    </fieldset>
    <fieldset>
        <legend><?= _('Zugangsdaten') ?></legend>
        <label>
            <?= _('Auswahl des externen Tools') ?>
            <select class="config_tool" name="tool_id">
                <? foreach ($tools as $tool): ?>
                    <option value="<?= htmlReady($tool->id) ?>"
                        <? if ($tool->allow_custom_url): ?>
                            data-url="<?= htmlReady($tool->launch_url) ?>"
                        <? endif ?>
                        <?= !$lti_data->hasOwnTool() && $lti_data->tool_id === $tool->id ? 'selected' : '' ?>>
                        <?= htmlReady($tool->name) ?>
                    </option>
                <? endforeach ?>
                <option value="0" <?= $lti_data->hasOwnTool() ? 'selected' : '' ?>><?= _('Eigenes Tool einrichten') ?></option>
            </select>
        </label>

        <div class="config_custom_url">
            <label>
                <?= _('URL der Anwendung (optional)') ?>
                <?= tooltipIcon(_('Sie können direkt auf eine URL in der Anwendung verlinken.')) ?>
                <input type="text" name="custom_url" value="<?= htmlReady($lti_data->getLaunchURL()) ?>">
            </label>
        </div>

        <div class="config_launch_url">
            <label>
                <?= _('URL der Anwendung') ?>
                <?= tooltipIcon(_('Die Betreiber dieses Tools müssen Ihnen eine URL und Zugangsdaten (Consumer-Key und Consumer-Secret) mitteilen.')) ?>
                <input type="text" name="launch_url" value="<?= htmlReady($lti_data->getLaunchURL()) ?>">
            </label>

            <label>
                <?= _('Consumer-Key des LTI-Tools') ?>
                <input type="text" name="consumer_key" value="<?= htmlReady($lti_data->getConsumerKey()) ?>">
            </label>

            <label>
                <?= _('Consumer-Secret des LTI-Tools') ?>
                <input type="text" name="consumer_secret" value="<?= htmlReady($lti_data->getConsumerSecret()) ?>">
            </label>

            <label>
                <?= _('LTI-Version') ?>
                <select name="lti_version">
                    <option value="1.1" <?= empty($lti_data->tool->lti_version) || $lti_data->tool->lti_version === '1.1' ? 'selected' : '' ?>>
                        1.0/1.1
                    </option>
                    <option value="1.3a" <?= !empty($lti_data->tool->lti_version) && $lti_data->tool->lti_version === '1.3a' ? 'selected' : '' ?>>
                        1.3a
                    </option>
                </select>
            </label>

            <label>
                <?
                $signature_method = $lti_data->getOauthSignatureMethod();
                ?>
                <?= _('OAuth Signatur Methode des LTI-Tools') ?>
                <select name="oauth_signature_method">
                    <option value="sha1">HMAC-SHA1</option>
                    <option value="sha256" <?= $signature_method === 'sha256' ? 'selected' : '' ?>>HMAC-SHA256</option>
                </select>
            </label>

            <label>
                <?= _('Schlüssel des LTI-Tools per URL laden') ?>
                <input type="url" name="keyset_url" value="">
            </label>

            <label>
                <?= _('Öffentlicher Schlüssel des LTI-Tools') ?>
                <?
                $keyring = null;
                if ($lti_data->tool) {
                    $keyring = $lti_data->tool->getKeyring();
                }
                $public_key_string = '';
                if ($keyring) {
                    $keychain = $keyring->toKeyChain();
                    $public_key_string = $keychain->getPublicKey()->getContent();
                }
                ?>
                <textarea name="tool_public_key"><?= htmlReady($public_key_string) ?></textarea>
            </label>

            <label>
                <input type="checkbox" name="send_lis_person" value="1" <?= $lti_data->getSendLisPerson() ? ' checked' : '' ?>>
                <?= _('Nutzerdaten an LTI-Tool senden') ?>
                <?= tooltipIcon(_('Nutzerdaten dürfen nur an das externe Tool gesendet werden, wenn es keine Datenschutzbedenken gibt. Mit Setzen des Hakens bestätigen Sie, dass die Übermittlung der Daten zulässig ist.')) ?>
            </label>
        </div>
    </fieldset>
    <fieldset>
        <legend><?= _('Anzeigeeinstellungen') ?></legend>
        <label>
            <input type="checkbox" name="document_target" value="iframe" <?= isset($lti_data->options['document_target']) && $lti_data->options['document_target'] === 'iframe' ? ' checked' : '' ?>>
            <?= _('Anzeige im IFRAME auf der Seite') ?>
            <?= tooltipIcon(_('Normalerweise wird das externe Tool in einem neuen Fenster angezeigt. Aktivieren Sie diese Option, wenn die Anzeige stattdessen in einem IFRAME erfolgen soll.')) ?>
        </label>

        <label>
            <?= _('Zusätzliche LTI-Parameter') ?>
            <?= tooltipIcon(_('Ein Wert pro Zeile, Beispiel: Review:Chapter=1.2.56')) ?>
            <textarea name="custom_parameters"><?= htmlReady($lti_data->options['custom_parameters'] ?? '') ?></textarea>
        </label>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), 'save') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('course/lti')) ?>
    </footer>
</form>

<script>
    $('.config_tool').change(function() {
        let url = $(this).find(':selected').data('url');

        if ($(this).val() == 0) {
            $('.config_launch_url').show();
        } else {
            $('.config_launch_url').hide();
        }

        if (url) {
            $('.config_custom_url').find('input').attr('placeholder', url);
            $('.config_custom_url').show();
        } else {
            $('.config_custom_url').hide();
        }
    }).trigger('change');
</script>
