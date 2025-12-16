<ul class="clean">
    <? $folder = $topic->folders->first() ?>
    <? if ($documents_activated && $folder) : ?>
        <li>
            <? $folder_label = sprintf(_('Zu Dateiordner von Thema %s navigieren'), htmlReady($topic->title)) ?>
            <a
                href="<?= URLHelper::getLink('dispatch.php/course/files/index/' . $folder->id) ?>"
                aria-label="<?= $folder_label ?>"
                title="<?= $folder_label ?>"
            >
                <?= $folder->getTypedFolder()->getIcon('clickable')->asSvg(['class' => 'text-bottom']) ?>
                <span class="<?= $always_show ? '' : 'responsive-hidden' ?>">
                    <?= _('Dateiordner') ?>
                </span>
            </a>
        </li>
    <? endif ?>

    <? if ($forum_activated && $topic->forum_thread_url) : ?>
        <li>
            <? $ftopic_label = sprintf(_('Zu Forumsthema von Thema %s navigieren'), htmlReady($topic->title)) ?>
            <a
                href="<?= URLHelper::getLink($topic->forum_thread_url) ?>"
                aria-label="<?= $ftopic_label ?>"
                title="<?= $ftopic_label ?>"
            >
                <?= Icon::create('forum')->asSvg(['class' => 'text-bottom']) ?>
                <span class="<?= $always_show ? '' : 'responsive-hidden' ?>">
                    <?= _('Thema im Forum') ?>
                </span>
            </a>
        </li>
    <? endif ?>
</ul>
