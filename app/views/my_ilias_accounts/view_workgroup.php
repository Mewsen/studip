<form class="default" action="<?= $controller->link_for($module->getRoute('view_tools')) ?>" method="post" target="_blank">
    <?= CSRFProtection::tokenTag() ?>
    <?= $this->render_partial('my_ilias_accounts/_ilias_module.php') ?>
    <footer data-dialog-button>
    <? if ($ilias->isActive()) : ?>
        <?= $module->isAllowed('start') ? Studip\LinkButton::create(_('Zum Arbeitsbereich in ILIAS'), $controller->url_for($module->getRoute('start')), ['target' => '_blank', 'rel' => 'noopener noreferrer']) :'' ?>
        <?= $module->isAllowed('edit') ? Studip\LinkButton::create(_('Arbeitsbereich bearbeiten'), $controller->url_for($module->getRoute('edit')), ['target' => '_blank', 'rel' => 'noopener noreferrer']) :'' ?>
    <? endif ?>
        <?= Studip\Button::createCancel(_('Schließen'), 'cancel', ['data-dialog' => 'close']) ?>
    </footer>
</form>