<?
# Lifter010: TODO
use Studip\Button, Studip\LinkButton;

?>
<? if (isset($flash['delete'])): ?>
    <?= (string)QuestionBox::create(
        _('Wollen Sie die Zuordnung der Veranstaltung zum automatischen Eintragen wirklich löschen?'),
        $controller->deleteURL($flash['delete'], ['delete' => 1]),
        $controller->deleteURL($flash['delete'], ['back' => 1])
    ) ?>
<? endif; ?>
    <form class="default" action="<?= $controller->index() ?>" method="post">
        <?= CSRFProtection::tokenTag() ?>
        <?= $this->render_partial("admin/autoinsert/_search.php", ['semester_data' => $semester_data]) ?>
    </form>

<? if (is_array($seminar_search) && count($seminar_search) > 0): ?>
    <br>
    <form class="default" action="<?= $controller->new() ?>" method="post">
        <?= CSRFProtection::tokenTag() ?>
        <fieldset>
            <legend>
                <?= _('Suchergebnisse') ?>
            </legend>

            <label>
                <?= _('Veranstaltung') ?>
                <select name="sem_id" id="sem_id">
                    <? foreach ($seminar_search as $seminar): ?>
                        <option value="<?= $seminar[0] ?>">
                            <?= htmlReady($seminar[1]) ?>
                        </option>
                    <? endforeach; ?>
                </select>
            </label>

            <fieldset>
                <legend>
                    <?= _('Automatisch eintragen nach...') ?>
                </legend>
                <section>
                    <label>
                        <input type="checkbox" name="autoinsert_type" value="domain" checked>
                        <?= _('Nutzerdomäne') ?>
                    </label>
                    <label>
                        <input type="checkbox" name="autoinsert_type" value="degree">
                        <?= _('Abschluss') ?>
                    </label>
                    <label>
                        <input type="checkbox" name="autoinsert_type" value="subject">
                        <?= _('Studienfach') ?>
                    </label>
                    <label>
                        <input type="checkbox" name="autoinsert_type" value="semester">
                        <?= _('Fachsemester') ?>
                    </label>
                    <label>
                        <input type="checkbox" name="autoinsert_type" value="institute">
                        <?= _('Einrichtung') ?>
                    </label>
                </section>
            </fieldset>

            <?= $this->render_partial('admin/autoinsert/_domains') ?>
            <?= $this->render_partial('admin/autoinsert/_degrees') ?>
            <?= $this->render_partial('admin/autoinsert/_subjects') ?>
            <?= $this->render_partial('admin/autoinsert/_semesters') ?>
            <?= $this->render_partial('admin/autoinsert/_institutes') ?>

        </fieldset>
        <footer>
            <?= Studip\Button::create(_('Anlegen'), 'anlegen') ?>
        </footer>
    </form>
<? endif; ?>

<? if (!empty($auto_sems)) : ?>
    <table class="default">
        <caption><?= _('Vorhandene Zuordnungen') ?></caption>
        <thead>
            <tr>
                <th><?= _('Veranstaltung') ?></th>
                <th><?= _('Art der Zuordnung') ?></th>
                <th><?= _('Zugeordnet zu') ?></th>
                <th><?= _('Berechtigung') ?></th>
                <th class="actions"><?= _('Aktionen') ?></th>
            </tr>
        </thead>
        <tbody>
            <? if ($grouping == 'by_course') : ?>
                <? foreach ($auto_sems as $id => $types): ?>
                    <? $row = 1; foreach ($types as $courses): ?>
                        <? $typerow = 1; foreach ($courses as $auto_sem) : ?>
                            <tr>
                                <? if ($row == 1) : ?>
                                    <td rowspan="<?= count($types) + 1 ?>">
                                        <a href="<?= $controller->link_for('course/overview', ['auswahl' => $auto_sem['seminar_id']]) ?>">
                                            <?= htmlReady($auto_sem['Name']) ?>
                                        </a>
                                    </td>
                                <? endif ?>
                                <? if ($typerow == 1) : ?>
                                    <td rowspan="<?= count($courses) ?>">
                                        <?= htmlReady($range_types[$auto_sem['range_type']]) ?>
                                    </td>
                                    <? endif ?>
                                <td>
                                    <?= htmlReady($auto_sem['range_name']) ?>
                                </td>
                                <td>
                                    <?= htmlReady($auto_sem['status'] ?: _('alle')) ?>
                                </td>

                                <td class="actions">
                                    <a href="<?= $controller->delete($auto_sem['seminar_id'] ) ?>">
                                        <?= Icon::create(
                                            'trash',
                                            Icon::ROLE_CLICKABLE,
                                            ['title' => _('Veranstaltung entfernen'), 'class' => 'text-top']
                                        ) ?>
                                    </a>
                                </td>
                            </tr>
                            <? $typerow++ ?>
                        <? endforeach ?>
                        <? $row++ ?>
                    <? endforeach ?>
                <? endforeach ?>
            <? endif ?>
        </tbody>
    </table>
<? endif ?>
