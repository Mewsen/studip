<form class="default" action="<?= $controller->link_for('vips/sheets/copy_assignment') ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>
    <input type="hidden" name="sort" value="<?= $sort ?>">
    <input type="hidden" name="desc" value="<?= $desc ?>">

    <input type="text" name="search_filter[search_string]" value="<?= htmlReady($search_filter['search_string']) ?>" aria-label="<?= _('Suchbegriff eingeben') ?>"
           placeholder="<?= _('Aufgabenblatt oder Veranstaltung') ?>" style="max-width: 24em;">

    <select name="search_filter[assignment_type]" class="inline_select" aria-label="<?= _('Modus auswählen') ?>">
        <option value="">
            <?= _('Beliebiger Modus') ?>
        </option>
        <? foreach ($assignment_types as $type => $entry): ?>
            <option value="<?= $type ?>" <?= $search_filter['assignment_type'] == $type ? 'selected' : '' ?>>
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
        <?= Studip\Button::create(_('Suchen'), 'start_search', ['data-dialog' => 'size=1200x800', 'formaction' => $controller->url_for('vips/sheets/copy_assignment_dialog')]) ?>
        <?= Studip\Button::create(_('Zurücksetzen'), 'reset_search', ['data-dialog' => 'size=1200x800', 'formaction' => $controller->url_for('vips/sheets/copy_assignment_dialog')]) ?>
    </div>

    <? if ($count): ?>
        <table class="default">
            <thead>
                <tr class="sortable">
                    <th style="width: 45%;" class="<?= $controller->sort_class($sort === 'test_title', $desc) ?>">
                        <input type="checkbox" data-proxyfor=".batch_select_d" data-activates=".batch_action_d" aria-label="<?= _('Alle Aufgaben auswählen') ?>">
                        <a href="<?= $controller->link_for('vips/sheets/copy_assignment_dialog',
                            compact('search_filter') + ['sort' => 'test_title', 'desc' => $sort === 'test_title' && !$desc]) ?>" data-dialog="size=1200x800">
                            <?= _('Aufgabenblatt') ?>
                        </a>
                    </th>
                    <th style="width: 40%;" class="<?= $controller->sort_class($sort === 'course_name', $desc) ?>">
                        <a href="<?= $controller->link_for('vips/sheets/copy_assignment_dialog',
                            compact('search_filter') + ['sort' => 'course_name', 'desc' => $sort === 'course_name' && !$desc]) ?>" data-dialog="size=1200x800">
                            <?= _('Veranstaltung') ?>
                        </a>
                    </th>
                    <th style="width: 15%;" class="<?= $controller->sort_class($sort === 'start_time', $desc) ?>">
                        <a href="<?= $controller->link_for('vips/sheets/copy_assignment_dialog',
                            compact('search_filter') + ['sort' => 'start_time', 'desc' => $sort === 'start_time' && !$desc]) ?>" data-dialog="size=1200x800">
                            <?= _('Semester') ?>
                        </a>
                    </th>
                </tr>
            </thead>

            <tbody>
                <? foreach ($assignments as $assignment): ?>
                    <? $course_id = $assignment['range_type'] === 'course' ? $assignment['range_id'] : null ?>
                    <tr>
                        <td>
                            <label class="undecorated">
                                <input class="batch_select_d" type="checkbox" name="assignment_ids[]" value="<?= $assignment['id'] ?>" aria-label="<?= _('Zeile auswählen') ?>">
                                <?= htmlReady($assignment['test_title']) ?>

                                <a href="<?= $controller->link_for('vips/sheets/show_assignment', ['cid' => $course_id, 'assignment_id' => $assignment['id']]) ?>" target="_blank">
                                    <?= Icon::create('link-intern')->asImg(['title' => _('Vorschau anzeigen')]) ?>
                                </a>
                            </label>
                        </td>
                        <td>
                            <? if ($course_id): ?>
                                <?= htmlReady($assignment['course_name']) ?>
                            <? endif ?>
                        </td>
                        <td>
                            <? if ($course_id && $assignment['start_time']): ?>
                                <?= htmlReady(Semester::findByTimestamp($assignment['start_time'])->name) ?>
                            <? endif ?>
                        </td>
                    </tr>
                <? endforeach ?>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="3" class="actions">
                        <?= $controller->page_chooser($controller->url_for('vips/sheets/copy_assignment_dialog', ['page' => '%d'] + compact('search_filter', 'sort', 'desc')),
                                                      $count, $page, 'data-dialog="size=1200x800"', $size) ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    <? else: ?>
        <?= MessageBox::info(_('Es wurden keine Aufgabenblätter gefunden.')) ?>
    <? endif ?>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Kopieren'), 'copy_assignment', ['class' => 'batch_action_d']) ?>
    </footer>
</form>
