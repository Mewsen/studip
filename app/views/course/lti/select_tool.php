<?php
/**
 * @var StudipController $controller
 * @var Lti\Deployment[] $global_tool_deployments
 */

?>
<form class="default" method="post" action="<?= $controller->link_for('course/lti/select_tool_redirect') ?>"
      data-dialog>
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= _('Auswahl des LTI-Tools') ?></legend>
        <label>
            <?= _('Bitte wählen Sie ein LTI-Tool aus.') ?>
            <select name="selected_deployment_id">
                <? foreach ($global_tool_deployments as $deployment) : ?>
                    <option value="<?= htmlReady($deployment->id) ?>">
                        <? if ($deployment->name) : ?>
                            <?= htmlReady(sprintf('%1$s (%2$s)', $deployment->registration->name, $deployment->name)) ?>
                        <? else : ?>
                            <?= htmlReady($deployment->registration->name) ?>
                        <? endif ?>
                    </option>
                <? endforeach ?>
                <? if (Config::get()->LTI_ALLOW_TOOL_CONFIG_IN_COURSE) : ?>
                    <option value="new">
                        <?= _('Neues LTI-Tool für die Veranstaltung einrichten.') ?>
                    </option>
                <? endif ?>
            </select>
        </label>
    </fieldset>
    <div data-dialog-button>
        <?= \Studip\Button::create(_('Weiter')) ?>
    </div>
</form>
