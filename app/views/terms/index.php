<?php
/**
 * @var TermsController $controller
 * @var string $return_to
 * @var string $redirect_token
 * @var string $denial_message
 * @var string $compulsory
 * @var array{type: string, url: string} $terms_of_use
 */
?>
<form action="<?= $controller->link_for('terms', compact('return_to', 'redirect_token')) ?>" method="post">
    <?= CSRFProtection::tokenTag()?>
    <? if ($denial_message): ?>
        <section class="contentbox">
            <header>
                <h1><?= _('Was können Sie tun?') ?></h1>
            </header>
            <section>
                <?= $denial_message ?>
            </section>
        </section>
    <? else: ?>
        <?= $GLOBALS['template_factory']->render('terms.php', [
            'terms_of_use' => $terms_of_use,
        ]) ?>
    <? endif; ?>
    <footer style="text-align: center">
    <? if ($denial_message): ?>
        <?= Studip\Button::createAccept(_('Verstanden'), 'deny', [
            'formaction' => URLHelper::getURL('dispatch.php/logout'),
        ]) ?>
    <? else: ?>
        <?= Studip\Button::createAccept(_('Ich erkenne die Nutzungsbedingungen an'), 'accept') ?>

        <? if (!$compulsory): ?>
        <?= Studip\LinkButton::createCancel(
            _('Ich stimme den Nutzungsbedingungen nicht zu'),
            $controller->url_for('terms', ['action' => 'denied'])
        ) ?>
        <? endif; ?>
    <? endif; ?>
    </footer>
</form>
