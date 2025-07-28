<?php
/**
 * @var Course_Forum_ConfigsController $controller
 * @var CourseConfig $config
 */
?>

<form class="default" method="post" action="<?= $controller->url_for('course/forum/configs/save') ?>">
    <?= CSRFProtection::tokenTag() ?>

    <label>
        <?= _('Wer darf das Forum moderieren?') ?>
        <select name="forum_moderation_permission">
            <option
                value="all"
                <?php if ($config->FORUM_MODERATION_PERMISSION === 'all') echo 'selected'; ?>
            >
                <?= _('Alle Teilnehmenden der Veranstaltung') ?>
            </option>

            <option
                value="tutor"
                <?php if ($config->FORUM_MODERATION_PERMISSION === 'tutor') echo 'selected'; ?>
            >
                <?= _('Tutor/-innen und Lehrende') ?>
            </option>

            <option
                value="dozent"
                <?php if ($config->FORUM_MODERATION_PERMISSION === 'dozent') echo 'selected'; ?>
            >
                <?= _('Nur Lehrende') ?>
            </option>
        </select>
    </label>

    <label>
        <input
            type="checkbox"
            aria-label="<?= _('Kategorien ausblenden') ?>"
            name="forum_hide_categories_navigation"
            <?= $config->FORUM_HIDE_CATEGORIES_NAVIGATION ? 'checked' : '' ?>
            value="1"
        />
        <span>
            <?= _('Kategorien ausblenden') ?>
        </span>
    </label>

    <div data-dialog-button>
        <?= \Studip\Button::create(_('Übernehmen')) ?>
    </div>
</form>
