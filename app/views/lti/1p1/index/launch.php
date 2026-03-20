<?php
/**
 * @var Lti_1p1_IndexController $controller
 * @var string $launchUrl
 * @var array $launchData
 * @var string $signature
 */
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title data-original="<?= htmlReady(PageLayout::getTitle()) ?>">
            <?= htmlReady(PageLayout::getTitle() . ' - ' . Config::get()->UNI_NAME_CLEAN) ?>
        </title>
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function () {
                document.ltiLaunchForm.submit();
            });
        </script>
    </head>
    <body>
        <form name="ltiLaunchForm" method="post" action="<?= htmlReady($launchUrl) ?>">
            <? foreach ($launchData as $key => $value): ?>
                <input type="hidden" name="<?= htmlReady($key) ?>" value="<?= htmlReady($value, false) ?>" />
            <? endforeach ?>
            <input type="hidden" name="oauth_signature" value="<?= $signature ?>" />
            <noscript>
                <button><?= _('Anwendung starten') ?></button>
            </noscript>
        </form>
    </body>
</html>
