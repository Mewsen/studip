<form id="language-selector" method="POST" action="<?= URLHelper::getLink(Request::url(), ['cancel_login' => null]) ?>">
    <?= CSRFProtection::tokenTag() ?>
    <input type="hidden" name="user_config_submitted" value="1">
    <select id="languages" name="set_language" class="select2" onchange="this.form.submit()">
        <? foreach ($GLOBALS['INSTALLED_LANGUAGES'] as $temp_language_key => $temp_language): ?>
            <option value="<?= htmlReady($temp_language_key) ?>" <?= array_key_exists('forced_language', $_SESSION) && $_SESSION['forced_language'] === $temp_language_key ? 'selected' : '' ?>
                data-flag="<?= URLHelper::getLink('assets/images/languages/' . $temp_language['picture']) ?>">
                <?= htmlReady($temp_language['name']) ?>
            </option>
        <? endforeach; ?>
    </select>
    </div>
</form>

<script>
    jQuery(function ($) {
        let format = function (state) {
            if (!state.id) { // optgroup
                return state.text;
            }
            let flagUrl = $(state.element).data('flag');
            let flag = $(`<img src="${flagUrl}" style="vertical-align: text-top;">`);
            let span = $('<span>');
            span.text(state.text);
            $(flag).prependTo(span);
            return span;
        };
        $('#languages').select2({
            minimumResultsForSearch: -1,
            width: '100%',
            templateResult: format,
            templateSelection: format
        });
    });
</script>