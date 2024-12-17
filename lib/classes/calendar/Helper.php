<?php

namespace Studip\Calendar;

class Helper
{
    /**
     * Retrieves the time slot duration in the calendar for a specified calendar type
     * and either the current user or a specific user.
     *
     * @param string $calendar_type The calendar type for which to retrieve the slot duration.
     *     Valid values: 'week', 'day', 'week_group' (week group calendar), 'week_day' (day group calendar).
     *     Defaults to 'week'.
     * @param string $user_id The user for which to retrieve the slot duration. Defaults to an
     *     empty string which then in turn means the current users slot duration is retrieved.
     *
     * @return string The slot duration as a time string in the form HH:MM:SS.
     */
    public static function getCalendarSlotDuration(string $calendar_type = 'week', string $user_id = '') : string
    {
        $default_slot_duration = '00:30:00';

        $user_config = new \UserConfig($user_id ?: $GLOBALS['user']->id);
        $calendar_settings = $user_config->CALENDAR_SETTINGS;

        if (
            $calendar_type === 'week'
            && !empty($calendar_settings['step_week'])
        ) {
            $step_week = (int) $calendar_settings['step_week'];
            $hours = floor($step_week / 3600);
            $minutes = round(($step_week - $hours * 3600) / 60);
            return sprintf('%1$02u:%2$02u:00', $hours, $minutes);
        } elseif (
            $calendar_type === 'day'
            && !empty($calendar_settings['step_day'])
        ) {
            $step_day = (int) $calendar_settings['step_day'];
            $hours = floor($step_day / 3600);
            $minutes = round(($step_day - $hours * 3600) / 60);
            return sprintf('%1$02u:%2$02u:00', $hours, $minutes);
        } elseif (
            $calendar_type === 'week_group'
            && !empty($calendar_settings['step_week_group'])
        ) {
            $step_week = (int) $calendar_settings['step_week_group'];
            $hours = floor($step_week / 3600);
            $minutes = round(($step_week - $hours * 3600) / 60);
            return sprintf('%1$02u:%2$02u:00', $hours, $minutes);
        } elseif (
            $calendar_type === 'day_group'
            && !empty($calendar_settings['step_day_group'])
        ) {
            $step_day = (int) $calendar_settings['step_day_group'];
            $hours = floor($step_day / 3600);
            $minutes = round(($step_day - $hours * 3600) / 60);
            return sprintf('%1$02u:%2$02u:00', $hours, $minutes);
        }

        // An unknown slot type or no appropriate match before:
        // Return the default duration.
        return $default_slot_duration;
    }


    /**
     * Retrieves the default calendar date by various methods.
     *
     * @return \DateTime The default date for the calendar.
     *     This defaults to the current date if no other date
     *     can be retrieved.
     */
    public static function getDefaultCalendarDate() : \DateTime
    {
        $default_date = new \DateTime();
        if (\Request::submitted('date') || \Request::submitted('defaultDate')) {
            $parameter_name = 'date';
            if (\Request::submitted('defaultDate')) {
                $parameter_name = 'defaultDate';
            }
            $date = \Request::getDateTime($parameter_name, 'Y-m-d');
            if ($date instanceof \DateTime) {
                $default_date = $date;
                //Update the session value:
                $_SESSION['calendar_date'] = $default_date->format('Y-m-d');
            }
        } elseif (\Request::submitted('semester_id')) {
            //A semester-ID is set, but no specific date that would override it.
            //Use the first lecture week of the semester as default date.
            $semester_id = \Request::option('semester_id');
            $semester = \Semester::find($semester_id);
            if ($semester) {
                $default_date->setTimestamp($semester->vorles_beginn);
                //Update the session value:
                $_SESSION['calendar_date'] = $default_date->format('Y-m-d');
            }
        } elseif (!empty($_SESSION['calendar_date'])) {
            $date = \DateTime::createFromFormat(
                'Y-m-d',
                $_SESSION['calendar_date'],
                $default_date->getTimezone()
            );
            if ($date instanceof \DateTime) {
                $default_date = $date;
            }
        }
        $default_date->setTime(0,0,0);

        return $default_date;
    }

    /**
     * Constructs a Fullcalendar instance of the schedule for the current user.
     *
     * @param string $semester_id The ID of the semester to be used. Defaults to an empty string
     *     which in turn means that the current semester shall be used.
     *
     * @param bool $show_hidden_courses Whether to include hidden courses in the schedule (true)
     *     or not (false). Defaults to false.
     *
     * @return \Studip\Fullcalendar A fullcalendar instance for the schedule of the current user.
     */
    public static function getScheduleFullcalendar(
        string $semester_id = '',
        bool $show_hidden_courses = false
    ) : \Studip\Fullcalendar
    {
        if (!$semester_id) {
            $semester_id = \Semester::findCurrent()?->id ?? '';
        }
        $schedule_settings = \UserConfig::get(\User::findCurrent()->id)->getValue('SCHEDULE_SETTINGS') ?? [];
        $slot_duration = '00:30:00';
        if (!empty($schedule_settings['size']) && in_array($schedule_settings['size'], ['small', 'large'])) {
            if ($schedule_settings['size'] === 'small') {
                $slot_duration = '01:00:00';
            } elseif ($schedule_settings['size'] === 'large') {
                $slot_duration = '00:15:00';
            }
        }

        //Determine the value of the hiddenDays config.
        //In case no visible days are set, default to hide Saturday and Sunday.
        $hidden_days = [6, 7];
        if (!empty($schedule_settings['visible_days'])) {
            $hidden_days = [1, 2, 3, 4, 5, 6, 7];
            $hidden_days = array_diff(
                $hidden_days,
                $schedule_settings['visible_days']
            );
        }

        $fullcalendar_hidden_days = [];
        foreach ($hidden_days as $day) {
            if ($day === 7) {
                $fullcalendar_hidden_days[] = 0;
            } else {
                $fullcalendar_hidden_days[] = $day;
            }
        }

        return new \Studip\Fullcalendar(
            _('Stundenplan'),
            [
                'editable'    => true,
                'selectable'  => true,
                'dialog_size' => 'auto',
                'minTime'     => $schedule_settings['start_time'] ?? '08:00',
                'maxTime'     => $schedule_settings['end_time'] ?? '20:00',
                'allDaySlot'  => false,
                'header'      => [
                    'left' => '',
                    'right' => ''
                ],
                'views' => [
                    'timeGridWeek' => [
                        'columnHeaderFormat' => ['weekday' => 'short'],
                        'slotDuration'       => $slot_duration
                    ]
                ],
                'columnHeaderFormat' => ['weekday' => 'short'],
                'defaultView' => 'timeGridWeek',
                'defaultDate' => date('Y-m-d'),
                'slotLabelFormat' => [
                    'hour'           => 'numeric',
                    'minute'         => '2-digit',
                    'omitZeroMinute' => false
                ],
                'weekends'   => true,
                'hiddenDays' => $fullcalendar_hidden_days,
                'timeGridEventMinHeight' => 20,
                'eventSources' => [
                    [
                        'url' => \URLHelper::getURL(
                            'dispatch.php/calendar/schedule/data',
                            ['show_hidden' => $show_hidden_courses]
                        ),
                        'method' => 'GET',
                        'extraParams' => [
                            'semester_id' => $semester_id,
                            'full_semester_time_range' => false
                        ]
                    ]
                ],
                'studip_urls' => [
                    'add' => \URLHelper::getURL('dispatch.php/calendar/schedule/entry/add')
                ]
            ]
        );
    }
}
