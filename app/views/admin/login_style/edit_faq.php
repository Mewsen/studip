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
    <?= CSRFProtection::tokenTag() ?>
    <label>
        <span class="required">
            <?= _('Titel') ?>
        </span>
        <?= I18N::input('title', $entry->title, ['required' => true]) ?>
    </label>
    <label>
        <span class="required">
            <?= _('Text') ?>
        </span>
        <?= I18N::textarea('description',
            $entry->description,
            [
                'class'       => 'wysiwyg',
                'required'    => true,
                'data-editor' => 'toolbar=small'
            ]
        ) ?>
    </label>
    <div data-dialog-button>
        <?= \Studip\Button::create(_('Speichern')) ?>
    </div>

</form>
