<form action="<?= $controller->link_for("admin/mailqueue/delete_entry/" . $queue_id . "/" . $oldornew) ?>"
      class="default" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <label>
        <span><?= _('Möchten Sie den Eintrag aus der Mailqueue löschen?') ?>
        </span>
    </label>
    <div data-dialog-button>
        <?= Studip\Button::create(_('Eintrag löschen')) ?>
    </div>
</form>

