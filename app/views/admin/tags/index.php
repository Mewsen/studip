<?php
/**
 * @var Admin_TagsController $controller
 * @var Tag[] $tags
 * @var integer $all_tags
 * @var integer $page
 * */
?>
<table class="default">
    <caption>
        <?= _('Schlagwörter') ?>
        <span class="actions">
            <?= sprintf(_('%s Schlagwörter'), $all_tags) ?>
        </span>
    </caption>
    <thead>
        <tr>
            <th><?= _('Schlagwort') ?></th>
            <th><?= _('Verknüpfte Objekte') ?></th>
            <th><?= _('Aktiv') ?></th>
            <th class="actions">
                <?= _('Aktion') ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <? foreach ($tags as $tag) : ?>
        <tr>
            <td>
                <?= htmlReady($tag['name']) ?>
            </td>
            <td>
                <a href="<?= URLHelper::getLink('dispatch.php/admin/tags/view_objects/'.$tag->id) ?>" data-dialog>
                    <?= TagRelation::countBySql('`tag_id` = ?', [$tag->id]) ?>
                </a>
            </td>
            <td>
                <?= $tag['active']
                    ? Icon::create('checkbox-checked', Icon::ROLE_INFO)
                    : Icon::create('checkbox-unchecked', Icon::ROLE_INFO) ?>
            </td>
            <td class="actions">
                <a href="<?= $controller->edit($tag) ?>" data-dialog>
                    <?= Icon::create('edit') ?>
                </a>
            </td>
        </tr>
        <? endforeach ?>
        <? if (count($tags) === 0) : ?>
        <tr>
            <td colspan="2">
                <?= _('Noch keine Schlagwörter vorhanden.') ?>
            </td>
        </tr>
        <? endif ?>
    </tbody>

    <tfoot>
    <tr>
        <td colspan="4" class="actions">
            <?= Pagination::create($all_tags, $page)->asLinks() ?>
        </td>
    </tr>
    </tfoot>
</table>
