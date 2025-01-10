<? if (count($connected) + count($proposals) > 0) : ?>
    <? if (count($connected) > 0) : ?>
        <form method="post">
            <?= CSRFProtection::tokenTag() ?>
            <table class="default">
                <caption>
                    <?= _('Verknüpfte Veranstaltungen') ?>
                    <thead>
                    <tr>
                        <th><?= _('Name') ?></th>
                        <th class="actions"><?= _('Aktion') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach ($connected as $connection) : ?>
                        <tr>
                            <td>
                                <a href="<?= URLHelper::getLink('dispatch.php/course/details/' . $connection['course_id']) ?>" target="_blank">
                                    <?= CourseAvatar::getAvatar($connection['course_id'])->getImageTag(Avatar::SMALL) ?>
                                    <?= htmlReady($connection->course->getFullName()) ?>
                                </a>
                            </td>
                            <td class="actions">
                                <?= Icon::create('trash')->asInput([
                                    'title' => _('Verknüpfung aufheben'),
                                    'data-confirm' => _('Wirklich die Zuweisung zu der Veranstaltung aufheben?'),
                                    'formaction' => $controller->url_for('course/connectedcourses/remove/'.$connection['course_id'])
                                ]) ?>
                            </td>
                        </tr>
                    <? endforeach ?>
                    </tbody>
                </caption>
            </table>
        </form>
    <? endif ?>

    <? if (count($proposals) > 0) : ?>
        <form method="post">
            <?= CSRFProtection::tokenTag() ?>
            <table class="default">
                <caption>
                    <?= _('Eingereichte Vorschläge') ?>
                </caption>
                <thead>
                <tr>
                    <th><?= _('Name') ?></th>
                    <th><?= _('Vorgeschlagen von') ?></th>
                    <th class="actions"><?= _('Aktion') ?></th>
                </tr>
                </thead>
                <tbody>
                <? foreach ($proposals as $proposal) : ?>
                    <tr>
                        <td>
                            <a href="<?= URLHelper::getLink('dispatch.php/course/details/' . $connection['course_id']) ?>" target="_blank">
                                <?= CourseAvatar::getAvatar($proposal['course_id'])->getImageTag(Avatar::SMALL) ?>
                                <?= htmlReady($proposal->course->getFullName()) ?>
                            </a>
                        </td>
                        <td>
                            <?= htmlReady($proposal->user->getFullName()) ?>
                        </td>
                        <td class="actions">
                            <? if ($proposal['proposed_from'] === 'course') : ?>
                                <?= Icon::create('accept')->asInput([
                                    'title'        => _('Vorschlag annehmen'),
                                    'data-confirm' => _('Wirklich die Veranstaltung mit dieser Studiengruppe verknüpfen?'),
                                    'formaction'   => $controller->connectURL($proposal['course_id'])
                                ]) ?>
                            <? endif ?>
                            <? if ($proposal['proposed_from'] === 'course') : ?>
                                <?= Icon::create('decline')->asInput([
                                    'title'        => _('Vorschlag ablehnen'),
                                    'data-confirm' => _('Wirklich den Vorschlag ablehnen?'),
                                    'formaction'   => $controller->declineURL($proposal->id)
                                ]) ?>
                            <? else : ?>
                                <?= Icon::create('decline')->asInput([
                                    'title'        => _('Vorschlag zurückziehen'),
                                    'data-confirm' => _('Wirklich den Vorschlag zurückziehen?'),
                                    'formaction'   => $controller->declineURL($proposal->id)
                                ]) ?>
                            <? endif ?>
                        </td>
                    </tr>
                <? endforeach ?>
                </tbody>
            </table>
        </form>
    <? endif ?>

<? else : ?>

    <div class="studip-contents-overview-teaser">
        <div class="teaser-content">

            <div>
                <header><?= _('Verknüpfung zu Lehrveranstaltungen') ?></header>
                <?= _('Verknüpfen Sie diese Studiengruppen mit Lehrveranstaltungen, mit deren Inhalten sich diese Studiengruppe beschäftigt. Dadurch machen Sie diese Studiengruppe sichtbarer für andere Teilnehmende der Veranstaltung.') ?>
            </div>

            <?= \Studip\LinkButton::create(
                _('Verknüpfung zu Lehrveranstaltung vorschlagen'),
                $controller->connect(),
                ['data-dialog' => 1]
             )?>
        </div>
    </div>

<? endif ?>

