<?php
/**
 * @var Course_LtiController $controller
 * @var LtiDeployment $lti_data
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
                        <?= !$lti_data->hasOwnTool() && $lti_data->tool_id === $tool->id ? 'selected' : '' ?>>
                        <?= htmlReady($tool->name) ?>
                    </option>
                <? endforeach ?>
                <option value="" <?= $lti_data->hasOwnTool() ? 'selected' : '' ?>><?= _('Eigenes LTI-Tool einrichten') ?></option>
            </select>
        </label>
    </fieldset>
    <fieldset>
        <legend><?= _('Konfiguration des LTI-Tools') ?></legend>
        <div class="global-tool-settings">
            <label>
                <?= _('Angepasste URL des LTI-Tools') ?>
                <?= tooltipIcon(_('Sie können direkt auf eine URL im LTI-Tool verlinken.')) ?>
                <input type="text" name="custom_url" value="<?= htmlReady($lti_data->getLaunchURL()) ?>">
            </label>
        </div>

        <div class="custom-tool-config">
            <?= $this->render_partial(
                'lti/_tool_form_fields',
                [
                    'tool'              => $lti_data->tool,
                    'custom_launch_url' => $lti_data->getLaunchURL()
                ]
            ) ?>
        </div>
    </fieldset>
    <fieldset>
        <legend><?= _('Anzeigeeinstellungen') ?></legend>
        <label>
            <input type="checkbox" name="document_target" value="iframe" <?= isset($lti_data->options['document_target']) && $lti_data->options['document_target'] === 'iframe' ? ' checked' : '' ?>>
            <?= _('Anzeige im IFRAME auf der Seite') ?>
            <?= tooltipIcon(_('Normalerweise wird das externe Tool in einem neuen Fenster angezeigt. Aktivieren Sie diese Option, wenn die Anzeige stattdessen in einem IFRAME erfolgen soll.')) ?>
        </label>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), 'save') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('course/lti')) ?>
    </footer>
</form>
