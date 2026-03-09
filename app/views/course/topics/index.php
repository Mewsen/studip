<?php
/**
 * @var CourseTopic[] $topics
 * @var Course_TopicsController $controller
 * @var array<array{next: ?CourseTopic, previous: ?CourseTopic}> $topic_links
 */

use Studip\Button;

?>
<? if (count($topics) > 0) : ?>
    <form method="POST">
        <?= CSRFProtection::tokenTag() ?>
        <table class="default" id="topics_index_table">
            <thead>
                <tr>
                    <th scope="col" style="width: 20px">
                        <input
                            type="checkbox"
                            data-proxyfor="#topics_index_table tbody input[type=checkbox]"
                            data-activates="#topics_index_table tfoot button"
                            aria-label="<?= _('Alle Themen auswählen') ?>"
                        >
                    </th>
                    <th scope="col"><?= _('Thema') ?></th>
                    <th scope="col" class="responsive-hidden"><?= _('Termine') ?></th>
                    <th scope="col"><?= _('Materialien') ?></th>
                    <th scope="col" class="responsive-hidden"><?= _('Beschreibung') ?></th>
                    <? if ($is_tutor = User::findCurrent()->hasPermissionLevel('tutor', Context::get())) : ?>
                        <th scope="col" class="actions"><?= _('Aktionen') ?></th>
                    <? endif ?>
                </tr>
            </thead>
            <tbody>
            <? foreach ($topics as $topic) : ?>
                <tr>
                    <td>
                        <input type="checkbox" value="<?= htmlReady($topic->id) ?>" name="topics[]"
                               aria-label="<?= sprintf(_('Thema %s auswählen'), htmlReady($topic->title)) ?>">
                    </td>
                    <td>
                        <a
                            href="<?= URLHelper::getLink('dispatch.php/course/topics/details/' . $topic->id) ?>"
                            title=" <?= sprintf(_('Thema %s öffnen'), htmlReady($topic->title)) ?>"
                            aria-label="<?= sprintf(_('Thema %s öffnen'), htmlReady($topic->title)) ?>"
                            data-dialog="size=auto"
                        >
                            <?= htmlReady($topic['title']) ?>
                        </a>
                        <? if ($topic->paper_related): ?>
                            <?= Icon::create('info-circle')->asImg(array_merge(
                                tooltip2(_('Thema behandelt eine Hausarbeit oder ein Referat'))
                            )) ?>
                        <? endif ?>
                    </td>
                    <td class="responsive-hidden">
                        <?= $this->render_partial('course/topics/_dates.php', ['topic' => $topic]) ?>
                    </td>
                    <td>
                        <?= $this->render_partial('course/topics/_material.php', ['topic' => $topic]) ?>
                    </td>
                    <td class="responsive-hidden">
                        <?= formatReady($topic['description']) ?>
                    </td>
                    <? if ($is_tutor) : ?>
                        <td class="actions">
                            <div>
                                <? $move_up_label = sprintf(_('%s nach oben verschieben'), htmlReady($topic->title));
                                if ($topic_links[$topic->id]['previous']) : ?>
                                    <button
                                        class="as-link"
                                        formaction="<?= $controller->swap($topic, $topic_links[$topic->id]['previous']) ?>"
                                        aria-label="<?= $move_up_label ?>"
                                        title="<?= $move_up_label ?>"
                                    >
                                        <?= Icon::create('arr_2up') ?>
                                    </button>
                                <? else : ?>
                                    <?= Icon::create('arr_2up', Icon::ROLE_INACTIVE) ?>
                                <? endif ?>
                                <? $move_down_label = sprintf(_('%s nach unten verschieben'), htmlReady($topic->title));
                                if ($topic_links[$topic->id]['next']) : ?>
                                    <button
                                        class="as-link"
                                        formaction="<?= $controller->swap($topic, $topic_links[$topic->id]['next']) ?>"
                                        aria-label="<?= $move_down_label ?>"
                                        title="<?= $move_down_label ?>"
                                    >
                                        <?= Icon::create('arr_2down') ?>
                                    </button>
                                <? else : ?>
                                    <?= Icon::create('arr_2down', Icon::ROLE_INACTIVE) ?>
                                <? endif ?>
                                <?= $controller->getActionMenu($topic) ?>
                            </div>
                        </td>
                    <? endif ?>
                </tr>
            <? endforeach ?>
            </tbody>
            <? if ($is_tutor) : ?>
                <tfoot>
                <tr>
                    <td colspan="6">
                        <? if ($documents_activated) : ?>
                            <?= Button::create(_('Dateiordner anlegen'), 'bulk_folder', [
                                'formaction' => $controller->bulkURL('folder'),
                                'data-confirm' => _('Sind Sie sicher, dass Sie für Ihre Auswahl je einen Dateiordner anlegen wollen?'),
                            ]) ?>
                        <? endif ?>
                        <? if ($forum_activated) : ?>
                            <?= Button::create(_('Forumsthema anlegen'), 'bulk_ftopic', [
                                'formaction' => $controller->bulkURL('ftopic'),
                                'data-confirm' => _('Sind Sie sicher, dass Sie für Ihre Auswahl je ein Forumsthema anlegen wollen?'),
                            ]) ?>
                        <? endif ?>
                        <?= Button::create(_('Löschen'), 'bulk_delete', [
                            'formaction' => $controller->bulkURL('delete'),
                            'data-confirm' => _('Sind Sie sicher, dass Sie Ihre Auswahl löschen wollen?'),
                        ]) ?>
                    </td>
                </tr>
                </tfoot>
            <? endif ?>
        </table>
    </form>
<? else : ?>
    <?= MessageBox::info(_('Keine Themen vorhanden.')) ?>
<? endif ?>
