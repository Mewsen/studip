<?php
/**
 * @var Vips_AdminController $controller
 * @var VipsBlock $block
 * @var VipsGroup[] $groups
 */
?>
<form class="default" action="<?= $controller->link_for('vips/admin/store_block') ?>" data-secure method="POST">
    <?= CSRFProtection::tokenTag() ?>

    <? if ($block->id): ?>
        <input type="hidden" name="block_id" value="<?= $block->id ?>">
    <? endif ?>

    <label>
        <span class="required"><?= _('Blockname') ?></span>
        <input type="text" name="block_name" required value="<?= htmlReady($block->name) ?>">
    </label>

    <label>
        <?= _('Sichtbarkeit') ?>
        <?= tooltipIcon(_('Blöcke und zugeordnete Aufgabenblätter können nur für bestimmte Gruppen sichtbar oder auch komplett unsichtbar gemacht werden.')) ?>
        <select name="group_id">
            <option value="0">
                <?= _('Alle Teilnehmenden (keine Beschränkung)') ?>
            </option>
            <option value="" <?= !$block->visible ? 'selected' : '' ?>>
                <?= _('Für Teilnehmende unsichtbar') ?>
            </option>
            <? foreach ($groups as $group): ?>
                <option value="<?= $group->id ?>" <?= $block->group_id === $group->id ? 'selected' : '' ?>>
                    <?= sprintf(_('Gruppe „%s“'), htmlReady($group->name)) ?>
                </option>
            <? endforeach ?>
        </select>
    </label>

    <label>
        <input type="checkbox" name="block_grouped" value="1" <?= $block->weight !== null ? 'checked' : '' ?>>
        <?= _('Aufgabenblätter in der Bewertung gruppieren') ?>
        <?= tooltipIcon(_('In der Ergebnisübersicht wird nur der Block anstelle der enthaltenen Aufgabenblätter aufgeführt.')) ?>
    </label>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), 'store_block') ?>
    </footer>
</form>
