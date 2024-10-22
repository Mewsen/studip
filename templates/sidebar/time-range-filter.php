<form class="default" method="post" action="" data-autosubmit="filter_time_range">
    <?= CSRFProtection::tokenTag() ?>
    <label>
        <?= _('Dateien neuer als') ?>:
        <input type="text" name="begin" id="begin"
               value="<?= htmlReady($begin ?? '') ?>"
               data-date-picker="<?= htmlReady(json_encode(['<=' => '#end'])) ?>">
    </label>
    <label>
        <?= _('Dateien älter als') ?>:
        <input type="text" name="end" id="end"
               value="<?= htmlReady($end ?? '') ?>"
               data-date-picker="<?= htmlReady(json_encode(['>=' => '#begin'])) ?>"
               onchange="this.closest('form').submit()">
    </label>
    <? if (!empty($course_options)) : ?>
        <label>
            <?= _('Veranstaltung') ?>:
            <select name="course_id">
                <option value=""><?= _('Bitte wählen') ?></option>
                <? foreach ($course_options as $course_id => $course_name) : ?>
                    <option value="<?= htmlReady($course_id) ?>"
                            <?= $course_id == $selected_course_id
                              ? 'selected' : '' ?>>
                        <?= htmlReady($course_name) ?>
                    </option>
                <? endforeach ?>
            </select>
        </label>
    <? endif ?>
    <?= \Studip\Button::create(_('Übernehmen'), 'filter') ?>
</form>
