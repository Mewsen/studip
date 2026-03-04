<form action="<?= $controller->link_for('admin/sem_classes/create_sem_type') ?>" method="post" class="default">
    <input type="hidden" name="sem_class" value="<?= $sem_class ?>">
    <fieldset>
        <legend>
            <?= _('Veranstaltungstyp anlegen') ?>
        </legend>

        <label>
            <span class="required">
                <?= _("Name") ?>
            </span>
            <?= I18N::input('name', new I18NString('', []), ['required' => true]) ?>
        </label>
    </fieldset>

    <footer data-dialog-button>
        <?= Studip\Button::create(_("Erstellen")) ?>
    </footer>
</form>
