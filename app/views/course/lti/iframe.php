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
    <?
    $data_protection_warning = CourseConfig::get(Context::getId())->LTI_DATA_PROTECTION_COURSE_WARNING;
    if (empty($data_protection_warning)) {
        $data_protection_warning = Config::get()->LTI_DATA_PROTECTION_DEFAULT_WARNING;
    }
    ?>
    <?= MessageBox::warning($data_protection_warning)->hideClose() ?>
    <article class="studip">
        <header><h1><?= _('Zu übertragende personenbezogene Daten') ?></h1></header>
        <section>
            <?= _('Beim Wechsel in das LTI-Tool werden die folgenden personenbezogenen Daten übertragen:') ?>
            <ul>
                <li>
                    <label>
                        <input type="checkbox" checked disabled>
                        <?= _('Die ID ihres Stud.IP-Kontos') ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" checked disabled>
                        <?= _('Ihr Vor- und Nachname, sowie gegebenenfalls vorhandene Titel') ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" checked disabled>
                        <?= _('Ihre E-Mail Adresse') ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="submit_optional_field[lang]" value="1">
                        <?= _('Ihre in Stud.IP eingestellte Sprache') ?>
                    </label>
                </li>
                <li>
                    <label>
                        <input type="checkbox" name="submit_optional_field[avatar_url]" value="1">
                        <?= _('Ihr Profilbild') ?>
                    </label>
                </li>
            </ul>
        </section>
    </article>
    <?= $this->render_partial('lti/_deployment_user_info', ['deployment' => $deployment]) ?>
    <article class="studip">
        <header><h1><?= _('Bestätigung') ?></h1></header>
        <section>
            <?= _(
                'Ich habe die Datenschutzhinweise zur Benutzung des LTI-Tools zur Kenntnis genommen und stimme der Weitergabe meiner personenbezogenen Daten zu. '
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
