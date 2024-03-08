<?php
/**
 * @var Admin_LtiController $controller
 * @var LtiTool $tool
 */
?>
<form class="default" action="<?= $controller->link_for('admin/lti/save/' . $tool->id) ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend>
            <?= _('Konfiguration des LTI-Tools') ?>
        </legend>
        <label class="studiprequired">
            <span class="textlabel"><?= _('Name') ?></span>
            <span class="asterisk">*</span>
            <input type="text" name="name" value="<?= htmlReady($tool->name) ?>">
        </label>
        <?= $this->render_partial('lti/_tool_form_fields', ['tool' => $tool]) ?>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), 'save') ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'), $controller->url_for('admin/lti')) ?>
    </footer>
</form>
