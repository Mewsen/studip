<?
/**
 * @var StudipController $controller
 * @var LtiTool[] $global_tools
 */
?>
<form class="default" method="post" action="<?= $controller->link_for('course/lti/select_tool') ?>"
      data-dialog>
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= _('Auswahl des LTI-Tools') ?></legend>
        <label>
            <?= _('Bitte wählen Sie ein LTI-Tool aus.') ?>
            <select name="selected_tool_id">
                <? foreach ($global_tools as $tool) : ?>
                    <option value="<?= htmlReady($tool->id) ?>">
                        <?= htmlReady($tool->name) ?>
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
