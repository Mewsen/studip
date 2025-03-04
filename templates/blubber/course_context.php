<div class="blubber_course_info indented">
    <div class="headline">
        <div class="side">
            <a href="<?= URLHelper::getLink("dispatch.php/course/go", ['to' => $course->getId()]) ?>">
                <?= htmlReady($course->name) ?>
            </a>
        </div>
    </div>
    <div>
        <? $sem_class = $course->getSemClass() ?>
        <h4><?= htmlReady($sem_class['title_dozent_plural'] ?: $GLOBALS['DEFAULT_TITLE_FOR_STATUS']['dozent'][1]) ?></h4>
        <ol class="clean members">
            <? foreach ($teachers as $teacher) : ?>
                <li>
                    <a href="<?= URLHelper::getLink("dispatch.php/profile", ['username' => $teacher['username']]) ?>">
                        <?= Avatar::getAvatar($teacher['user_id'])->getImageTag(Avatar::SMALL) ?>
                        <?= htmlReady($teacher->getUserFullname()) ?>
                    </a>
                </li>
            <? endforeach ?>
        </ol>
        <? if (count($tutors)) : ?>
            <h4><?= htmlReady($sem_class['title_tutor_plural'] ?: $GLOBALS['DEFAULT_TITLE_FOR_STATUS']['tutor'][1]) ?></h4>
            <ol class="clean members">
                <? foreach ($tutors as $tutor) : ?>
                    <li>
                        <a href="<?= URLHelper::getLink("dispatch.php/profile", ['username' => $tutor['username']]) ?>">
                            <?= Avatar::getAvatar($tutor['user_id'])->getImageTag(Avatar::SMALL) ?>
                            <?= htmlReady($tutor->getUserFullname()) ?>
                        </a>
                    </li>
                <? endforeach ?>
            </ol>
        <? endif ?>
        <h4>
            <?= sprintf(_("%s %s und %s"), $students_count, $sem_class['title_tutor_plural'] ?: $GLOBALS['DEFAULT_TITLE_FOR_STATUS']['autor'][1], $GLOBALS['DEFAULT_TITLE_FOR_STATUS']['user'][1]) ?>
        </h4>
    </div>
</div>
<?= $this->render_partial("blubber/_tagcloud") ?>
<?= $this->render_partial('blubber/disable-notifications', compact('thread', 'unfollowed')) ?>
