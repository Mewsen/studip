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
    <? if ($kings): ?>
        <div><?= $kings ?></div>
    <? endif; ?>
        <div class="minor">
            <?= _('Profilbesuche:') ?>
            <?= number_format($views, 0, ',', '.') ?>
        </div>
    <? if ($score && $score_title): ?>
        <div class="minor">
            <a href="<?= URLHelper::getLink('dispatch.php/score') ?>" title="<?= _('Zur Rangliste') ?>">
                <?= _('Stud.IP-Punkte') ?>: <?= number_format($score, 0, ',', '.') ?>
                <br />
                <?= _('Rang') ?>: <?= htmlReady($score_title) ?>
            </a>
        </div>
    <? endif; ?>
</div>
