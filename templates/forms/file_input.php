<div class="formpart" data-form-input-for="<?= htmlReady($name) ?>">
    <file-upload
        name="<?= htmlReady($name) ?>"
        title="<?= htmlReady($title) ?>"
        upload-url="<?= htmlReady($uploadUrl) ?>"
        folder="<?= htmlReady($value) ?>"
        id="<?= htmlReady($id) ?>"
        :multiple="<?= $multiple ? 'true' : 'false' ?>"
        accept="<?= htmlReady($accept) ?>"
        <?= $required ? ':required="true"' : '' ?>></file-upload>
</div>
