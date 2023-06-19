<form name="item_form" action="<?= URLHelper::getLink($action_url) ?>" method="POST" class="default" style="width: 90%; margin: auto;">
    <?= CSRFProtection::tokenTag(); ?>
    <input type="hidden" name="parent_id" value="<?= $study_area->parent_id ?>">

    <table style="width: 100%"><?= $message ?></table>

    <fieldset>
        <legend><?= _('Bereich editieren') ?></legend>

        <label>
            <?= _('Name des Elements') ?>
            <?= I18N::input(
                'edit_name',
                $study_area->name,
                $study_area->studip_object_id ? ['disabled' => ''] : []
            ) ?>
        </label>

    <? if (count($GLOBALS['SEM_TREE_TYPES']) > 1) : ?>
        <label>
            <?= _('Typ des Elements') ?>
            <select name="edit_type">
            <? foreach ($sem_tree_types as $sem_tree_type_key => $sem_tree_type): ?>
                <option value="<?= htmlReady($sem_tree_type_key) ?>" <? if ($sem_tree_type_key == $study_area->type) echo 'selected'; ?>>
                    <?= htmlReady($sem_tree_type['name'] ?: $sem_tree_type_key) ?>
                </option>
            <? endforeach; ?>
            </select>
        </label>
    <? else : # Auswahl ausblenden, wenn nur ein Typ vorhanden ?>
        <input type='hidden' name='edit_type' value='0'>
    <? endif ?>

        <label>
            <?= _('Infotext:') ?>
            <?= I18N::textarea('edit_info', $study_area->info, ['wrap' => 'virtual', 'rows' => 5]) ?>
        </label>
    </fieldset>

    <footer>
        <?= Studip\Button::createAccept(_('Absenden'), ['title' => _('Einstellungen ÃŒbernehmen')]) ?>
        <?= Studip\LinkButton::createCancel(_('Abbrechen'),
            URLHelper::getURL($cancel_url),
            ['title' => _('Aktion abbrechen')])
        ?>
    </footer>
</form>
