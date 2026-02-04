<?php
/**
 * @var Lti_1p1_IndexController $controller
 * @var string $launchUrl
 * @var array $launchData
 * @var string $signature
 */
?>


<form name="ltiLaunchForm" method="post" action="<?= htmlReady($launchUrl) ?>">
    <? foreach ($launchData as $key => $value): ?>
        <input type="hidden" name="<?= htmlReady($key) ?>" value="<?= htmlReady($value, false) ?>" />
    <? endforeach ?>
    <input type="hidden" name="oauth_signature" value="<?= $signature ?>" />
    <noscript>
        <button><?= _('Anwendung starten') ?></button>
    </noscript>
</form>
