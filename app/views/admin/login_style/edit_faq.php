<form action="<?= $controller->link_for("admin/loginstyle/store_faq", ['id' => $entry->getId()]) ?>"
      method="post"
      enctype="multipart/form-data"
      class="default">

    <label for="title">
        <?= _('Titel') ?>
        <input type="text" name="title" value="<?= htmlReady($entry->title) ?>" required>
    </label>

    <label for="description">
        <?= _('Text') ?>
        <textarea name="description"
                  class="add_toolbar wysiwyg" data-editor="toolbar=minimal"><?= htmlReady($entry->description)?></textarea>
    </label>

    <div data-dialog-button>
        <?= \Studip\Button::create(_("Speichern")) ?>
    </div>

</form>
