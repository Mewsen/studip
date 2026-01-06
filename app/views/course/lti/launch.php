<?php
/**
 * @var StudipController $controller
 * @var ?Lti\ResourceLink $resourceLink
 * @var string $launchUrl
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
        <title data-original="<?= htmlReady(PageLayout::getTitle()) ?>">
            <?= htmlReady(PageLayout::getTitle() . ' - ' . Config::get()->UNI_NAME_CLEAN) ?>
        </title>
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
            <form name="ltiLaunchForm" method="post" action="<?= htmlReady($launchUrl) ?>">
                <? foreach ($launchData as $key => $value): ?>
                    <input type="hidden" name="<?= htmlReady($key) ?>" value="<?= htmlReady($value, false) ?>" />
                <? endforeach ?>
                <input type="hidden" name="oauth_signature" value="<?= $signature ?>" />
                <noscript>
                    <button><?= _('Anwendung starten') ?></button>
                </noscript>
            </form>
        <? endif ?>
    </body>
</html>
