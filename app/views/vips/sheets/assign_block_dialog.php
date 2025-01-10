<?php
/**
 * @var Vips_SheetsController $controller
 * @var int[] $assignment_ids
 * @var VipsBlock[] $blocks
 */
?>
<form class="default" action="<?= $controller->assign_block() ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <? foreach ($assignment_ids as $assignment_id): ?>
        <input type="hidden" name="assignment_ids[]" value="<?= $assignment_id ?>">
    <? endforeach ?>

    <label>
        <?= _('Block auswählen') ?>

        <select name="block_id">
            <option value="0">
                <?= _('Keinem Block zuweisen') ?>
            </option>
            <? foreach ($blocks as $block): ?>
                <option value="<?= $block->id ?>">
                    <?= htmlReady($block->name) ?>
                </option>
            <? endforeach ?>
        </select>
    </label>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Zuweisen'), 'assign_block') ?>
    </footer>
</form>
