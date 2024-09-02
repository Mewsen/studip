<section class="contentbox">
    <header>
        <h1><?= _('Grunddaten') ?></h1>
    </header>
    <section>
        <dl style="margin: 0;">
        <? if (Context::get()->Untertitel != '') : ?>
            <dt><?= _('Untertitel') ?></dt>
            <dd>
                <?= htmlReady(Context::get()->Untertitel) ?>
            </dd>
        <? endif ?>
        <? if (!$course->isStudygroup()) : ?>
            <dt><?= _('Zeit / Veranstaltungsort') ?></dt>
            <dd>
                <?= $times_rooms ?: _('Die Zeiten der Veranstaltung stehen nicht fest.') ?>
            </dd>
            <? if ($next_date) : ?>
                <dt><?= _('Nächster Termin') ?></dt>
                <dd><?= $next_date->getFullName('long') ?></dd>
            <? else : ?>
                <dt><?= _('Erster Termin') ?></dt>
                <dd>
                <? if ($first_date) : ?>
                    <?= $first_date->getFullName('long') ?>
                <? else : ?>
                    <?= _('Die Zeiten der Veranstaltung stehen nicht fest.') ?>
                <? endif ?>
                </dd>
            <? endif ?>
            <dt><?= htmlReady(get_title_for_status('dozent', $num_lecturers)) ?></dt>
            <dd><?= implode(', ', $lecturer_html) ?> </dd>
        <? else : ?>
            <? if ($course->beschreibung) : ?>
                <dt><?= _('Beschreibung') ?></dt>
                <dd><?= formatLinks($course->beschreibung) ?></dd>
            <? endif ?>
            <dt><?= _('Moderiert von') ?></dt>
            <dd>
                <ul class="list-csv">
                <? foreach ($all_mods as $mod) : ?>
                    <li>
                        <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $mod->user->username]) ?>">
                            <?= htmlready($mod->user->getFullName()) ?>
                        </a>
                    </li>
                <? endforeach ?>
                </ul>
            </dd>
        <? endif ?>
        </dl>
    </section>
</section>

<?php

// Anzeige von News
if (!empty($news)) {
    echo $news;
}

// Anzeige von Terminen
if (!empty($dates)) {
    echo $dates;
}

if (!empty($questionnaires)) {
    echo $questionnaires;
}

// display plugins

if (!empty($plugins)) {
    $layout = $GLOBALS['template_factory']->open('shared/content_box');
    foreach ($plugins as $plugin) {
        $template = $plugin->getInfoTemplate($course_id);

        if ($template) {
            echo $template->render(layout: $layout);
            $layout->clear_attributes();
        }
    }
}
echo Feedback::getHTML($course_id, 'Course');
