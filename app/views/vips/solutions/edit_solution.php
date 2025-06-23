<?php
/**
 * @var Vips_SolutionsController $controller
 * @var int $assignment_id
 * @var VipsAssignment $assignment
 * @var string $view
 * @var int $exercise_id
 * @var string $solver_or_group_id
 * @var string $solver_name
 * @var string $solver_id
 * @var Exercise $exercise
 * @var VipsSolution $solution
 * @var float $max_points
 */
?>
<? /* breadcrumb navigation */ ?>
<div class="breadcrumb width-1200">
    <? /* overview */ ?>
    <a href="<?= $controller->assignment_solutions(['assignment_id' => $assignment_id, 'view' => $view]) ?>">
        <?= htmlReady($assignment->test->title) ?>
    </a>

    &nbsp;/&nbsp;

    <? /* previous solver */ ?>
    <? if (isset($prev_solver)): ?>
        <a href="<?= $controller->edit_solution(['assignment_id' => $assignment_id, 'exercise_id' => $exercise_id, 'solver_id' => $prev_solver['user_id'], 'view' => $view]) ?>">
            <?= Icon::create('arr_1left')->asImg(['title' => _('Voriger Teilnehmer / vorige Teilnehmerin')]) ?>
        </a>
    <? else: ?>
        <?= Icon::create('arr_1left', Icon::ROLE_INACTIVE)->asImg(['title' => _('Keiner der vorhergehenden Teilnehmenden hat diese Aufgabe bearbeitet')]) ?>
    <? endif ?>

    <? /* overview */ ?>
    <a href="<?= $controller->assignment_solutions(['assignment_id' => $assignment_id, 'expand' => $solver_or_group_id, 'view' => $view]) ?>#row_<?= $solver_or_group_id ?>">
        <?= htmlReady($solver_name) ?>
    </a>

    <? /* next solver */ ?>
    <? if (isset($next_solver)): ?>
        <a href="<?= $controller->edit_solution(['assignment_id' => $assignment_id, 'exercise_id' => $exercise_id, 'solver_id' => $next_solver['user_id'], 'view' => $view]) ?>">
            <?= Icon::create('arr_1right')->asImg(['title' => _('Nächster Teilnehmer / nächste Teilnehmerin')]) ?>
        </a>
    <? else: ?>
        <?= Icon::create('arr_1right', Icon::ROLE_INACTIVE)->asImg(['title' => _('Keiner der nachfolgenden Teilnehmenden hat diese Aufgabe bearbeitet')]) ?>
    <? endif ?>

    &nbsp;/&nbsp;

    <? /* previous exercise */ ?>
    <? if (isset($prev_exercise)): ?>
        <a href="<?= $controller->edit_solution(['assignment_id' => $assignment_id, 'exercise_id' => $prev_exercise['id'], 'solver_id' => $solver_id, 'view' => $view]) ?>">
            <?= Icon::create('arr_1left')->asImg(['title' => _('Vorige Aufgabe')]) ?>
        </a>
    <? else: ?>
        <?= Icon::create('arr_1left', Icon::ROLE_INACTIVE)->asImg(['title' => _('Die teilnehmende Person hat keine der vorhergehenden Aufgaben bearbeitet')]) ?>
    <? endif ?>

    <? /* exercise name */ ?>
    <?= htmlReady($exercise->title) ?>

    <? /* next exercise */ ?>
    <? if (isset($next_exercise)): ?>
        <a href="<?= $controller->edit_solution(['assignment_id' => $assignment_id, 'exercise_id' => $next_exercise['id'], 'solver_id' => $solver_id, 'view' => $view]) ?>">
            <?= Icon::create('arr_1right')->asImg(['title' => _('Nächste Aufgabe')]) ?>
        </a>
    <? else: ?>
        <?= Icon::create('arr_1right', Icon::ROLE_INACTIVE)->asImg(['title' => _('Die teilnehmende Person hat keine der nachfolgenden Aufgaben bearbeitet')]) ?>
    <? endif ?>
</div>

