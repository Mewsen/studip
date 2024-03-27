<?php
/**
 * @var Course_LtiController $controller
 * @var LtiDeployment $deployment
 * @var LtiTool[] $tools
 */
?>
<form class="default" action="<?= $controller->link_for('course/lti/save', $deployment->isNew() ? '' : $deployment->position) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend>
            <?= _('Grunddaten') ?>
        </legend>

        <label>
            <span class="required">
                <?= _('Titel') ?>
            </span>
            <input type="text" name="title" value="<?= htmlReady($deployment->title) ?>" required>
        </label>

        <label>
            <?= _('Beschreibung') ?>
            <textarea name="description" class="wysiwyg"><?= wysiwygReady($deployment->description) ?></textarea>
        </label>

    </fieldset>
    <fieldset>
        <legend><?= _('LTI-Tool') ?></legend>
        <label>
            <?= _('LTI-Tool auswählen') ?>
            <select class="config_tool" name="tool_id"
                    data-shows=".custom-tool-config"
                    data-hides=".global-tool-settings"
                    data-triggering-value="">
                <? foreach ($tools as $tool): ?>
                    <option value="<?= htmlReady($tool->id) ?>"
                        <? if ($tool->allow_custom_url): ?>
                            data-url="<?= htmlReady($tool->launch_url) ?>"
                        <? endif ?>
                        <?= !$deployment->hasOwnTool() && $deployment->tool_id === $tool->id ? 'selected' : '' ?>>
                        <?= htmlReady($tool->name) ?>
                    </option>
                <? endforeach ?>
                <option value="" <?= $deployment->hasOwnTool() ? 'selected' : '' ?>><?= _('Eigenes LTI-Tool einrichten') ?></option>
            </select>
        </label>
        <label class="custom-tool-config">
            <input type="radio" name="config_type" value="automatic" <?= empty($config_type) || $config_type === 'automatic' ? 'checked' : '' ?>
                   data-shows=".automatic-tool-config" data-hides=".manual-tool-config">
            <?= _('LTI-Tool automatisch konfigurieren') ?>
        </label>
        <label class="custom-tool-config">
            <input type="radio" name="config_type" value="manual" <?= !empty($config_type) && $config_type === 'manual' ? 'checked' : '' ?>
                   data-shows=".manual-tool-config" data-hides=".automatic-tool-config">
            <?= _('LTI-Tool manuell konfigurieren') ?>
        </label>
    </fieldset>
    <fieldset>
        <legend><?= _('Konfiguration des LTI-Tools') ?></legend>
        <div class="global-tool-settings">
            <label>
                <?= _('Angepasste URL des LTI-Tools') ?>
                <?= tooltipIcon(_('Sie können direkt auf eine URL im LTI-Tool verlinken.')) ?>
                <input type="text" name="custom_url" value="<?= htmlReady($deployment->tool->launch_url ?? '') ?>">
            </label>
        </div>
        <div class="tool-config">
            <?= $this->render_partial(
                'lti/_tool_form_fields',
                [
                    'tool'              => $deployment->tool,
                    'custom_launch_url' => $deployment->tool->launch_url ?? ''
                ]
            ) ?>
        </div>
    </fieldset>
    <fieldset>
        <legend><?= _('Anzeigeeinstellungen') ?></legend>
        <label>
            <input type="checkbox" name="document_target" value="iframe" <?= isset($deployment->options['document_target']) && $deployment->options['document_target'] === 'iframe' ? ' checked' : '' ?>>
            <?= _('Anzeige im IFRAME auf der Seite') ?>
            <?= tooltipIcon(_('Normalerweise wird das externe Tool in einem neuen Fenster angezeigt. Aktivieren Sie diese Option, wenn die Anzeige stattdessen in einem IFRAME erfolgen soll.')) ?>
        </label>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), 'save') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('course/lti')) ?>
    </footer>
</form>
