<?php
/**
 * @var Vips_SheetsController $controller
 * @var VipsAssignment $assignment
 * @var int $assignment_id
 * @var int $position
 * @var string $solver_id
 * @var Exercise $item
 */
?>
<a href="<?= $controller->link_for('vips/sheets/show_exercise', ['assignment_id' => $assignment_id, 'exercise_id' => $item->task_id, 'solver_id' => $solver_id]) ?>">
    <div class="sidebar_exercise_label">
        <?= sprintf(_('Aufgabe %d'), $position) ?>
    </div>
    <div class="sidebar_exercise_points">
        <?= sprintf(_('%g Punkte'), $item->points) ?>
    </div>
    <div class="sidebar_exercise_state">
        <? if ($assignment->getSolution($solver_id, $item->task_id)): ?>
            <?= Icon::create('accept', Icon::ROLE_STATUS_GREEN)->asSvg(['title' => _('Aufgabe bearbeitet')]) ?>
        <? endif ?>
    </div>
</a>
