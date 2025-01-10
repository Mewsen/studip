<?php
/**
 * @var StudygroupController $controller
 * @var int $anzahl
 * @var string $sort_type
 * @var string $sort_order
 * @var string $q
 * @var string $closed
 * @var array $groups
 * @var User $user
 * @var int $entries_per_page
 * @var int $page
 * @var string $sort
 */
?>

<?= $this->render_partial("course/studygroup/_feedback") ?>

<?php
$headers = [
    'name'              => _('Name'),
    'tags'              => _('Schlagwörter'),
    'last_activity'     => _('Letzte Aktivität'),
    'member'            => _('Mitglieder'),
    'founder'           => _('Gründer:in')
];
?>

<? if ($anzahl > 0): ?>
    <table class="default studygroup-browse sortable-table" data-sortlist="[[3, 1]]">
        <caption>
            <?= sprintf(ngettext('%u Studiengruppe', '%u Studiengruppen', $anzahl), $anzahl)?>
        </caption>
        <colgroup>
            <col style="width: 32px">
            <col>
            <col style="width: 10%">
            <col style="width: 10%">
            <col style="width: 10%">
            <col style="width: 20%">
        </colgroup>
        <thead>
            <tr class="sortable" title="<?= _('Klicken, um die Sortierung zu ändern') ?>">
                <th class="nosort hidden-small-down"></th>
                <? foreach ($headers as $key => $label): ?>
                    <? if ($key !== 'last_activity' && $key !== 'tags') : ?>
                        <th <? if ($sort_type === $key) echo 'class="sort' . $sort_order . '"'; ?>>
                            <a href="<?= $controller->link_for("studygroup/browse/1/{$key}_" . ($sort_order === 'asc' ? 'desc' : 'asc'), compact('q', 'closed')) ?>">
                                <?= htmlReady($label) ?>
                            </a>
                        </th>
                    <? elseif($key !== 'tags') : ?>
                        <th data-sort="htmldata">
                            <?= htmlReady($label) ?>
                        </th>
                    <? else : ?>
                        <th>
                            <?= htmlReady($label) ?>
                        </th>
                    <? endif; ?>
                <? endforeach; ?>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <? foreach ($groups as $group): ?>
            <? $is_member = $user->course_memberships->findBy('seminar_id', $group['Seminar_id'])->count(); ?>
            <tr>
                <td class="hidden-small-down">
                    <?= StudygroupAvatar::getAvatar($group['Seminar_id'])->getImageTag(Avatar::SMALL, ['title' => $group['Name']]) ?>
                </td>
                <td class="studygroup-title">
                    <? if ($is_member): ?>
                    <a href="<?= URLHelper::getlink("dispatch.php/course/go?to=" . $group['Seminar_id']) ?>">
                        <? else: ?>
                        <a href="<?= URLHelper::getlink("dispatch.php/course/studygroup/details/" . $group['Seminar_id'], ['cid' => null]) ?>">
                            <? endif; ?>
                            <?= htmlready($group['Name']) ?>
                            <?= $group['visible'] ? '' : "[" . _('versteckt') . "]" ?>
                            <? if ($group['admission_prelim'] == 1) { ?>
                                <?= Icon::create('lock-locked', Icon::ROLE_INACTIVE, ['title' => _('Mitgliedschaft muss beantragt werden')]) ?>
                            <? } ?>
                        </a>
                </td>
                <td>
                    <? foreach ($group['course']->tags as $tag) : ?>
                        <a href="<?= $controller->browse(['q' => $tag['name']]) ?>">
                            <?= htmlReady('#'.$tag['name']) ?>
                        </a>
                    <? endforeach ?>
                </td>
                <td data-sort-value="<?= htmlReady($group['last_visit_date']) ?>">
                    <?= htmlReady(date('d.m.Y', $group['last_visit_date'])) ?>
                </td>
                <td align="center">
                    <?= StudygroupModel::countMembers($group['Seminar_id']) ?>
                </td>
                <td style="white-space:nowrap;">
                    <? $founders = StudygroupModel::getFounder($group['Seminar_id']);
                    foreach ($founders as $founder) : ?>
                        <?= Avatar::getAvatar($founder['user_id'])->getImageTag(Avatar::SMALL, [
                            'class' => 'hidden-small-down',
                        ]) ?>
                        <a href="<?= URLHelper::getlink('dispatch.php/profile', ['username' => $founder['uname']]) ?>">
                            <?= htmlready($founder['fullname']) ?>
                        </a>
                        <br>
                    <? endforeach; ?>
                </td>
            </tr>
        <? endforeach; ?>
        </tbody>
    <? if ($anzahl > $entries_per_page) : ?>
        <tfoot>
            <tr>
                <td colspan="6" class="actions">
                    <?= $GLOBALS['template_factory']->render('shared/pagechooser', [
                        'perPage'      => $entries_per_page,
                        'num_postings' => $anzahl,
                        'page'         => $page,
                        'pagelink'     => "dispatch.php/studygroup/browse/%s/{$sort}",
                        'pageparams'   => compact('q', 'closed'),
                    ]) ?>
                </td>
            </tr>
        </tfoot>
    <? endif; ?>
    </table>
<? endif; ?>

<?= \Studip\LinkButton::createAdd(
    _('Neue Studiengruppe anlegen'),
    URLHelper::getURL('dispatch.php/course/wizard', ['studygroup' => 1]),
    ['class' => 'hidden-medium-up']
) ?>
