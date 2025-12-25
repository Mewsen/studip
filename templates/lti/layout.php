<!DOCTYPE html>
<html lang="<?= htmlReady(str_replace('_', '-', $_SESSION['_language'])) ?>">
    <head>
        <meta charset="utf-8">
        <title data-original="<?= htmlReady(PageLayout::getTitle()) ?>">
            <?= htmlReady(PageLayout::getTitle() . ' - ' . Config::get()->UNI_NAME_CLEAN) ?>
        </title>

        <?= PageLayout::getHeadElements() ?>
    </head>

    <body id="lti" class="no-sidebar">
        <!-- Start main page content -->
        <main id="content-wrapper">
            <? SkipLinks::addIndex(_('Hauptinhalt'), 'content', 100) ?>
            <div id="content">
                <h1 class="sr-only"><?= htmlReady(PageLayout::getTitle()) ?></h1>
                <?= implode(PageLayout::getMessages(QuestionBox::class)) ?>
                <?= $content_for_layout ?>
            </div>

        </main>
        <!-- End main content -->
    </body>
</html>
