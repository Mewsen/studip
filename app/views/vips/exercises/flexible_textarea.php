<?php
/**
 * @var Exercise $exercise
 * @var bool $wysiwyg
 * @var bool $monospace
 * @var string $name
 * @var string $value
 */
?>
<div class="size_toggle <?= $wysiwyg ? 'size_large' : 'size_small' ?>">
    <textarea class="character_input size-l small_input <?= $monospace ? 'monospace' : '' ?>" <?= $wysiwyg ? '' : 'name="'.$name.'"' ?>
        rows="<?= $exercise->textareaSize($value) ?>"><?= htmlReady($value) ?></textarea>
    <div class="large_input">
        <textarea class="character_input size-l wysiwyg" <?= $wysiwyg ? 'name="'.$name.'"' : '' ?>><?= wysiwygReady($value) ?></textarea>
    </div>
    <button hidden class="textarea_toggle"></button>
</div>
