<?php
/**
 * @var Course_TimesroomsController $controller
 * @var string $cycle_id
 * @var array $linkAttributes
 * @var array $checked_dates
 * @var QuickSearch $room_search
 * @var array $teachers
 * @var array $gruppen
 * @var array $time_ranges
 * @var bool $allow_multiple_room_bookings
 * @var int $preparation_time
 * @var int $subsequent_time
 * @var int $max_preparation_time
 * @var string[] $selected_lecturer_ids
 * @var string[] $selected_room_ids
 */
?>
<form method="post" action="<?= $controller->link_for('course/timesrooms/saveStack/' . $cycle_id, $linkAttributes ?? []) ?>"
      class="default collapsable" data-dialog="size=big">
    <?= CSRFProtection::tokenTag()?>
    <input type="hidden" name="method" value="edit">
    <input type="hidden" name="checked_dates" value="<?= implode(',', $checked_dates) ?>">

    <section id="room-fieldset">
        <course-date-room-fieldset
            :time_ranges="<?= htmlReady(json_encode($time_ranges)) ?>"
            :course_date_ids="<?= htmlReady(json_encode($checked_dates)) ?>"
            :room_management_enabled="<?= Config::get()->RESOURCES_ENABLE ? 'true' : 'false' ?>"
            :allow_multiple_room_bookings="<?= $allow_multiple_room_bookings ? 'true' : 'false' ?>"
            :initial_preparation_time="<?= $preparation_time ?>"
            :initial_subsequent_time="<?= $subsequent_time ?>"
            :max_preparation_time="<?= $max_preparation_time ?>"
            :selected_rooms="<?= htmlReady(json_encode($selected_room_ids ?? [])) ?>"
            :show_nochange_option="true"
        ></course-date-room-fieldset>
    </section>

    <fieldset class="collapsed">
        <legend><?= _('Terminangaben') ?></legend>
        <label>
            <?= _('Art') ?>
            <select name="course_type" id="course_type">
                <option value=""><?= _('-- Keine Änderung --') ?></option>
                <? foreach ($GLOBALS['TERMIN_TYP'] as $id => $value) : ?>
                    <option value="<?= $id ?>"><?= htmlReady($value['name']) ?></option>
                <? endforeach ?>
            </select>
        </label>
    </fieldset>

    <fieldset class="collapsed">
        <legend><?= _('Durchführende Lehrende') ?></legend>
        <? if ($selected_lecturer_ids) : ?>
            <ul>
                <? foreach ($teachers as $teacher) : ?>
                    <? if (in_array($teacher['user_id'], $selected_lecturer_ids)) : ?>
                        <li><?= htmlReady($teacher['fullname']) ?></li>
                    <? endif ?>
                <? endforeach ?>
            </ul>
        <? endif ?>
        <label>
            <?= _('Aktion auswählen') ?>
            <select name="related_persons_action" id="related_persons_action">
                <option value=""><?= _('-- Keine Änderung --') ?></option>
                <option value="add"><?= _('Lehrende hinzufügen') ?></option>
                <option value="delete"><?= _('Lehrende entfernen') ?></option>
            </select>
        </label>

        <? if (!empty($teachers)) : ?>
            <label>
                <?= _('Lehrende') ?>
                <select name="related_persons[]" id="related_persons" multiple>
                <? foreach ($teachers as $teacher) : ?>
                    <option value="<?= htmlReady($teacher->user_id) ?>">
                        <?= htmlReady($teacher->user->getFullName()) ?>
                    </option>
                <? endforeach ?>
                </select>
            </label>
        <? endif ?>
    </fieldset>

    <? if (count($gruppen)) : ?>
        <fieldset class="collapsed">
            <legend><?= _('Beteiligte Gruppen') ?></legend>
            <label>
                <?= _('Aktion auswählen') ?>
                <select name="related_groups_action" id="related_groups_action">
                    <option value=""><?= _('-- Keine Änderung --') ?></option>
                    <option value="add"><?= _('Gruppen hinzufügen') ?></option>
                    <option value="delete"><?= _('Gruppen entfernen') ?></option>
                </select>
            </label>

            <label>
                <?= _('Statusgruppen')?>
                <select id="related_groups" name="related_groups[]" multiple>
                    <? foreach ($gruppen as $gruppe) : ?>
                        <option value="<?= htmlReady($gruppe->statusgruppe_id) ?>"><?= htmlReady($gruppe->name) ?></option>
                    <? endforeach ?>
                </select>
            </label>
        </fieldset>
    <? endif ?>


    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Änderungen speichern'), 'save') ?>
        <? if (Request::int('fromDialog')) : ?>
            <?= Studip\LinkButton::create(
                _('Zurück zur Übersicht'),
                $controller->url_for('course/timesrooms/index'),
                ['data-dialog' => 'size=big']
            ) ?>
        <? endif ?>
    </footer>
</form>
<script>
    STUDIP.Vue.load().then(({createApp}) => {
        STUDIP.editStackRoomFieldset = createApp({
            el: "#room-fieldset"
        });
    });
</script>
