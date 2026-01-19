<?php
/**
 * @var StudipController $controller
 * @var ?LtiResourceLink $resource_link
 * @var array $launch_data
 * @var string $signature
 * @var string $version
 * @var \OAT\Library\Lti1p3Core\Message\LtiMessage $message
 */
?>
<? if ($resource_link) : ?>
   <!DOCTYPE html>
    <html>
    <head>
    <meta charset="UTF-8">
        <? if ($version === '1.1') : ?>
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function () {
                    document.ltiLaunchForm.submit();
                });
            </script>
        <? endif ?>
    </head>
    <body>
        <? if ($version === '1.3a'): ?>
            <? if ($message) : ?>
                <?= $message->toHtmlRedirectForm(Request::submitted('do_not_send') ? false : true) ?>
            <? else: ?>
                <?= _('Das LTI-Tool kann nicht aufgerufen werden.') ?>
            <? endif ?>
        <? endif ?>
        <? if ($version === '1.1'): ?>
            <form name="ltiLaunchForm" method="post" action="<?= htmlReady($resource_link->deployment->getLaunchUrl()) ?>">
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
