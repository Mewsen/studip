<?php
/**
 * @var Course_Forum_DiscussionTypesController $controller
 * @var ForumDiscussionType[] $discussion_types
 */

use Forum\ForumDiscussionType;

?>

<div class="forum">
    <table class="default sortable-table">
        <caption>
            <?= _('Diskussionstyps') ?>
            <span class="actions">
                <a href="<?= $controller->url_for('course/forum/discussion_types/edit') ?>" data-dialog="width=700">
                    <?= Icon::create('add', 'clickable', ['title' => _('Neue Diskussionstyp anlegen')]) ?>
                </a>
            </span>
        </caption>

        <colgroup>
            <col style="width: 10%">
            <col>
            <col style="width: 24px">
        </colgroup>

        <thead>
            <tr>
                <th><?= _('Icon') ?></th>
                <th data-sort="text"><?= _('Name') ?></th>
                <th class="actions"><?= _('Aktionen') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($discussion_types as $type) : ?>
            <tr>
                <td>
                    <?php if ($type->icon) : ?>
                        <?= Icon::create($type->icon, ['title' => htmlReady($type->icon)])->asSvg(24) ?>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?= $controller->url_for('course/forum/discussion_types/show/'.$type->type_id) ?>">
                        <?= htmlReady($type->name) ?>
                    </a>
                </td>

                <td class="actions">
                    <?= ActionMenu::get()
                        ->addLink(
                            $controller->url_for('course/forum/discussion_types/edit', $type),
                            _('Bearbeiten'),
                            Icon::create('edit', 'clickable', ['title' => _('Diskussionstyp bearbeiten')]),
                            ['data-dialog' => 'width=700']
                        )
                        ->addLink(
                            $controller->url_for('course/forum/discussion_types/delete', $type),
                            _('Löschen'),
                            Icon::create('trash', 'clickable',['title' => _('Diskussionstyp löschen')]),
                            ['data-confirm' => sprintf(
                                _('Wollen Sie "%s" löschen?'),
                                $type->name
                            )]
                        );
                    ?>
                </td>
            </tr>
        <?php endforeach ?>

        <?php if (count($discussion_types) === 0) : ?>
            <tr>
                <td colspan="3" class="text-center">
                    <?= _('Es sind noch keine Diskussionstypen vorhanden.') ?>
                </td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
</div>
