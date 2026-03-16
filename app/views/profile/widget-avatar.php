<div class="avatar-widget">
    <? if ($GLOBALS['perm']->have_profile_perm('user', $current_user)) : ?>
        <a class="profile-avatar"
           href="<?= URLHelper::getURL('dispatch.php/settings/avatar/') ?>">
            <?= $avatar->getImageTag(Avatar::NORMAL) ?>
            <div id="avatar-overlay" class="avatar-overlay">
                <div class="text">
                    <?= _('Profilbild ändern') ?>
                </div>
            </div>
        </a>
    <? else : ?>
        <?= $avatar->getImageTag(Avatar::NORMAL) ?>
    <? endif ?>
</div>
<div class="profile-sidebar-details">
    <? if ($can_show_scores): ?>
        <div id="profile-score-container" role="status" aria-live="polite" aria-busy="true">
            <div class="minor profile-score-loader">
                <span>
                    <?= _('Stud.IP-Punkte wird berechnet...') ?>
                </span>
                <img  width="14" height="14"
                    src="<?= Assets::image_path('loading-indicator.svg') ?>"
                    alt=""
                >
            </div>
            <div class="profile-score-kings hidden"></div>
            <div class="minor profile-score-info hidden">
                <a class="profile-score-link" href="<?= URLHelper::getLink('dispatch.php/score') ?>" title="<?= _('Zur Rangliste') ?>">
                    <div class="hidden">
                        <?= _('Stud.IP-Punkte') ?>: <span id="profile-score"></span>
                    </div>
                    <div class="hidden">
                        <?= _('Rang') ?>: <span id="profile-score-title"></span>
                    </div>
                </a>
            </div>
        </div>
    <? endif; ?>
    <div class="minor">
        <?= _('Profilbesuche:') ?>
        <?= number_format($views, 0, ',', '.') ?>
    </div>
</div>
