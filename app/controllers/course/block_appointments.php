<?php

/**
 * block_appointments.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      André Noack <noack@data-quest.de>
 * @author      David Siegfried <david.siegfried@uni-vechta.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @package     admin
 */
class Course_BlockAppointmentsController extends AuthenticatedController
{
    /**
     * Common tasks for all actions
     *
     * @param String $action Called action
     * @param Array  $args   Possible arguments
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $course_id = $args[0] ?? null;

        $this->course_id = Request::option('cid', $course_id);
        if (!get_object_type($this->course_id, ['sem']) ||
            SeminarCategories::GetBySeminarId($this->course_id)->studygroup_mode ||
            !$GLOBALS['perm']->have_studip_perm("tutor", $this->course_id)
        ) {
            throw new Trails\Exception(400);
        }
        PageLayout::setHelpKeyword('Basis.VeranstaltungenVerwaltenAendernVonZeitenUndTerminen');
        PageLayout::setTitle(Course::findCurrent()->getFullName() . " - " . _('Blockveranstaltungstermine anlegen'));
    }


    /**
     * Display the block appointments
     */
    public function index_action()
    {
        if (Navigation::hasItem('/course/admin/timesrooms')) {
            Navigation::activateItem('/course/admin/timesrooms');
        }
        PageLayout::setTitle(_('Neuen Blocktermin anlegen'));

        $this->linkAttributes   = ['fromDialog' => Request::int('fromDialog') ? 1 : 0];
        $this->start_ts         = strtotime('this monday');
        $this->request          = $this->flash['request'] ?? $_SESSION['block_appointments'] ?? [];
        $this->lecturers        = CourseMember::findByCourseAndStatus(
            $this->course_id,
            'dozent'
        );
        $this->start                 = null;
        $this->end                   = null;
        $this->date_types = [];
        foreach ($GLOBALS['TERMIN_TYP'] as $id => $data) {
            $this->date_types[] = [
                'id'   => $id,
                'name' => $data['name']
            ];
        }
        $this->available_lecturers = [];
        $course                    = Course::find($this->course_id);
        $lecturers                 = $course->getMembersWithStatus('dozent');
        foreach ($lecturers as $lecturer) {
            $this->available_lecturers[$lecturer->user_id] = $lecturer->getUserFullname();
        }
        $this->selected_lecturer_ids = [];
        $this->selected_date_type    = 0;
        $this->dow                   = ['all'];
        $this->preparation_time      = 0;
        $this->subsequent_time       = 0;

        if ($this->request instanceof Request) {
            $this->start                 = $this->request->getDateTime('start_date', 'd.m.Y', 'start_time', 'H:i');
            $this->end                   = $this->request->getDateTime('end_date', 'd.m.Y', 'end_time', 'H:i');
            $this->selected_lecturer_ids = $this->request->getArray('lecturers');
            $this->selected_date_type    = $this->request->int('date_type');
            $this->dow                   = $this->request->getArray('dow');
            $this->preparation_time      = $this->request->int('preparation_time', 0);
            $this->subsequent_time       = $this->request->int('subsequent_time', 0);
        } elseif (is_array($this->request)) {
            $this->start              = $this->request['start'] ?? null;
            $this->end                = $this->request['end'] ?? null;
            $this->selected_date_type = $this->request['date_type'] ?? 0;
            $this->dow                = $this->request['dow'] ?? ['all'];
            $this->preparation_time   = $this->request['preparation_time'] ?? 0;
            $this->subsequent_time    = $this->request['subsequent_time'] ?? 0;
        }
        if (!$this->start || !$this->end) {
            //Provide some default values:
            $this->start = new DateTime();
            $this->start = $this->start->add(new DateInterval('PT1H'));
            $this->start->setTime(intval($this->start->format('H')), 0, 0);
            $this->end = clone $this->start;
            $this->end = $this->end->add(new DateInterval('PT30M'));
        }

        $this->allow_multiple_room_bookings = ResourceManager::userHasGlobalPermission(
            User::findCurrent(),
            Config::get()->ROOM_PERMISSIONS_FOR_MULTIPLE_BOOKINGS_PER_COURSE_DATE
        );
        $this->max_preparation_time = intval(Config::get()->RESOURCES_MAX_PREPARATION_TIME) ?? 999;
    }

