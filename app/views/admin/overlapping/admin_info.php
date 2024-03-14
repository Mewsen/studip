<?php
/**
 * @var Course $course
 * @var InstituteMember[] $admins
 * @var ProfileController $controller
 */
?>
<section class="contentbox">
    <header>
        <h1>
            <?= _('Zuständige Administratoren') ?>
        </h1>
    </header>
    <section>
        <dl>
            <dt>
                <?= _('Heimateinrichtung') ?>
            </dt>
            <dd>
                <?= htmlReady($course->home_institut->getFullName()) ?>
            </dd>
            <dt>
                <?= _('Zuständige Administratoren') ?>
            </dt>
            <? foreach ($admins as $admin) : ?>
            <dd>
                <a href="<?= $controller->url_for('profile', ['username' => $admin->username]) ?>">
                    <?= htmlReady($admin->user->getFullName()) ?>
                </a>
            </dd>
            <? endforeach; ?>
        </dl>
    </section>
</section>
