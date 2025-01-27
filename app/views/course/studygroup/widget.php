<article class="studip connectedcourses_widget">
    <header>
        <h1>

            <? if ($course->isStudygroup()) : ?>
                <?= Icon::create('seminar', Icon::ROLE_INFO)->asimg(['class' => "text-bottom"]) ?>
                <?= _('Zugehörige Veranstaltung') ?>
            <? else : ?>
                <?= Icon::create('studygroup', Icon::ROLE_INFO)->asimg(['class' => "text-bottom"]) ?>
                <?= _('Verknüpfte Studiengruppen') ?>
            <? endif ?>
        </h1>

    </header>

    <section>
        <? if ($course->isStudygroup()) : ?>
            <ul>
            <? foreach ($connections as $connection) : ?>
                <li>
                    <? $link = $connection->course->isAccessibleToUser()
                        ? URLHelper::getLink('seminar_main.php', ['auswahl' => $connection->course->id])
                        : URLHelper::getLink('dispatch.php/course/details', ['cid' => $connection->course->id]) ?>
                    <a href="<?= $link ?>">
                        <?= htmlReady($connection->course->getFullname()) ?>
                    </a>
                </li>
            <? endforeach ?>
            </ul>
        <? else : ?>
            <table class="default">
                <colgroup>
                    <col style="width: 60px;">
                </colgroup>
                <thead>
                    <tr>
                        <th><?= _('Avatar') ?></th>
                        <th><?= _('Name / Beschreibung') ?></th>
                        <th><?= _('Mitglieder') ?></th>
                        <th><?= _('Gründer:in') ?></th>
                    </tr>
                </thead>
                <tbody>
                <? foreach ($connections as $connection) : ?>
                    <tr>
                        <td>
                            <? $link = $connection->studygroup->isAccessibleToUser()
                                ? URLHelper::getLink('seminar_main.php', ['auswahl' => $connection->studygroup->id])
                                : URLHelper::getLink('dispatch.php/course/studygroup/details/'.$connection->studygroup->id) ?>
                            <a href="<?= $link ?>">
                                <?= CourseAvatar::getAvatar($connection->studygroup->id)->getImageTag(Avatar::SMALL) ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= $link ?>">
                                <?= htmlReady($connection->studygroup->getFullname()) ?>
                            </a>
                            <? if ($connection->studygroup->beschreibung) : ?>
                            <div>
                                <?= htmlReady($connection->studygroup->beschreibung) ?>
                            </div>
                            <? endif ?>
                        </td>
                        <td>
                            <?= count($connection->studygroup->members) ?>
                        </td>
                        <td>
                            <?
                            $founders = $connection->studygroup->members->filter(function ($m) { return $m['status'] === 'dozent'; });
                            foreach ($founders as $index => $founder) : ?>
                                <? if ($index > 0) : ?>
                                ,
                                <? endif ?>
                                <a href="<?= URLHelper::getLink('dispatch.php/profile', ['username' => $founder->user->username]) ?>">
                                    <?= Avatar::getAvatar($founder->user->id)->getImageTag(Avatar::SMALL) ?>
                                    <?= htmlReady($founder->user->getFullname()) ?>
                                </a>
                            <? endforeach ?>
                        </td>
                    </tr>
                <? endforeach ?>
                </tbody>
            </table>
        <? endif ?>

    </section>

</article>
