<fieldset>
    <legend>
        <?= _('Ordnereigenschaften') ?>
    </legend>
    <label>
        <?= _('Name') ?>
        <input id="edit_folder_name" type="text" name="name" placeholder="<?= _('Name') ?>" value="<?= htmlReady($name) ?>">
    </label>
    <label>
        <?= _('Beschreibung') ?>
        <textarea name="description" class="wysiwyg" placeholder="<?= _('Optionale Beschreibung') ?>"><?= htmlReady($description); ?></textarea>
    </label>
</fieldset>

<? if (!is_a($folder, 'VirtualFolderType')): ?>
    <fieldset class="select_terms_of_use">
        <legend>
            <?= _('Ordnertyp auswählen') ?>
        </legend>
        <? foreach ($folder_types as $folder_type) : ?>
        <input type="radio" name="folder_type"
               value="<?= htmlReady($folder_type['class']) ?>"
               id="folder-type-<?= htmlReady($folder_type['class']) ?>"
               <? if ($folder_type['class'] === get_class($folder)) echo 'checked'; ?>>
        <label for="folder-type-<?= htmlReady($folder_type['class']) ?>">
            <?= Icon::create('radiobutton-unchecked')->asImg(24, ['class' => 'arrow']) ?>
            <?= Icon::create('radiobutton-checked')->asImg(24, ['class' => 'check']) ?>
            <div class="text">
                <?= htmlReady($folder_type['name']) ?>
            <? if ($template = $folder_type['instance']->getDescriptionTemplate()): ?>
                <?= tooltipIcon($template instanceof Flexi_Template ? $template->render() : $template, false, true) ?>
            <? endif ?>
            </div>
            <div class="icon">
                <? if ($folder_type['icon']) : ?>
                    <?= $folder_type['icon']->asImg(32) ?>
                <? endif ?>
            </div>
        </label>
        <? if ($folder_type['class'] === get_class($folder)) : ?>
            <? $folder_template = $folder->getEditTemplate() ?>
        <? else : ?>
            <? $folder_template = $folder_type['instance']->getEditTemplate() ?>
        <? endif; ?>
        <? if ($folder_template) : ?>
            <div class="terms_of_use_description">
                <div class="description">
                    <?= $folder_template->render() ?>
                </div>
            </div>
        <? endif; ?>
    <? endforeach; ?>
</fieldset>
<? endif ?>
