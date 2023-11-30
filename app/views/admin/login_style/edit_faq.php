<?php
/**
 * @var Admin_LoginStyleController $controller
 * @var LoginFaq $entry
 */
?>
<form action="<?= $controller->store_faq($entry) ?>"
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
        <?= \Studip\Button::create(_('Speichern')) ?>
    </div>

</form>
