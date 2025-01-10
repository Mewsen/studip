<form method="get"
      action="<?= $controller->link_for('course/connectedcourses/connect', ['search' => 1]) ?>">
    <?= CSRFProtection::tokenTag() ?>
    <table class="default" style="margin-top: 20px;">
        <caption>
            <?= _('Lehrveranstaltungen') ?>
            <span class="actions">
                <? if (Request::get('search')) : ?>
                        <select name="semester_id" aria-label="<?= _('Filtern Sie optional nach einem Semester') ?>">
                            <option value=""><?= _('In Semester') ?></option>
                            <? foreach (array_reverse(Semester::getAll()) as $semester) : ?>
                                <option value="<?= htmlReady($semester->id) ?>"<?= $semester->id === Request::option('semester_id') ? ' selected' : '' ?>>
                                     <?= htmlReady($semester->name) ?>
                                </option>
                            <? endforeach ?>
                        </select>

                        <input type="text"
                               name="search"
                               id="search_connectable_courses"
                               autofocus
                               placeholder="<?= _('Veranstaltung suchen ...') ?>"
                               value="<?= htmlReady(Request::get('search') != 1 ? Request::get('search') : '') ?>">
                        <?= Icon::create('search')->asInput([
                            'title' => _('Suchen Sie nach beliebigen Veranstaltungen'),
                            'data-dialog' => 1
                        ]) ?>
                        <a href="<?= $controller->connect() ?>" data-dialog title="<?= _('Suche schließen') ?>">
                            <?= Icon::create('decline') ?>
                        </a>
                <? else : ?>
                    <?= Icon::create('search')->asInput([
                        'title' => _('Suchen Sie nach beliebigen Veranstaltungen'),
                        'data-dialog' => 1,
                        'formaction' => $controller->connectURL(['search' => 1])
                    ]) ?>
                <? endif ?>
            </span>
        </caption>
        <thead>
            <tr>
                <th><?= _('Name') ?></th>
                <th><?= _('Semester') ?></th>
                <th class="actions"><?= _('Aktion') ?></th>
            </tr>
        </thead>
        <tbody>
        <? if (!Request::get('search') || Request::get('search') == 1) : ?>
            <? if (count($my_courses) + count($suggestions) > 0) : ?>
                <? foreach ($my_courses as $my_course) : ?>
                    <?= $this->render_partial('course/connectedcourses/_course_to_connect', ['course' => $my_course]) ?>
                <? endforeach ?>
                <? foreach ($suggestions as $suggested_course) : ?>
                    <?= $this->render_partial('course/connectedcourses/_course_to_connect', ['course' => $suggested_course]) ?>
                <? endforeach ?>
            <? else : ?>
            <tr>
                <td colspan="3">
                    <?= _('Suchen Sie nach Veranstaltungen.') ?>
                </td>
            </tr>
            <? endif ?>
        <? else : ?>
            <? if (isset($searchresults) && count($searchresults)) : ?>
            <? foreach ($searchresults as $course) : ?>
                <?= $this->render_partial('course/connectedcourses/_course_to_connect', ['course' => $course]) ?>
            <? endforeach ?>
            <? else : ?>
                <tr>
                    <td colspan="3">
                        <?= _('Keine passenden Ergebnisse gefunden.') ?>
                    </td>
                </tr>
            <? endif ?>
        <? endif ?>
        </tbody>
    </table>
</form>
