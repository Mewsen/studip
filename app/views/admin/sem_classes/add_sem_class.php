<form action="<?= $controller->link_for('admin/sem_classes/create_sem_class') ?>" method="post" class="default">
    <fieldset>
        <legend>
            <?= _('Veranstaltungskategorie anlegen') ?>
        </legend>

        <label>
            <span class="required">
                <?= _("Name") ?>
            </span>
            <?= I18N::input('add_name', new I18NString('', []), ['required' => true]) ?>
        </label>

        <label>
            <?= _("Attribute kopieren von Veranstaltungskategorie") ?>
            <select name="add_like">
                <option value=""><?= _("keine") ?></option>
                <? foreach ($GLOBALS['SEM_CLASS'] as $id => $sem_class) : ?>
                    <option value="<?= $id ?>"><?= htmlReady($sem_class['name']) ?></option>
                <? endforeach ?>
            </select>
        </label>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::create(_("Erstellen")) ?>
    </footer>
</form>
