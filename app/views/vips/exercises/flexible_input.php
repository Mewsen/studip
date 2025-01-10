<?php
/**
 * @var string $size
 */
?>
<div class="flexible_input">
    <input type="text" class="character_input small_input size-l"
        <? if ($size === 'small'): ?>
            <?= isset($name) ? 'name="'.$name.'"' : (isset($data_name) ? 'data-name="'.$data_name.'"' : '') ?>
        <? endif ?>
        <? if (isset($value)): ?>
            value="<?= htmlReady($value) ?>"
        <? endif ?>
        >
    <div class="large_input">
        <? $wysiwyg = isset($data_name) ? 'wysiwyg-hidden' : 'wysiwyg' ?>
        <textarea class="character_input <?= $wysiwyg ?> size-l" data-editor="removePlugins=studip-quote,studip-settings;toolbar=small"
            <? if ($size === 'large'): ?>
                <?= isset($name) ? 'name="'.$name.'"' : (isset($data_name) ? 'data-name="'.$data_name.'"' : '') ?>
            <? endif ?>
        ><?= wysiwygReady($value ?? '') ?></textarea>
    </div>
</div>
<?= Icon::create('arr_1down')->asInput(['class' => 'textarea_toggle small_input', 'title' => _('Auf mehrzeilige Eingabe umschalten')]) ?>
<?= Icon::create('arr_1up')->asInput(['class' => 'textarea_toggle large_input', 'title' => _('Auf einzeilige Eingabe umschalten')]) ?>
