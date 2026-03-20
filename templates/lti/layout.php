<?php
/**
 * @var string $content_for_layout
 */

?>

<!DOCTYPE html>
<html lang="<?= htmlReady(str_replace('_', '-', $_SESSION['_language'])) ?>">
    <head>
        <meta charset="utf-8">
        <title data-original="<?= htmlReady(PageLayout::getTitle()) ?>">
            <?= htmlReady(PageLayout::getTitle() . ' - ' . Config::get()->UNI_NAME_CLEAN) ?>
        </title>

        <script>
            String.locale = "<?= htmlReady(strtr($_SESSION['_language'], '_', '-')) ?>";
            window.STUDIP = {
                ABSOLUTE_URI_STUDIP: "<?= $GLOBALS['ABSOLUTE_URI_STUDIP'] ?>",
                CSRF_TOKEN: {
                    name: '<?=CSRFProtection::TOKEN?>',
                    value: '<? try {echo CSRFProtection::token();} catch (SessionRequiredException $e){}?>'
                },
                STUDIP_SHORT_NAME: "<?= htmlReady(Config::get()->STUDIP_SHORT_NAME) ?>",
                server_timestamp: <?= time() ?>,
            }
        </script>

        <?= PageLayout::getHeadElements() ?>
        <link href="<?= URLHelper::getLink('assets/stylesheets/lti.css') ?>" rel="stylesheet" type="text/css">

        <style>
            body.lti {
                display: grid;
                grid-template-columns: 1fr;
            }

            .content-wrapper {
                width: 100%;
                max-width: 1152px;
                margin: 0 auto;
            }
            .content {
                padding: 16px;
            }
            #current-page-structure {
                display: none;
            }
        </style>
    </head>

    <body class="lti">
        <main class="content-wrapper">
            <h1 class="sr-only"><?= htmlReady(PageLayout::getTitle()) ?></h1>
            <div class="content">
                <?= $content_for_layout ?>
            </div>
        </main>
    </body>
</html>
