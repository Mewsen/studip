<?php

/**
 * schedule.php - Calender schedule controller
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @package     calender
 * @since       6.0
 */
class Calendar_ScheduleController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!Context::isCourse() && Navigation::hasItem('/calendar')) {
            Navigation::activateItem('/calendar');
        }
    }

    public function index_action()
    {
        PageLayout::setTitle(_('Stundenplan'));

        if (Navigation::hasItem('/calendar/schedule')) {
            Navigation::activateItem('/calendar/schedule');
        }

        //Build the sidebar:

        $sidebar = Sidebar::get();

        //Add the semester selector widget first:
        $semester_widget = new SemesterSelectorWidget(
            $this->url_for('calendar/schedule/index')
        );
        $sidebar->addWidget($semester_widget);

        //Then add the actions for the action widget:
        $actions = new ActionsWidget();
        $actions->addLink(
            _('Neuer Termin'),
            $this->url_for('calendar/schedule/entry/add'),
            Icon::create('add'),
            ['data-dialog' => 'size=default']
        );

        $actions->addLink(
            _('Drucken'),
            'javascript:void(window.print());',
            Icon::create('print')
        );
        $actions->addLink(
            _('Einstellungen'),
            $this->url_for('settings/calendar'),
            Icon::create('settings'),
            ['data-dialog' => 'size=auto;reload-on-close']
        );
        $sidebar->addWidget($actions);

        //Handle the selected semester and create a Fullcalendar instance.

        $semester = null;
        if (Request::submitted('semester_id')) {
            $semester = Semester::find(Request::get('semester_id'));
        }
        if (!$semester) {
            $semester = Semester::findCurrent();
        }

        $fullcalendar = \Studip\Calendar\Helper::getScheduleFullcalendar($semester->id ?? '');
        $this->fullcalendar = $fullcalendar->render();
    }

    public function data_action()
    {
        //Fullcalendar sets the week time range in which to put the course dates
        //of the semester. Therefore, start and end are handled in here.
        $begin = Request::getDateTime('start', \DateTime::RFC3339);
        $end = Request::getDateTime('end', \DateTime::RFC3339);
        if (!($begin instanceof \DateTime) || !($end instanceof \DateTime)) {
            //No time range specified.
            throw new InvalidArgumentException('Invalid parameters!');
        }

        $result = [];

        $semester_id = Request::get('semester_id');
        $semester = Semester::find($semester_id);
        if ($semester) {
            //Get all regular course dates for that semester:
            $cycle_dates = SeminarCycleDate::findBySql(
                'INNER JOIN `termine` USING (`metadate_id`)
                INNER JOIN `seminare` USING (`seminar_id`)
                INNER JOIN `seminar_user` USING (`seminar_id`)
                WHERE
                `seminar_user`.`user_id` = :user_id
                AND
                (
                `termine`.`date` BETWEEN :begin AND :end
                OR `termine`.`end_time` BETWEEN :begin AND :end
                )
                GROUP BY `metadate_id`',
                [
                    'user_id' => $GLOBALS['user']->id,
                    'begin' => $semester->beginn,
                    'end' => $semester->ende
                ]
            );

            foreach ($cycle_dates as $cycle_date) {
                //Calculate a fake begin and end that lies in the week
                //fullcalendar has specified.
                $fake_begin = clone $begin;
                $fake_end = clone $begin;
                if ($cycle_date->weekday > 1) {
                    $fake_begin = $fake_begin->add(new DateInterval('P' . ($cycle_date->weekday - 1) . 'D'));
                    $fake_end = $fake_end->add(new DateInterval('P' . ($cycle_date->weekday - 1) . 'D'));
                }
                $start_time_parts = explode(':', $cycle_date->start_time);
                $end_time_parts = explode(':', $cycle_date->end_time);
                $fake_begin->setTime(
                    intval($start_time_parts[0]),
                    intval($start_time_parts[1]),
                    intval($start_time_parts[2])
                );
                $fake_end->setTime(
                    intval($end_time_parts[0]),
                    intval($end_time_parts[1]),
                    intval($end_time_parts[2])
                );

                //Get the course colour:
                $course_membership = CourseMember::findOneBySQL(
                    'seminar_id = :course_id AND user_id = :user_id',
                    [
                        'course_id' => $cycle_date->seminar_id,
                        'user_id' => $GLOBALS['user']->id
                    ]
                );
                $event_classes = [];
                if ($course_membership) {
                    $event_classes[] = sprintf('course-color-%u', $course_membership->gruppe);
                }

                $event = new \Studip\Calendar\EventData(
                    $fake_begin,
                    $fake_end,
                    $cycle_date->course->getFullName(),
                    $event_classes,
                    '',
                    '',
                    false,
                    'SeminarCycleDate',
                    $cycle_date->id,
                    '',
                    '',
                    'course',
                    $cycle_date->seminar_id,
                    [
                        'show' => $this->url_for('calendar/schedule/course_info/' . $cycle_date->seminar_id)
                    ]
                );

                $result[] = $event->toFullcalendarEvent();
            }
        }

        //Add all schedule entries to the result set:
        $weekly_dates = ScheduleEntry::findByUser_id($GLOBALS['user']->id);
        foreach ($weekly_dates as $date) {
            $event_data = $date->toEventData($GLOBALS['user']->id);
            //Disable fullcalendar drag & drop actions:
            $event_data->editable = false;
            $result[] = $event_data->toFullcalendarEvent();
        }

        $this->render_json($result);
    }

    /**
     * This action handles adding and editing schedule entries.
     *
     * @param string $entry_id The ID of the entry to be modified. In case the ID is set to "add", a new entry
     *     will be created. In all other cases, an existing entry will be loaded.
     */
    public function entry_action(string $entry_id)
    {
        $this->entry = null;
        if ($entry_id === 'add') {
            //Add mode
            $this->entry          = new ScheduleEntry();
            $this->entry->user_id = $GLOBALS['user']->id;
            if (!Request::submitted('save')) {
                //Provide good default values:
                $this->entry->dow = Request::int('dow', intval(date('N')));
                $this->entry->setFormattedStart(Request::get('start', date('H:00', time() + 3600)));
                $this->entry->setFormattedEnd(Request::get('end', date('H:00', time() + 7200)));
            }

            PageLayout::setTitle(_('Neuer Termin'));
        } else {
            //Edit mode
            $this->entry = ScheduleEntry::find($entry_id);
            if (!$this->entry) {
                PageLayout::postError(_('Der Termin wurde nicht gefunden.'));
            }
            if ($this->entry->user_id !== $GLOBALS['user']->id) {
                //"Hey, this is private! Mmmmmmm!" (moves flat hand away from body)
                throw new AccessDeniedException(_('Sie dürfen diesen Termin nicht bearbeiten!'));
            }

            PageLayout::setTitle($this->entry->toString());
        }

        if (Request::submitted('save')) {
            CSRFProtection::verifyUnsafeRequest();

            $this->entry->dow = Request::int('dow', intval(date('N')));
            $this->entry->setFormattedStart(Request::get('start'));
            $this->entry->setFormattedEnd(Request::get('end'));
            $this->entry->label   = Request::get('label', '');
            $this->entry->content = Request::get('content', '');

            if (intval($this->entry->start_time) >= intval($this->entry->end_time)) {
                PageLayout::postError(_('Der Startzeitpunkt darf nicht nach dem Endzeitpunkt liegen!'));
                return;
            }

            if ($this->entry->store() !== false) {
                if ($entry_id === 'add') {
                    PageLayout::postSuccess(_('Der Termin wurde hinzugefügt.'));
                } else {
                    PageLayout::postSuccess(_('Der Termin wurde bearbeitet.'));
                }
                if (Request::isDialog()) {
                    $this->response->add_header('X-Dialog-Close', '1');
                } else {
                    $this->redirect('calendar/schedule/index');
                }
            } else {
                if ($entry_id === 'add') {
                    PageLayout::postError(_('Der Termin konnte nicht hinzugefügt werden.'));
                } else {
                    PageLayout::postError(_('Der Termin konnte nicht bearbeitet werden.'));
                }
            }
        } elseif (Request::submitted('delete')) {
            CSRFProtection::verifyUnsafeRequest();
            if ($this->entry->delete()) {
                PageLayout::postSuccess(_('Der Termin wurde gelöscht.'));
            } else {
                PageLayout::postError(_('Der Termin konnte nicht gelöscht werden.'));
            }
            if (Request::isDialog()) {
                $this->response->add_header('X-Dialog-Close', '1');
            } else {
                $this->redirect('calendar/schedule/index');
            }
        }
    }

    public function course_info_action(string $seminar_id)
    {
        $this->course = Course::find($seminar_id);
        if (!$this->course) {
            PageLayout::postError(_('Die Veranstaltung wurde nicht gefunden.'));
            return;
        }
        $this->membership = CourseMember::findOneBySQL(
            '`seminar_id` = :course_id AND `user_id` = :user_id',
            [
                'course_id' => $this->course->id,
                'user_id'   => $GLOBALS['user']->id
            ]
        );
        if (!$this->membership) {
            throw new AccessDeniedException();
        }
        PageLayout::setTitle($this->course->getFullName());

        if (Request::isPost()) {
            CSRFProtection::verifyUnsafeRequest();
            $success = false;
            if (Request::submitted('hide')) {
                //Hide the course.
            } elseif (Request::submitted('save')) {
                //Save the selected group.
                $selected_groups = Request::getArray('gruppe');
                if (array_key_exists($this->course->id, $selected_groups)) {
                    $this->membership->gruppe = $selected_groups[$this->course->id] ?? '0';
                }
                $success = $this->membership->store() !== false;
                if ($success) {
                    PageLayout::postSuccess(_('Die Farbe der Veranstaltung wurde geändert.'));
                } else {
                    PageLayout::postError(_('Die Farbe der Veranstaltung konnte nicht geändert werden.'));
                }
            }
            if ($success) {
                if (Request::isDialog()) {
                    $this->response->add_header('X-Dialog-Close', '1');
                } else {
                    $this->redirect('calendar/schedule/index');
                }
            }
        }
    }

    public function mark_course_action(string $course_id)
    {
        $course = Course::find($course_id);
        if ($course->isStudygroup()) {
            throw new AccessDeniedException();
        }
        $entry = ScheduleCourseDate::findOneBySQL(
            '`course_id` = :course_id AND `user_id` = :user_id',
            [
                'course_id' => $course_id,
                'user_id'   => $GLOBALS['user']->id
            ]
        );
        if ($entry) {
            PageLayout::postInfo(_('Die Veranstaltung wurde bereits zum Stundenplan hinzugefügt.'));
        } else {
            $entry = new ScheduleCourseDate();
            $entry->course_id   = $course->id;
            $entry->user_id     = $GLOBALS['user']->id;
            $entry->metadate_id = '';
            $entry->visible     = '1';
            if ($entry->store() !== false) {
                PageLayout::postSuccess(_('Die Veranstaltung wurde zum Stundenplan hinzugefügt.'));
            } else {
                PageLayout::postError(_('Die Veranstaltung konnte nicht zum Stundenplan hinzugefügt werden.'));
            }
        }
        $this->redirect('calendar/schedule/index');
    }
}
