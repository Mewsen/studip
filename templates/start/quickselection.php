<div id="quickSelectionWrap" style="padding: 1ex;">
<? foreach ($navigation as $nav) : ?>
    <? if ($nav->isVisible()) : ?>
        <ul class="mainmenu list-unstyled">
            <? if (is_internal_url($url = $nav->getURL())) : ?>
                <li><a href="<?= URLHelper::getLink($url) ?>">
            <? else : ?>
                <li><a href="<?= htmlReady($url) ?>" target="_blank" rel="noopener noreferrer">
            <? endif ?>
            <?= htmlReady($nav->getTitle()) ?></a></li>

        <li>
        <ul class="list-slash-separated-small">
            <? foreach ($nav as $subnav) : ?>
                <? if ($subnav->isVisible()) : ?>
                    <li>
                        <? if (is_internal_url($url = $subnav->getURL())) : ?>
                        <a href="<?= URLHelper::getLink($url) ?>">
                            <? else : ?>
                            <a href="<?= htmlReady($url) ?>" target="_blank" rel="noopener noreferrer">
                                <? endif ?>
                                <?= htmlReady($subnav->getTitle()) ?></a>
                    </li>
                <? endif ?>
            <? endforeach ?>
        </ul>
        </li>
    </ul>
    <? endif ?>
<? endforeach ?>
</div>
