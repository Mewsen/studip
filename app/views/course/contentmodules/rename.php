<?php
/**
 * @var Course_ContentmodulesController $controller
 * @var StudipModule $module
 * @var string $original_name
 * @var array $metadata
 * @var ToolActivation $tool
 */
?>
<form class="default"
      action="<?= $controller->link_for('course/contentmodules/rename/' . $module->getPluginId()) ?>"
      method="post">
    <fieldset>

        <label>
            <?= _('Neuer Name des Werkzeugs') ?>
            <input type="text"
                   name="displayname"
                   value="<?= htmlReady($tool['metadata']['displayname'] ?? '') ?>"
                   placeholder="<?= htmlReady($original_name) ?>">
        </label>

        <div>
            <?= htmlReady(sprintf(_('Ursprünglicher Werkzeugname ist "%s".'), $original_name)) ?>
        </div>
    </fieldset>
    <div data-dialog-button>
        <?= \Studip\Button::create(_('Speichern'))?>
        <? if (!empty($tool['metadata']['displayname'])) : ?>
            <?= \Studip\Button::create(_('Namen löschen'), 'delete') ?>
        <? endif ?>
    </div>
</form>