<form class="default width-1200" action="<?= $controller->store_correction() ?>" data-secure method="POST" enctype="multipart/form-data">
    <?= CSRFProtection::tokenTag() ?>
    <input type="hidden" name="solution_id" value="<?= $solution->id ?>">
    <input type="hidden" name="exercise_id" value="<?= $exercise_id ?>">
    <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">
    <input type="hidden" name="solver_id" value="<?= htmlReady($solver_id) ?>">
    <input type="hidden" name="view" value="<?= htmlReady($view) ?>">
    <input type="hidden" name="max_points" value="<?= $max_points ?>">

    <?= Studip\Button::createAccept(_('Speichern'), 'store_solution', ['style' => 'display: none;']) ?>

    <?= $this->render_partial('vips/exercises/correct_exercise') ?>

    <fieldset>
        <legend>
            <?= sprintf(_('Bewertung der Lösung von „%s“'), htmlReady($solver_name)) ?>
            <div style="float: right;">
                <? if (isset($solution->grader_id)): ?>
                    <?= _('Manuell korrigiert') ?>
                <? elseif ($solution->state): ?>
                    <?= _('Automatisch korrigiert') ?>
                <? elseif ($solution->id): ?>
                    <?= _('Unkorrigiert') ?>
                <? else: ?>
                    <?= _('Nicht abgegeben') ?>
                <? endif ?>
            </div>
        </legend>

        <? if ($solution->isArchived()): ?>
            <? if ($solution->feedback) : ?>
                <div class="label-text">
                    <?= _('Anmerkungen zur Lösung') ?>
                </div>
                <div class="vips_output">
                    <?= formatReady($solution->feedback) ?>
                </div>
            <? endif ?>

            <?= $this->render_partial('vips/solutions/feedback_files_table') ?>

            <div class="description">
                <?= sprintf(_('Vergebene Punkte: %g von %g'), $solution->points, $max_points) ?>
            </div>
        <? else: ?>
            <label>
                <?= _('Anmerkungen zur Lösung') ?>
                <textarea name="feedback" class="character_input size-l wysiwyg"><?= wysiwygReady($solution->feedback) ?></textarea>
            </label>

            <table class="default">
                <? if ($solution->feedback_folder && count($solution->feedback_folder->file_refs)): ?>
                    <thead>
                        <tr>
                            <th style="width: 50%;">
                                <?= _('Dateien zur Korrektur') ?>
                            </th>
                            <th style="width: 10%;">
                                <?= _('Größe') ?>
                            </th>
                            <th style="width: 20%;">
                                <?= _('Autor/-in') ?>
                            </th>
                            <th style="width: 15%;">
                                <?= _('Datum') ?>
                            </th>
                            <th class="actions">
                                <?= _('Aktionen') ?>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="dynamic_list">
                        <? foreach ($solution->feedback_folder->file_refs as $file_ref): ?>
                            <tr class="dynamic_row">
                                <td>
                                    <input type="hidden" name="file_ids[]" value="<?= htmlReady($file_ref->id) ?>">
                                    <a href="<?= htmlReady($file_ref->getDownloadURL()) ?>">
                                        <?= Icon::create('file')->asImg(['title' => _('Datei herunterladen')]) ?>
                                        <?= htmlReady($file_ref->name) ?>
                                    </a>
                                </td>
                                <td>
                                    <?= relsize($file_ref->file->size) ?>
                                </td>
                                <td>
                                    <?= htmlReady(get_fullname($file_ref->file->user_id, 'no_title')) ?>
                                </td>
                                <td>
                                    <?= date('d.m.Y, H:i', $file_ref->file->mkdate) ?>
                                </td>
                                <td class="actions">
                                    <?= Icon::create('trash')->asInput(['class' => 'delete_dynamic_row', 'title' => _('Datei löschen')]) ?>
                                </td>
                            </tr>
                        <? endforeach ?>
                    </tbody>
                <? endif ?>

                <tfoot>
                    <tr>
                        <td colspan="5">
                            <?= Studip\Button::create(_('Dateien zur Korrektur hochladen'), '', ['class' => 'vips_file_upload', 'data-label' => _('%d Dateien ausgewählt')]) ?>
                            <span class="file_upload_hint" style="display: none;"><?= _('Klicken Sie auf „Speichern“, um die gewählten Dateien hochzuladen.') ?></span>
                            <?= tooltipIcon(sprintf(_('max. %g MB pro Datei'), FileManager::getUploadTypeConfig($assignment->range_id)['file_size'] / 1048576)) ?>
                            <input class="file_upload attach" style="display: none;" type="file" name="upload[]" multiple>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <label>
                <span class="required"><?= sprintf(_('Vergebene Punkte (von %g)'), $max_points) ?></span>
                <input name="reached_points" type="text" class="size-s" pattern="-?[0-9,.]+" data-message="<?= _('Bitte geben Sie eine Zahl ein') ?>"
                       value="<?= isset($solution->points) ? sprintf('%g', $solution->points) : '' ?>" required>
            </label>
        <? endif ?>
    </fieldset>

    <footer>
        <? if ($solution->isArchived()): ?>
            <?= Studip\Button::create(_('Als aktuelle Lösung speichern'), 'restore_solution', ['formaction' => $controller->url_for('vips/solutions/restore_solution')]) ?>
        <? else: ?>
            <?= Studip\Button::createAccept(_('Speichern'), 'store_solution') ?>
        <? endif ?>

        <label style="float: right; margin-top: 0.5ex;">
            <input type="checkbox" name="corrected" value="1" <?= !$solution->grader_id || $solution->state ? 'checked' : ''?>>
            <?= _('Lösung als korrigiert markieren') ?>
        </label>
    </footer>
</form>
