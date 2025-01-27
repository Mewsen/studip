<?php
/**
 * @var Vips_SolutionsController $controller
 * @var VipsAssignment $assignment
 * @var VipsGroup $group
 * @var string $view
 * @var VipsGroupMember[] $members
 */
?>
<form class="default" action="<?= $controller->edit_group() ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <input type="hidden" name="assignment_id" value="<?= $assignment->id ?>">
    <input type="hidden" name="group_id" value="<?= htmlReady($group->id) ?>">
    <input type="hidden" name="view" value="<?= htmlReady($view) ?>">

    <div class="description">
        <?= _('Wählen Sie aus, wen Sie aus der Gruppe entfernen möchten:') ?>
    </div>

    <? foreach ($members as $member): ?>
        <label>
            <input type="checkbox" name="user_ids[]" value="<?= $member->user_id ?>">
            <?= htmlReady($member->user->getFullName('no_title_rev')) ?>
        </label>
    <? endforeach ?>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Entfernen'), 'edit') ?>
    </footer>
</form>
