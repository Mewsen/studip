<? if (PageLayout::isFooterEnabled()): ?>
<!-- Beginn Footer -->
<?= SkipLinks::addIndex(_('Fußzeile'), 'main-footer', 900, false) ?>
<footer id="main-footer" aria-label="<?= _('Fußzeile') ?>">
<? if (is_object($GLOBALS['user']) && $GLOBALS['user']->id != 'nobody') : ?>
    <div id="main-footer-info">
        <? printf(_('Sie sind angemeldet als %s (%s)'),
                  htmlReady($GLOBALS['user']->username),
                  htmlReady($GLOBALS['user']->perms)) ?>
        |
        <?= strftime('%x, %X') ?>
    </div>
<? endif; ?>

<? if (Navigation::hasItem('/footer')) : ?>
    <nav id="main-footer-navigation" aria-label="<?= _('Fußzeilennavigation') ?>">
        <ul>
        <? foreach (Navigation::getItem('/footer') as $nav): ?>
            <? if ($nav->isVisible()): ?>
                <li>
                <a
                <? if (is_internal_url($url = $nav->getURL())) : ?>
                    href="<?= URLHelper::getLink($url, $link_params ?? null) ?>"
                <? else: ?>
                    href="<?= htmlReady($url) ?>" target="_blank" rel="noopener noreferrer"
                <? endif ?>
                    <?= arrayToHtmlAttributes($nav->getLinkAttributes()) ?>
                ><?= htmlReady($nav->getTitle()) ?></a>
                </li>
            <? endif; ?>
        <? endforeach; ?>
        </ul>
    </nav>
<? endif; ?>
</footer>
<? endif; ?>
<!-- Ende Footer -->
