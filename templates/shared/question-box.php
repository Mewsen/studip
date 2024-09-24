<div class="modaloverlay">
    <div class="ui-dialog ui-corner-all ui-widget ui-widget-content ui-front ui-dialog-buttons studip-confirmation"
         role="alertdialog" aria-labelledBy="studip-confirmation-title-1" aria-describedby="studip-confirmation-desc-1"
         modal="true">
        <form action="<?= URLHelper::getLink($accept_url) ?>" method="post">
            <?= CSRFProtection::tokenTag() ?>
        <? foreach ($accept_parameters as $key => $value): ?>
            <?= addHiddenFields($key, $value) ?>
        <? endforeach; ?>

            <div class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
                <span class="ui-dialog-title" id="studip-confirmation-title-1">
                    <?= _('Bitte bestätigen Sie die Aktion') ?>
                </span>
                <a href="<?= URLHelper::getLink($decline_url, $decline_parameters) ?>" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close">
                    <span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span>
                    <span class="ui-button-text"><?= _('Schliessen') ?></span>
                </a>
            </div>
            <div class="content ui-widget-content ui-dialog-content studip-confirmation"
                 id="studip-confirmation-desc-1" role="heading" aria-level="2">
                <?= $question ?>
            </div>
            <div class="buttons ui-widget-content ui-dialog-buttonpane">
                <div class="ui-dialog-buttonset">
                    <?= Studip\Button::createAccept(_('Ja'), 'yes') ?>

                    <?= Studip\LinkButton::createCancel(
                        _('Nein'),
                        URLHelper::getURL($decline_url, $decline_parameters)
                    ) ?>
                </div>
            </div>
        </form>
    </div>
</div>
