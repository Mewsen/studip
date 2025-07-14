<?php
/**
 * @var Vips_SheetsController $controller
 * @var int $assignment_id
 * @var array $search_filter
 * @var array $exercise_types
 * @var string $sort
 * @var bool $desc
 * @var int $count
 * @var Exercise[] $exercises
 * @var int $page
 * @var int $size
 *
 */
?>
<form class="default" action="<?= $controller->copy_exercise() ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">
    <input type="hidden" name="sort" value="<?= htmlReady($sort) ?>">
    <input type="hidden" name="desc" value="<?= htmlReady($desc) ?>">

    <input type="text" name="search_filter[search_string]" value="<?= htmlReady($search_filter['search_string']) ?>" aria-label="<?= _('Suchbegriff eingeben') ?>"
           placeholder="<?= _('Titel der Aufgabe oder Veranstaltung') ?>" style="max-width: 24em;">

    <select name="search_filter[exercise_type]" class="inline_select" aria-label="<?= _('Aufgabentyp auswählen') ?>">
        <option value="">
            <?= _('Alle Aufgabentypen') ?>
        </option>
        <? foreach ($exercise_types as $type => $entry): ?>
            <option value="<?= $type ?>" <?= $search_filter['exercise_type'] == $type ? 'selected' : '' ?>>
                <?= htmlReady($entry['name']) ?>
            </option>
        <? endforeach ?>
    </select>

    <select name="search_filter[range_type]" class="inline_select" aria-label="<?= _('Quelle auswählen') ?>" style="margin-left: 1em;">
        <option value="user" <?= $search_filter['range_type'] == 'user' ? 'selected' : '' ?>>
            <?= _('Persönliche Aufgabensammlung') ?>
        </option>
        <option value="course" <?= $search_filter['range_type'] == 'course' ? 'selected' : '' ?>>
            <?= _('Aufgaben in Veranstaltungen') ?>
        </option>
    </select>

    <span style="margin-left: 1em;">
        <?= Studip\Button::create(_('Suchen'), 'start_search', ['data-dialog' => 'size=big', 'formaction' => $controller->url_for('vips/sheets/copy_exercise_dialog')]) ?>
        <?= Studip\Button::create(_('Zurücksetzen'), 'reset_search', ['data-dialog' => 'size=big', 'formaction' => $controller->url_for('vips/sheets/copy_exercise_dialog')]) ?>
    </span>

    <? if ($count): ?>
        <table class="default">
            <thead>
                <tr class="sortable">
                    <th style="width: 40%;" class="<?= $controller->sort_class($sort === 'title', $desc) ?>">
                        <input type="checkbox" data-proxyfor=".batch_select_d" data-activates=".batch_action_d" aria-label="<?= _('Alle Aufgaben auswählen') ?>">
                        <a href="<?= $controller->link_for('vips/sheets/copy_exercise_dialog',
                            compact('assignment_id', 'search_filter') + ['sort' => 'title', 'desc' => $sort === 'title' && !$desc]) ?>" data-dialog="size=big">
                            <?= _('Titel der Aufgabe') ?>
                        </a>
                    </th>
                    <th style="width: 25%;" class="<?= $controller->sort_class($sort === 'test_title', $desc) ?>">
                        <a href="<?= $controller->link_for('vips/sheets/copy_exercise_dialog',
                            compact('assignment_id', 'search_filter') + ['sort' => 'test_title', 'desc' => $sort === 'test_title' && !$desc]) ?>" data-dialog="size=big">
                            <?= _('Aufgabenblatt') ?>
                        </a>
                    </th>
                    <th style="width: 25%;" class="<?= $controller->sort_class($sort === 'course_name', $desc) ?>">
                        <a href="<?= $controller->link_for('vips/sheets/copy_exercise_dialog',
                            compact('assignment_id', 'search_filter') + ['sort' => 'course_name', 'desc' => $sort === 'course_name' && !$desc]) ?>" data-dialog="size=big">
                            <?= _('Veranstaltung') ?>
                        </a>
                    </th>
                    <th style="width: 10%;" class="<?= $controller->sort_class($sort === 'start_time', $desc) ?>">
                        <a href="<?= $controller->link_for('vips/sheets/copy_exercise_dialog',
                            compact('assignment_id', 'search_filter') + ['sort' => 'start_time', 'desc' => $sort === 'start_time' && !$desc]) ?>" data-dialog="size=big">
                            <?= _('Semester') ?>
                        </a>
                    </th>
                </tr>
            </thead>

            <tbody>
                <? foreach ($exercises as $exercise): ?>
                    <? $course_id = $exercise['range_type'] === 'course' ? $exercise['range_id'] : null ?>
                    <tr>
                        <td>
                            <label class="undecorated">
                                <input class="batch_select_d" type="checkbox" name="exercise_ids[<?= $exercise['id'] ?>]" value="<?= $exercise['assignment_id'] ?>" aria-label="<?= _('Zeile auswählen') ?>">
                                <?= htmlReady($exercise['title']) ?>

                                <a href="<?= $controller->link_for('vips/sheets/preview_exercise', ['assignment_id' => $exercise['assignment_id'], 'exercise_id' => $exercise['id']]) ?>"
                                   data-dialog="id=vips_preview;size=800x600" target="_blank">
                                    <?= Icon::create('question-circle')->asSvg(['title' => _('Vorschau anzeigen')]) ?>
                                </a>
                            </label>
                        </td>
                        <td>
                            <a href="<?= $controller->link_for('vips/sheets/edit_assignment', ['cid' => $course_id, 'assignment_id' => $exercise['assignment_id']]) ?>">
                                <?= htmlReady($exercise['test_title']) ?>
                            </a>
                        </td>
                        <td>
                            <? if ($course_id): ?>
                                <?= htmlReady($exercise['course_name']) ?>
                            <? endif ?>
                        </td>
                        <td>
                            <? if ($course_id && $exercise['start_time']): ?>
                                <?= htmlReady(Semester::findByTimestamp($exercise['start_time'])->name) ?>
                            <? endif ?>
                        </td>
                    </tr>
                <? endforeach ?>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="4" class="actions">
                        <?= $controller->page_chooser($controller->url_for('vips/sheets/copy_exercise_dialog', ['page' => '%d'] + compact('assignment_id', 'search_filter', 'sort', 'desc')),
                                                      $count, $page, 'data-dialog="size=big"', $size) ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    <? else: ?>
        <?= MessageBox::info(_('Es wurden keine Aufgaben gefunden.')) ?>
    <? endif ?>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Kopieren'), 'copy_exercise', ['class' => 'batch_action_d']) ?>
    </footer>
</form>
