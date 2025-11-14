<div class="blubber_panel vueinstance"
    <?= arrayToHtmlAttributes([
        'data-initial-thread-id' => !empty($thread) ? $thread->getId() : '',
        'data-search' => $search,
    ]) ?>
></div>
