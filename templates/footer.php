<? if (PageLayout::isFooterEnabled()): ?>
    <!-- Beginn Footer -->
    <?php SkipLinks::addIndex(_('Fußzeile'), 'main-footer', 900, false) ?>
    <footer id="main-footer" aria-label="<?= _('Fußzeile') ?>">
        <? if (is_object($GLOBALS['user']) && $GLOBALS['user']->id != 'nobody') : ?>
            <div id="main-footer-info">
                <?= sprintf(_('Sie sind angemeldet als %s (%s)'),
                    htmlReady($GLOBALS['user']->username),
                    htmlReady($GLOBALS['user']->perms)) ?>
                |
                <?= strftime('%x, %X') ?>
            </div>
        <? endif ?>
        <? if (Navigation::hasItem('/footer')): ?>
            <nav id="main-footer-navigation" aria-label="<?= _('Fußzeilennavigation') ?>">
                <ul>
                    <? if (is_object($GLOBALS['user']) && $GLOBALS['user']->id !== 'nobody') : ?>
                        <li>
                            <a href="<?= URLHelper::getLink('dispatch.php/short_urls/create', ['path' => Request::path()]) ?>"
                               data-dialog="size=400x350">
                                <?= _('Kurz-URL') ?>
                            </a>
                        </li>
                    <? endif ?>
                    <? foreach (Navigation::getItem('/footer') as $nav): ?>
                        <? if ($nav->isVisible()): ?>
                            <li>
                                <a <? if (is_internal_url($url = $nav->getURL())): ?>
                                    href="<?= URLHelper::getLink($url, $link_params ?? null) ?>" <? else: ?>
                                    href="<?= htmlReady($url) ?>" target="_blank" rel="noopener noreferrer" <? endif ?>
                                    <?= arrayToHtmlAttributes($nav->getLinkAttributes()) ?>><?= htmlReady($nav->getTitle()) ?></a>
                            </li>
                        <? endif ?>
                    <? endforeach ?>
                </ul>
            </nav>
        <? endif ?>
    </footer>
<? endif ?>
<!-- Ende Footer -->
