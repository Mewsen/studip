<?php
/**
 * @var Course_StatusgroupsController $controller
 * @var MyCoursesSearch $search
 * @var string[] $group_ids
 */
?>
<form action="<?= $controller->do_copy() ?>" method="post" class="default">
    <?= addHiddenFields('group_ids', $group_ids) ?>

    <fieldset>
        <legend><?= _('Gruppen in andere Veranstaltung kopieren') ?></legend>

        <label>
            <?= _('Zielveranstaltung auswählen') ?>
            <?= QuickSearch::get('course_id', $search)
                ->setAttributes(['required' => ''])
                ->setInputStyle('width:100%')
                ->withButton()
                ->render(); ?>
        </label>

        <label>
            <input type="checkbox" name="copy_members" value="1">
            <?= _('Inklusive aller zugeordneten Personen') ?>
        </label>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Kopieren')) ?>
        <?= Studip\LinkButton::createCancel(
            _('Abbrechen'),
            $controller->indexURL()
        ) ?>
    </footer>
</form>
