<div class="formpart" data-form-input-for="<?= htmlReady($name) ?>">
    <label<?= $required ? ' class="studiprequired"' : '' ?> for="<?= htmlReady($id) ?>">
        <span class="textlabel">
            <?= htmlReady($title) ?>
        </span>
        <? if ($required) : ?>
            <span class="asterisk" title="<?= _('Dies ist ein Pflichtfeld') ?>" aria-hidden="true">*</span>
        <? endif ?>
        <quicksearch-list-input
            name="<?= htmlReady($name) ?>"
            id="<?= htmlReady($id) ?>"
            <?= $required ? 'required aria-required="true"' : '' ?>
            value="<?= htmlReady($value) ?>"
            v-model="<?= htmlReady($name) ?>"
            <?= $attributes ?>
        ></quicksearch-list-input>
    </label>
</div>
