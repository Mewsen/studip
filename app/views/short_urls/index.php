<?php
/**
 * @var ShortUrl[] $short_urls
 * @var ShortUrlsController $controller
 */
?>
<? if (!empty($short_urls)) : ?>
    <form method="post">
        <?= CSRFProtection::tokenTag() ?>
        <table class="default">
            <thead>
            <tr>
                <th><?= _('Alias') ?></th>
                <th><?= _('Ziel') ?></th>
                <th class="actions"><?= _('Aktionen') ?></th>
            </tr>
            </thead>
            <tbody>
            <? foreach ($short_urls as $url) : ?>
                <tr>
                    <td><?= htmlReady($url->alias) ?></td>
                    <td><?= htmlReady($url->path) ?></td>
                    <td class="actions">

                    </td>
                </tr>
            <? endforeach ?>
            </tbody>
        </table>
    </form>
<? else : ?>
    <?= MessageBox::info(_('Sie haben noch keine Kurz-URL angelegt.')) ?>
<? endif ?>
