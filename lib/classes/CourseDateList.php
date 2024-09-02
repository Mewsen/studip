<?php
/**
 * CourseDateList.class.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @copyright   2023-2024
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

/**
 * The CourseDateCollection class helps with managing all types of
 * course dates: regular, irregular and cancelled course dates.
 * It provides helper methods for getting all the time ranges from
 * the different date type instances it contains.
 */
class CourseDateList implements Stringable
{
    /**
     * @var array $regular_dates contains regular course dates.
     */
    protected array $regular_dates = [];

    /**
     * @var array $single_dates contains single course dates.
     * These can be part of a regular course date, or they can
     * be irregular dates, depending on the use case of the
     * CourseDateCollection instance.
     */
    protected array $single_dates = [];

    /**
     * @var array $cancelled_dates contains cancelled course dates.
     */
    protected array $cancelled_dates = [];

    public function addRegularDate(SeminarCycleDate $regular_date)
    {
        $this->regular_dates[] = $regular_date;
    }

    public function addSingleDate(CourseDate $date)
    {
        $this->single_dates[] = $date;
    }

    public function addCancelledDate(CourseExDate $cancelled_date)
    {
        $this->cancelled_dates[] = $cancelled_date;
    }

    public function isEmpty() : bool
    {
        return empty($this->regular_dates)
            && empty($this->single_dates)
            && empty($this->cancelled_dates);
    }

    public function getAllRegularDates() : array
    {
        return $this->regular_dates;
    }

    /**
     * Compares two regular dates by looking at their sort position, their weekday
     * and their start hour. If the first date is less than the second date in those
     * attributes, -1 is returned. In the oppsite case, 1 is returted. In case both
     * regular dates are equal, 0 is returned.
     *
     * @param SeminarCycleDate $a The first regular date to compare.
     *
     * @param SeminarCycleDate $b The second regular date to compare.
     *
     * @return int The result of the comparison result: -1, 0 or 1.
     */
    public static function compareRegularDates(SeminarCycleDate $a, SeminarCycleDate $b) : int
    {
        return $a->sorter - $b->sorter
            ?: $a->weekday - $b->weekday
            ?: $a->start_hour - $b->start_hour;
    }

    /**
     * @see CourseDateList::compareSingleDatesOrCancelledDates
     */
    public static function compareSingleDates(CourseDate $a, CourseDate $b) : int
    {
        return self::compareSingleDatesOrCancelledDates($a, $b);
    }

    /**
     * @see CourseDateList::compareSingleDatesOrCancelledDates
     */
    public static function compareCancelledDates(CourseExDate $a, CourseExDate $b) : int
    {
        return self::compareSingleDatesOrCancelledDates($a, $b);
    }

    /**
     * Compares two single dates or cancelled dates. If the first date starts before the second,
     * it is considered less than the second date and -1 is returned. In the opposite
     * case, it is considered more than the second date and 1 is returned. In case both
     * dates start on the exact same point in time, the end time of both dates is compared
     * in the same manner. Only in the case that the end time is also equal, both dates
     * are considered equal and 0 is returned.
     *
     * @param $a The first date for the comparison.
     *
     * @param $b The second date for the comparison.
     *
     * @return int -1 if a < b, 0 if a == b and 1 if a > b.
     */
    protected static function compareSingleDatesOrCancelledDates($a, $b) : int
    {
        return $a->date - $b->date
            ?: $a->end_time - $b->end_time;
    }

    /**
     * Sorts all dates that are present in this collection.
     *
     * @return void
     */
    public function sort() : void
    {
        uasort($this->regular_dates, self::compareRegularDates(...));
        uasort($this->single_dates, self::compareSingleDates(...));
        uasort($this->cancelled_dates, self::compareCancelledDates(...));
    }

    public function getRegularDates() : array
    {
        return $this->regular_dates;
    }

    public function getSingleDates(
        bool $include_regular_dates = false,
        bool $include_cancelled_dates = false,
        bool $sorted = false
    ): array {
        if ($include_regular_dates) {
            $all_single_dates = [];
            foreach ($this->regular_dates as $regular_date) {
                foreach ($regular_date->dates as $date) {
                    $all_single_dates[] = $date;
                }
            }
            $all_single_dates = array_merge($all_single_dates, $this->single_dates);
            if ($include_cancelled_dates) {
                $all_single_dates = array_merge($all_single_dates, $this->cancelled_dates);
            }
            if ($sorted) {
                uasort($all_single_dates, self::compareSingleDatesOrCancelledDates(...));
            }
            return $all_single_dates;
        } else {
            if ($include_cancelled_dates || $sorted) {
                $all_single_dates = $this->single_dates;
                if ($include_cancelled_dates) {
                    $all_single_dates = array_merge($all_single_dates, $this->cancelled_dates);
                }
                if ($sorted) {
                    uasort($all_single_dates, self::compareSingleDatesOrCancelledDates(...));
                }
                return $all_single_dates;
            } else {
                return $this->single_dates;
            }
        }
    }

