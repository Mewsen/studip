<?php
/**
 * @var bool $show_data_protection_info
 * @var StudipController $controller
 * @var LtiDeployment $deployment
 * @var array $launch_data
 * @var string $signature
 * @var bool $lti13a_mode
 * @var \OAT\Library\Lti1p3Core\Message\LtiMessage $message
 */
?>
<? if ($show_data_protection_info) : ?>
    <article class="studip">
        <header><h1><?= _('Datenschutzhinweise') ?></h1></header>
        <section>
            ACHTUNG! Sie verlassen jetzt Stud.IP! Vorsicht mit den Daten!
        </section>
    </article>
    <?= $this->render_partial('lti/_deployment_user_info', ['deployment' => $deployment]) ?>
    <article class="studip">
        <header><h1><?= _('Bestätigung') ?></h1></header>
        <section>
            <?= _(
                'Ich habe die Datenschutzhinweise zur Benutzung des LTI-Tools zur Kenntnis genommen und stimme der Weitergabe meiner Daten zu. '
                . 'Mir ist bewusst, dass ich ohne die Zustimmung das LTI-Tool nicht nutzen kann.'
            ) ?>
            <form class="default" method="post" action="<?= $controller->link_for('course/lti/iframe/' . htmlReady($deployment->id)) ?>">
                <?= CSRFProtection::tokenTag() ?>
                <?= \Studip\Button::createAccept(_('Ja'), 'continue') ?>
                <? if (empty($deployment->options['document_target']) || $deployment->options['document_target'] !== 'ifame') : ?>
                    <?= \Studip\Button::createCancel(_('Nein')) ?>
                <? endif ?>
            </form>
        </section>
    </article>
<? else : ?>
    <!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8">
        <? if (!$lti13a_mode) : ?>
            <script type="text/javascript">
                window.onload=document.ltiLaunchForm.submit();
            </script>
        <? endif ?>
    </head>
    <body>
        <? if ($lti13a_mode) : ?>
            <? if ($message) : ?>
                <?= $message->toHtmlRedirectForm(Request::submitted('do_not_send') ? false : true) ?>
            <? else: ?>
                <?= _('Das LTI-Tool kann nicht aufgerufen werden.') ?>
            <? endif ?>
        <? else : ?>
            <form name="ltiLaunchForm" method="post" action="<?= htmlReady($deployment->getLaunchUrl()) ?>">
                <? foreach ($launch_data as $key => $value): ?>
                    <input type="hidden" name="<?= htmlReady($key) ?>" value="<?= htmlReady($value, false) ?>">
                <? endforeach ?>
                <input type="hidden" name="oauth_signature" value="<?= $signature ?>">
                <noscript>
                    <button><?= _('Anwendung starten') ?></button>
                </noscript>
            </form>
        <? endif ?>
    </body>
    </html>
<? endif ?>
