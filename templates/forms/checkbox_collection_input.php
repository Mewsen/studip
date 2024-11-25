<?php
/**
 * @var bool $collapsible
 * @var string $title
 * @var array $options
 * @var bool $required
 * @var string $name
 * @var array $selected
 * @var array $attributes
 */
?>
<fieldset<?= $collapsable ? ' class="collapsable collapsed"' : '' ?>>
    <legend><?= htmlReady($title) ?></legend>
    <? foreach ($options as $id => $displayname): ?>
        <label<?= $required ? ' class="studiprequired"' : '' ?>>
            <input type="checkbox"
                   v-model="<?= htmlReady($name) ?>"
                   name="<?= htmlReady($name) ?>[]"
                   value="<?= $id ?>"
                   class="<?= htmlReady($name . '-selector') ?>"
                   id="<?= $id ?>"
                <?= $required ? 'required aria-required="true"' : '' ?>
                <?= in_array($id, $selected) ? 'selected' : '' ?>
                <?= $attributes ?>>
            <span class="textlabel">
                <?= htmlReady($displayname) ?>
            </span>
            <? if ($required) : ?>
                <span class="asterisk" title="<?= _('Dies ist ein Pflichtfeld') ?>" aria-hidden="true">*</span>
            <? endif ?>
        </label>
    <? endforeach ?>
</fieldset>