    public function getCancelledDates() : array
    {
        return $this->cancelled_dates;
    }

    public function toHtml(
        bool $group_by_rooms = false,
        bool $with_room_names = false,
        bool $with_cancelled_dates = false
    ) : string {
        if ($this->isEmpty()) {
            return _('Die Zeiten der Veranstaltung stehen nicht fest.');
        }

        $template = null;
        if ($group_by_rooms) {
            $grouped_dates = [];
            foreach ($this->regular_dates as $regular_date) {
                $room = $regular_date->getMostBookedRoom();
                if ($room instanceof Room) {
                    if (!array_key_exists($room->name, $grouped_dates)) {
                        $grouped_dates[$room->name] = new CourseDateList();
                    }
                    $grouped_dates[$room->name]->addRegularDate($regular_date);
                } else {
                    if (!array_key_exists(_('Ohne Raum'), $grouped_dates)) {
                        $grouped_dates[_('Ohne Raum')] = new CourseDateList();
                    }
                    $grouped_dates[_('Ohne Raum')]->addRegularDate($regular_date);
                }
            }
            foreach ($this->single_dates as $date) {
                $room_name = $date->getRoomName();
                if ($room_name) {
                    if (!array_key_exists($room_name, $grouped_dates)) {
                        $grouped_dates[$room_name] = new CourseDateList();
                    }
                    $grouped_dates[$room_name]->addSingleDate($date);
                } else {
                    if (!array_key_exists(_('Ohne Raum'), $grouped_dates)) {
                        $grouped_dates[_('Ohne Raum')] = new CourseDateList();
                    }
                    $grouped_dates[_('Ohne Raum')]->addSingleDate($date);
                }
            }
            if ($with_cancelled_dates) {
                foreach ($this->cancelled_dates as $date) {
                    if (!array_key_exists(_('Ohne Raum'), $grouped_dates)) {
                        $grouped_dates[_('Ohne Raum')] = new CourseDateList();
                    }
                    $grouped_dates[_('Ohne Raum')]->addCancelledDate($date);
                }
            }
            $template = $GLOBALS['template_factory']->open('dates/room_grouped_course_date_list');
            $template->grouped_dates = $grouped_dates;
        } else {
            $template = $GLOBALS['template_factory']->open('dates/course_date_list');
            $template->with_room_names = $with_room_names;
            $template->collection = $this;
        }
        $template->with_cancelled_dates = $with_cancelled_dates;
        return $template->render();
    }

    public function toStringArray(bool $group_by_rooms = false, bool $with_room_names = false, bool $with_cancelled_dates = false) : array
    {
        $output = [];
        foreach ($this->regular_dates as $regular_date) {
            $date_line = $regular_date->toString('long-start');
            if ($with_room_names || $group_by_rooms) {
                $room = $regular_date->getMostBookedRoom();
                if ($room instanceof Room) {
                    if ($with_room_names) {
                        $date_line .= ' (' . sprintf(_('Raum %s'), $room->name) . ')';
                        $output[] = $date_line;
                    } else {
                        //Group by rooms.
                        if (!is_array($output[$room->name])) {
                            $output[$room->name] = [];
                        }
                        $output[$room->name][] = $date_line;
                    }
                } elseif ($group_by_rooms) {
                    //Use the "null" room name:
                    $null_room_name = _('Kein Raum');
                    if (!is_array($output[$null_room_name])) {
                        $output[$null_room_name] = [];
                    }
                    $output[$null_room_name][] = $date_line;
                }
            } else {
                $output[] = $date_line;
            }
        }
        foreach ($this->single_dates as $single_date) {
            $date_line = $single_date->getFullName($with_room_names ? 'long-include-room' : 'long');
            if ($group_by_rooms) {
                $room_name = _('Kein Raum');
                if ($single_date->room instanceof Room) {
                    $room_name = $single_date->room->name;
                }
                if (!is_array($output[$room_name])) {
                    $output[$room_name] = [];
                }
                $output[$room_name][] = $date_line;
            } else {
                $output[] = $date_line;
            }
        }
        if ($with_cancelled_dates) {
            foreach ($this->cancelled_dates as $cancelled_date) {
                if ($group_by_rooms) {
                    $room_name = _('Kein Raum');
                    if (!is_array($output[$room_name])) {
                        $output[$room_name] = [];
                    }
                    $output[$room_name][] = $cancelled_date->toString();
                } else {
                    $output[] = $cancelled_date->toString();
                }
            }
        }
        if ($group_by_rooms) {
            ksort($output);
            $flat_output = [];
            foreach ($output as $room_name => $date_lines) {
                $flat_output[] = sprintf('%s:', $room_name);
                $flat_output = array_merge($flat_output, $date_lines);
                //Separate the grouped output with an empty string:
                $flat_output[] = '';
            }
            return $flat_output;
        } else {
            return $output;
        }
    }


    public function __toString()
    {
        if ($this->isEmpty()) {
            return _('Die Zeiten der Veranstaltung stehen nicht fest.');
        }

        return implode("\n", $this->toStringArray());
    }
}
