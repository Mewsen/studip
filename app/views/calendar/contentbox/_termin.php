<?php
/**
 * @var CourseDate|CourseExDate|CalendarDateAssignment $termin
 * @var bool $admin
 * @var bool $isProfile
 * @var bool $course_range
 */
?>
<article class="studip toggle <?= ContentBoxHelper::classes($termin->getObjectId()) ?>"
         id="<?= htmlReady($termin->getObjectId()) ?>">
    <header>
        <h1>
            <a href="<?= ContentBoxHelper::href($termin->getObjectId()) ?>">
                <?= Icon::create('date', Icon::ROLE_INACTIVE)->asSvg(['class' => 'text-bottom']) ?>
                <?= htmlReady($titles[$termin->getObjectId()] ?? $termin->getTitle()) ?>
            </a>
        </h1>
        <nav>
            <span>
            <? if ($termin instanceof CalendarDateAssignment): ?>
                <?= $termin->getLocation() ? _('Raum') . ': ' . formatLinks($termin->getLocation()) : '' ?>
            <? else: ?>
                <?= $termin->getRoomName() ? _('Raum') . ': ' . formatLinks($termin->getRoomName()) : '' ?>
            <? endif; ?>
            </span>
            <? if ($admin && $isProfile && $termin->getObjectClass() === 'CalendarDateAssignment') : ?>
                <a href="<?= URLHelper::getLink('dispatch.php/calendar/calendar') ?>"
                   title="<?= _('Zum Kalender') ?>" aria-label="<?= _('Zum Kalender') ?>">
                    <?= Icon::create('schedule')->asSvg(['class' => 'text-bottom']) ?>
                </a>
                <? if ($termin->calendar_date->isWritable($GLOBALS['user']->id)) : ?>
                    <a href="<?= URLHelper::getLink('dispatch.php/calendar/date/edit/' . $termin->getPrimaryObjectId()) ?>"
                       title="<?= _('Termin bearbeiten') ?>" aria-label="<?= _('Termin bearbeiten') ?>"
                       data-dialog>
                        <?= Icon::create('edit')->asSvg(['class' => 'text-bottom']) ?>
                    </a>
                <? endif ?>
            <? elseif (!$course_range && in_array($termin->getObjectClass(), [CalendarCourseDate::class, CalendarCourseExDate::class])) : ?>
                <a href="<?= URLHelper::getLink('dispatch.php/course/dates', ['cid' => $termin->getPrimaryObjectId()]) ?>"
                   title="<?= _('Zur Veranstaltung') ?>" aria-label="<?= _('Zur Veranstaltung') ?>">
                    <?= Icon::create('seminar')->asSvg(['class'=> 'text-bottom']) ?>
                </a>
            <? endif ?>
        </nav>
    </header>
    <div>
        <?
        $themen = [];
        if ($termin instanceof CourseDate) {
            $themen = $termin->topics->toArray('title description');
        }
        $description = '';
        if ($termin instanceof CourseExDate) {
            $description = $termin->content;
        } elseif ($termin instanceof CourseDate && isset($termin->cycle)) {
            $description = $termin->cycle->description;
        } elseif (empty($themen)) {
            $description = $termin->getDescription();
        }
        ?>
        <? if ($description || count($themen) > 0) : ?>
            <p><?= formatReady($description) ?></p>
            <? if (count($themen)) : ?>
                <? foreach ($themen as $thema) : ?>
                    <h3>
                        <?= Icon::create('topic', Icon::ROLE_INFO)->asSvg(['class' => 'text-bottom']) ?>
                        <?= htmlReady($thema['title']) ?>
                    </h3>
                    <div>
                        <?= formatReady($thema['description']) ?>
                    </div>
                <? endforeach ?>
            <? endif ?>
        <? else : ?>
            <?= _('Keine Beschreibung vorhanden') ?>
        <? endif ?>
        <ul class="list-csv" style="text-align: center;">
            <? foreach ($termin->getAdditionalDescriptions() as $type => $info) : ?>
                <? if (trim($info)) : ?>
                    <li>
                        <small>
                            <? if (!is_numeric($type)): ?>
                                <em><?= htmlReady($type) ?>:</em>
                            <? endif; ?>
                            <?= htmlReady(trim($info)) ?>
                        </small>
                    </li>
                <? endif ?>
            <? endforeach ?>
        </ul>
        <? if (!$course_range && in_array($termin->getObjectClass(), [CalendarCourseDate::class, CalendarCourseExDate::class])) : ?>
            <div>
                <a href="<?= URLHelper::getLink('dispatch.php/course/dates', ['cid' => $termin->getPrimaryObjectId()]) ?>">
                    <?= Icon::create('link-intern')->asSvg(['class'=> 'text-bottom']) ?>
                    <?= _('Zur Veranstaltung') ?>
                </a>
            </div>
        <? endif ?>
    </div>
</article>
