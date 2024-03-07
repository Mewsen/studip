<?php
/**
 * @var string $launch_url
 * @var array $launch_data
 * @var string $signature
 * @var bool $lti13a_mode
 * @var \OAT\Library\Lti1p3Core\Message\LtiMessage $message
 */
?>
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
        <form name="ltiLaunchForm" method="post" action="<?= htmlReady($launch_url) ?>">
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
