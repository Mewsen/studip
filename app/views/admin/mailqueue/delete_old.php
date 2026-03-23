<form action="<?= $controller->link_for("admin/mailqueue/delete_old/") ?>"
      class="default" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <label>
        <span><?= _('Möchten Sie alle Einträge aus der Mailqueue löschen, die mindestens ein Jahr alt sind?') ?>
        </span>
    </label>
    <div data-dialog-button>
        <?= Studip\Button::create(_('Einträge löschen')) ?>
    </div>
</form>

