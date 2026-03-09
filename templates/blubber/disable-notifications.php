<? if (!$GLOBALS['perm']->have_perm("admin")) : ?>
    <div class="indented new_section">
        <a href="#"
           onClick="STUDIP.Blubber.followunfollow('<?= htmlReady($thread->id) ?>'); return false;"
           class="followunfollow<?= $unfollowed ? " unfollowed" : "" ?>"
           title="<?= _('Benachrichtigungen für diese Konversation abstellen.') ?>"
           data-thread_id="<?= htmlReady($thread->id) ?>">
            <?= Icon::create('decline')->asImg(['class' => 'follow text-bottom']) ?>
            <?= Icon::create('notification2')->asImg(['class' => 'unfollow text-bottom']) ?>
            <?= _('Benachrichtigungen aktiviert') ?>
        </a>
    </div>
<? endif ?>
