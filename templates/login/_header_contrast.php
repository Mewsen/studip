<form method="post" action="<?= URLHelper::getLink('dispatch.php/nobody_settings/store_settings') ?>">
    <? try {echo CSRFProtection::tokenTag();} catch (SessionRequiredException){}?>
    <input type="hidden" name="user_config_submitted" value="1">
    <input type="hidden" name="page" value="<?= Request::url() ?>">
    <div id="contrast">
        <? if (!empty($_SESSION['contrast'])): ?>
            <?= Icon::create('accessibility', Icon::ROLE_INFO_ALT)->asImg(24) ?>
            <button class="as-link" name="unset_contrast"><?= _('Normalen Kontrast aktivieren') ?></button>
            <?= tooltipIcon(_('Aktiviert standardmäßige, nicht barrierefreie Kontraste.'), false, false, true); ?>
        <? else: ?>
            <?= Icon::create('accessibility', Icon::ROLE_INFO_ALT)->asImg(24) ?>
            <button class="as-link" name="set_contrast"><?= _('Hohen Kontrast aktivieren') ?></button>
            <?= tooltipIcon(_('Aktiviert einen hohen Kontrast gemäß WCAG 2.1. Diese Einstellung wird nach dem Login übernommen.
                                Sie können sie in Ihren persönlichen Einstellungen ändern.'), false, false, true); ?>
        <? endif ?>
    </div>

</form>
