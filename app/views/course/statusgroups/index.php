<?php
/**
 * @var Course_StatusgroupsController $controller
 * @var bool $is_tutor
 * @var bool $is_locked
 * @var array $groups
 * @var bool $open_groups
 * @var string $order
 * @var string $sort_by
 * @var int $ungrouped_count
 */
?>
<form action="<?= $controller->batch_action() ?>" method="post">
<section class="contentbox course-statusgroups" <? if ($is_tutor && !$is_locked) echo 'data-sortable="' . $controller->order() . '"'; ?>>
    <header>
        <h1><?= _('Teilnehmende nach Gruppen') ?></h1>
    </header>
    <?php foreach ($groups as $group) : ?>
        <?= $this->render_partial('course/statusgroups/_group', [
            'group'       => $group['group'],
            'membercount' => $group['membercount'],
            'members'     => $group['members'],
            'joinable'    => $group['joinable'],
            'invisible'   => $group['invisible_users'],
            'load'        => $open_groups ? true : $group['load'] ?? true,
            'order'       => $order,
            'sort_by'     => $sort_by,
            'open_group'  => $open_groups,
        ]) ?>
    <?php endforeach ?>
    <?php if ((count($groups) > $ungrouped_count ? 2 : 1) && $is_tutor && !$is_locked) : ?>
        <footer>
            <div class="groupselection">
                <label>
                    <input aria-label="<?= sprintf(_('Alle Gruppen auswählen')) ?>"
                           type="checkbox" name="allgroups" value="1"
                           data-proxyfor=":checkbox.groupselector"
                           data-activates="select#batch-groups-action,#batch-groups-submit">
                    <?= _('Alle Gruppen auswählen') ?>
                </label>
            </div>
            <div class="groupactions">
                <label>
                    <select name="groups_action" id="batch-groups-action">
                        <option value="edit_size"><?= _('Gruppengröße bearbeiten') ?></option>
                        <option value="edit_selfassign"><?= _('Selbsteintrag bearbeiten') ?></option>
                        <option value="write_message"><?= _('Nachricht schreiben') ?></option>
                        <option value="copy"><?= _('In andere Veranstaltung kopieren') ?></option>
                        <option value="delete"><?= _('Löschen') ?></option>
                    </select>
                </label>
                <?= Studip\Button::create(_('Ausführen'), 'batch_groups', [
                    'data-dialog' => 'size=auto',
                    'id'          => 'batch-groups-submit',
                ]) ?>
            </div>
        </footer>
    <?php endif ?>
</section>