    /**
     * Saves the block appointments of a course
     *
     * @param String $course_id Id of the course
     */
    public function save_action($course_id)
    {
        $errors = [];

        $start = Request::getDateTime('start_date', 'd.m.Y', 'start_time', 'H:i');
        $end   = Request::getDateTime('end_date', 'd.m.Y', 'end_time', 'H:i');
        if (!$start || !$end || $start >= $end) {
            $errors[] = _('Bitte geben Sie korrekte Werte für Start- und Enddatum an!');
        }

        $room_choice      = Request::get('room');
        $preparation_time = 0;
        $subsequent_time  = 0;
        $room_name = '';
        if ($room_choice === 'room' && Config::get()->RESOURCES_MIN_BOOKING_TIME) {
            //Calculate the duration if a minimum booking time is set
            //and one or more rooms shall be booked:
            $fake_start_time = clone $start;
            $fake_end_time = clone $start;
            $fake_end_time->setTime(intval($end->format('H')), intval($end->format('i')), 0);
            $duration = $fake_end_time->getTimestamp() - $fake_start_time->getTimestamp();
            if ($duration < Config::get()->RESOURCES_MIN_BOOKING_TIME * 60) {
                $errors[] = sprintf(
                    ngettext(
                        'Die minimale Dauer einer Raumbuchung von einer Minute wurde unterschritten.',
                        'Die minimale Dauer einer Raumbuchung von %u Minuten wurde unterschritten.',
                        Config::get()->RESOURCES_MIN_BOOKING_TIME
                    ),
                    Config::get()->RESOURCES_MIN_BOOKING_TIME
                );
            }
            $preparation_time = Request::int('preparation_time', 0);
            $subsequent_time  = Request::int('subsequent_time', 0);
        } elseif ($room_choice === 'freetext') {
            $room_name = Request::get('room_name');
        }

        $date_type  = Request::int('date_type', 0);
        $dow        = Request::getArray('dow');
        if (empty($dow)) {
            $errors[] = _('Bitte wählen Sie mindestens einen Tag aus!');
        }

        $date_count = Request::int('date_count');
        if ($date_count < 1) {
            $errors[] = _('Bitte setzen Sie die Menge der zu erstellenden Termine mindestens auf 1.');
        }

        if (count($errors)) {
            $this->flash['request'] = Request::getInstance();
            PageLayout::postMessage(MessageBox::error(_('Bitte korrigieren Sie Ihre Eingaben:'), $errors));
            $this->redirect('course/block_appointments/index');
            return;
        }

        $lecturer_ids = Request::getArray('assigned_lecturers');
        $lecturers = [];
        if ($lecturer_ids) {
            $lecturers = User::findBySql(
                "INNER JOIN seminar_user USING (user_id)
                 WHERE seminar_id = :course_id
                 AND seminar_user.user_id IN (:lecturer_ids)
                 AND seminar_user.status = 'dozent'",
                [
                    'course_id' => $this->course_id,
                    'lecturer_ids' => $lecturer_ids,
                ]
            );
        }

        if (in_array('all', $dow)) {
            $dow = ['1', '2', '3', '4', '5', '6', '7'];
        } elseif (in_array('mon_fri', $dow)) {
            $dow = ['1', '2', '3', '4', '5'];
        }

        $dates = [];
        $t     = clone $start;
        $i     = 1;
        while ($t < $end && $i <= $date_count) {
            if (in_array($t->format('N'), $dow)) {
                $date_end = clone $t;
                $date_end->setTime(intval($end->format('H')), intval($end->format('i')), 0);
                $date = new CourseDate();
                $date->range_id = $course_id;
                $date->date_typ = $date_type;
                $date->raum     = $room_name;
                $date->date     = $t->getTimestamp();
                $date->end_time = $date_end->getTimestamp();
                if ($lecturers) {
                    $date->dozenten = $lecturers;
                }
                $dates[] = $date;
                $i++;
            }
            $t = $t->add(new DateInterval('P1D'));
        }

        //Store the last used values in the session as default values.
        $_SESSION['block_appointments'] = [
            'start'      => $start,
            'end'        => $end,
            'date_type'  => $date_type,
            'room_name'  => $room_name,
            'date_count' => $date_count,
            'dow'        => $dow
        ];
        $partially_booked_dates = [];
        $dates_created = array_filter(array_map(function ($d) use ($room_choice, $preparation_time, $subsequent_time, &$partially_booked_dates) {
            $result = $d->store();
            $room_ids = [];
            if ($room_choice === 'room') {
                $room_ids = Request::getArray('room_ids');
            }
            //Process the room-IDs: If a separable room is selected, set all its room parts as room-IDs.
            //Remove the prefix in all other cases.
            $processed_room_ids = [];
            foreach ($room_ids as $room_id) {
                $id_parts = explode('-', $room_id);
                if (count($id_parts) !== 2) {
                    //Invalid ID.
                    continue;
                }

                if ($id_parts[0] === 'separable_room') {
                    //A separable room was selected.
                    $separable_room = SeparableRoom::find($id_parts[1]);
                    if ($separable_room) {
                        foreach ($separable_room->parts as $part) {
                            $processed_room_ids[] = $part->room_id;
                        }
                    }
                } elseif ($id_parts[0] === 'room') {
                    //An ordinary room.
                    $processed_room_ids[] = $id_parts[1];
                }
            }
            $room_ids = $processed_room_ids;
            if ($room_ids) {
                $resources = Resource::findMany($room_ids);
                $rooms = [];
                foreach ($resources as $resource) {
                    $rooms[] = $resource->getDerivedClassInstance();
                }
                $booking_failures = 0;
                foreach ($rooms as $room) {
                    try {
                        $r = $d->bookRoom($room, $preparation_time * 60, $subsequent_time * 60);
                        if (!$r) {
                            $booking_failures++;
                        }
                    } catch (ResourceBookingException|ResourceBookingOverlapException $e) {
                        $booking_failures++;
                    }
                }
                if ($result && $booking_failures) {
                    //Not all selected rooms for the date could be booked:
                    $partially_booked_dates[] = htmlReady($d->getFullName());
                }
            }

            return $result ? htmlReady($d->getFullName()) : null;
        }, $dates));

        if ($date_count > 1) {
            $dates_created = array_count_values($dates_created);
            $dates_created = array_map(function ($k, $v) {
                return $k . ' (' . $v . 'x)';
            }, array_keys($dates_created), array_values($dates_created));
        }
        PageLayout::postSuccess(_('Folgende Termine wurden erstellt:'), $dates_created);
        if (!empty($partially_booked_dates)) {
            PageLayout::postWarning(_('Für folgende Termine konnten nicht alle ausgewählten Räume gebucht werden:'), $partially_booked_dates);
        }

        if (Request::int('fromDialog')) {
            $this->redirect('course/timesrooms/index');
        } else {
            $this->relocate('course/timesrooms/index');
        }
    }
}
