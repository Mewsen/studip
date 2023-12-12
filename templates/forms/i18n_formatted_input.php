<div class="formpart" data-form-input-for="<?= htmlReady($name) ?>">
    <label<?= ($this->required ? ' class="studiprequired"' : '') ?> for="<?= $id ?>">
        <span class="textlabel">
            <?= htmlReady($this->title) ?>
        </span>
        <? if ($this->required) : ?>
            <span class="asterisk" title="<?= _('Dies ist ein Pflichtfeld') ?>" aria-hidden="true">*</span>
        <? endif ?>
    </label>
    <i18n-textarea type="wysiwyg"
                   id="<?= $id ?>"
                   name="<?= htmlReady($name) ?>"
                   value="<?= htmlReady($value) ?>"
                   @allinputs="setInputs"
                   @selectlanguage="(language_id) => selectLanguage('<?= htmlReady($this->name) ?>', language_id)"
                   :wysiwyg_disabled="<?= \Config::get()->WYSIWYG ? 'false' : 'true' ?>" <?= $required ? 'required' : '' ?>>
    </i18n-textarea>
    </div>
