<? if (count($connected) + count($proposals) > 0) : ?>
    <? if (count($connected) > 0) : ?>
        <form method="post">
            <table class="default">
                <caption>
                    <?= _('Verknüpfte Studiengruppen') ?>
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
                                <a href="<?= URLHelper::getLink('dispatch.php/course/studygroup/details/' . $connection['studygroup_id'], [], true) ?>">
                                    <?= StudygroupAvatar::getAvatar($connection['studygroup_id'])->getImageTag(Avatar::SMALL) ?>
                                    <?= htmlReady($connection->studygroup->getFullName()) ?>
                                </a>
                            </td>
                            <td class="actions">
                                <?= CSRFProtection::tokenTag() ?>
                                <?= Icon::create('trash')->asInput([
                                    'title'        => _('Verknüpfung aufheben'),
                                    'data-confirm' => _('Wirklich die Zuweisung zu der Studiengruppe aufheben?'),
                                    'formaction'   => $controller->removeURL($connection['studygroup_id'])
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
            <table class="default">
                <?= CSRFProtection::tokenTag() ?>
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
                            <a href="<?= URLHelper::getLink('dispatch.php/course/studygroup/details/' . $proposal['studygroup_id']) ?>" target="_blank">
                                <?= StudygroupAvatar::getAvatar($proposal['studygroup_id'])->getImageTag(Avatar::SMALL) ?>
                                <?= htmlReady($proposal->studygroup->getFullName()) ?>
                            </a>
                        </td>
                        <td>
                            <?= htmlReady($proposal->user->getFullName()) ?>
                        </td>
                        <td class="actions">
                            <? if ($proposal['proposed_from'] === 'studygroup') : ?>
                                <?= Icon::create('accept')->asInput([
                                    'title'        => _('Vorschlag annehmen'),
                                    'data-confirm' => _('Wirklich die Studiengruppe mit dieser Veranstaltung verknüpfen?'),
                                    'formaction'   => $controller->connectURL($proposal['studygroup_id'])
                                ]) ?>
                            <? endif ?>
                            <? if ($proposal['proposed_from'] === 'studycourse') : ?>
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
                <header><?= _('Verknüpfung zu Studiengruppen') ?></header>
                <?= _('Verknüpfen Sie Studiengruppen, die sich mit den Inhalten dieser Veranstaltung beschäftigen.') ?>
            </div>

    <?= \Studip\LinkButton::create(
        _('Verknüpfung zu Studiengruppe vorschlagen'),
        $controller->connect(),
        ['data-dialog' => 1]
    )?>
        </div>
    </div>

<? endif ?>
