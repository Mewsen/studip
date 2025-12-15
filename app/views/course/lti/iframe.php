<?php
/**
 * @var StudipController $controller
 * @var ?LtiResourceLink $resourceLink
 * @var array $launchData
 * @var string $signature
 * @var string $ltiVersion
 * @var \OAT\Library\Lti1p3Core\Message\LtiMessage $message
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <? if (!$ltiVersion === '1.3a') : ?>
            <script type="text/javascript">
                window.onload=document.ltiLaunchForm.submit();
            </script>
        <? endif ?>
    </head>
    <body>
        <? if ($ltiVersion === '1.3a') : ?>
            <? if ($message) : ?>
                <?= $message->toHtmlRedirectForm(Request::submitted('do_not_send') ? false : true) ?>
            <? else: ?>
                <?= _('Das LTI-Tool kann nicht aufgerufen werden.') ?>
            <? endif ?>
        <? else : ?>
            <form name="ltiLaunchForm" method="post" action="<?= htmlReady($resourceLink->deployment->getLaunchUrl()) ?>">
                <? foreach ($launchData as $key => $value): ?>
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
