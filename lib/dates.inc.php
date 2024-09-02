<?php
/**
 * dates.inc.php - basale Routinen zur Terminveraltung.
 * Copyright (C) 2001 Stefan Suchi <suchi@gmx.de>, André Noack <anoack@mcis.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 **/

require_once 'lib/calendar_functions.inc.php';

/**
 * getWeekday liefert einen String mit einem Tagesnamen.
 *
 * @param int $day_num integer PHP-konformer Tag (0-6)
 * @param bool $short boolean Wenn gesetzt wird der Tag verkürzt zurückgegeben.
 *
 * @throws Exception
 */
function getWeekday(int $day_num, bool $short = true): string
{
    if ($day_num < 0 || $day_num > 6) {
        throw new Exception('Invalid day number');
    }
    return match($day_num) {
        0 => $short ? _('So') : _('Sonntag'),
        1 => $short ? _('Mo') : _('Montag'),
        2 => $short ? _('Di') : _('Dienstag'),
        3 => $short ? _('Mi') : _('Mittwoch'),
        4 => $short ? _('Do') : _('Donnerstag'),
        5 => $short ? _('Fr') : _('Freitag'),
        6 => $short ? _('Sa') : _('Samstag'),
    };
}


/**
 * getMonthName returns the localized name of a certain month in
 * either the abbreviated form (default) or as the actual name.
 *
 * @param string $month Month number
 * @param bool $short Display the abbreviated version or the actual
 *                    name of the month (defaults to abbreviated)
 * @return string Month name
 * @throws Exception when passed an invalid month number
 */
function getMonthName(string $month, bool $short = true): string
{
    $month = (int)$month;

    $months = [
        1  => [_('Januar'), _('Jan.')],
        2  => [_('Februar'), _('Feb.')],
        3  => [_('März'), _('März')],
        4  => [_('April'), _('Apr.')],
        5  => [_('Mai'), _('Mai')],
        6  => [_('Juni'), _('Juni')],
        7  => [_('Juli'), _('Juli')],
        8  => [_('August'), _('Aug.')],
        9  => [_('September'), _('Sep.')],
        10 => [_('Oktober'), _('Okt.')],
        11 => [_('November'), _('Nov.')],
        12 => [_('Dezember'), _('Dez.')],
    ];
    if (!isset($months[$month])) {
        throw new Exception("Invalid month '{$month}'");
    }
    return $months[$month][(int)$short];
}

function leadingZero(string $num): string
{
    if ($num == '') return '00';
    if (mb_strlen($num) < 2) {
        return '0' . $num;
    } else {
        return $num;
    }
}


/**
 * The function shrink_dates expects an array of dates where the start_time and the end_time is noted
 * and creates a compressed version (spanning f.e. multiple dates).
 *
 * Returns an array, where each element is one condensed entry. (f.e. 10.6 - 14.6 8:00 - 12:00)
 */
function shrink_dates(array $dates): array
{
    $ret = [];

    // First step: Clean out all duplicate dates (the dates are sorted)
    foreach ($dates as $key => $date) {
        if (isset($dates[$key + 1])) {
            if ($dates[$key + 1]['start_time'] === $date['start_time']
                && $dates[$key + 1]['end_time'] === $date['end_time']) {
                unset($dates[$key]);
            }
        }
    }

    // Second step: Make sure the dates are still ordered by start- and end-time without any holes
    usort($dates, function ($a, $b) {
        if ($a['start_time'] === $b['start_time']) {
            if ($a['end_time'] === $b['end_time']) return 0;
            return ($a['end_time'] > $b['end_time']) ? 1 : -1;
        }

        return ($a['start_time'] > $b['start_time']) ? 1 : -1;
    });

    // Third step: Check which dates are follow-ups
    for ($i = 1; $i < sizeof($dates); $i++) {
        if (((date('G', $dates[$i - 1]['start_time'])) === date('G', $dates[$i]['start_time']))
            && ((date('i', $dates[$i - 1]['start_time'])) === date('i', $dates[$i]['start_time']))
            && ((date('G', $dates[$i - 1]['end_time'])) === date('G', $dates[$i]['end_time']))
            && ((date('i', $dates[$i - 1]['end_time'])) === date('i', $dates[$i]['end_time']))) {
            $dates[$i]['time_match'] = true;
        }

        if (((date('z', $dates[$i]['start_time']) - 1) === date('z', $dates[$i - 1]['start_time']))
            || ((date('z', $dates[$i]['start_time']) == 0) && (date('j', $dates[$i - 1]['start_time']) == 0))) {
            if (!empty($dates[$i]['time_match'])) {
                $dates[$i]['conjuncted'] = true;
            }
        }
    }

    // Fourth step: aggregate the dates with follow-ups
    $return_string = '';
    // create text-output
    for ($i = 0; $i < count($dates); $i++) {
        $conjuncted = true;
        if (empty($dates[$i]['conjuncted'])) {
            $conjuncted = false;
        }

        if (empty($dates[$i]['conjuncted']) || empty($dates[$i + 1]['conjuncted'])) {
            $return_string .= strftime(' %A, %d.%m.%Y', $dates[$i]['start_time']);
        }

        if (!$conjuncted && !empty($dates[$i + 1]['conjuncted'])) {
            $return_string .= ' -';
            $conjuncted    = true;
        } else if (empty($dates[$i + 1]['conjuncted']) && !empty($dates[$i + 1]['time_match'])) {
            $return_string .= ',';
        }

        if (empty($dates[$i + 1]['time_match'])) {
            // check if the current date is for a whole day
            if ((($dates[$i]['end_time'] - $dates[$i]['start_time']) / 60 / 60) > 23) {
                $return_string .= ' (' . _('ganztägig') . ')';
            } else {
                $return_string .= ' ' . date('H:i', $dates[$i]['start_time']);
                if (date('H:i', $dates[$i]['start_time']) != date('H:i', $dates[$i]['end_time'])) {
                    $return_string .= ' - ' . date('H:i', $dates[$i]['end_time']);
                }
            }
        }

        if ($return_string != '' && empty($dates[$i + 1]['conjuncted']) && empty($dates[$i + 1]['time_match'])) {
            $ret[]         = $return_string;
            $return_string = '';
        }
    }

    return $ret;
}

/**
 * The preliminary meeting function checks whether there is a preliminary meeting and in this case,
 * returns the corresponding preliminary meeting location. Otherwise FALSE is returned.
 *
 * @param string $seminar_id
 * @param string $type
 * @return false|string|null
 */

function vorbesprechung(string $seminar_id, string $type = 'standard'): false|string|null
{
    $termin_id = DBManager::get()->fetchColumn(
        "SELECT termin_id FROM termine WHERE range_id = ? AND date_typ = '2' ORDER BY date",
        [$seminar_id]
    );

    if (!$termin_id) {
        return false;
    }

    $termin = new CourseDate($termin_id);
    $ret = (string) $termin;
    if (!empty($termin->room_booking->resource)) {
        $ret .= ', '._("Ort:").' ';
        switch ($type) {
            case 'export':
                $ret .= $termin->room_booking->resource->name;
                break;

            case 'standard':
            default:
                $ret .= '<a href="' . $termin->room_booking->resource->getActionLink('show') . '" data-dialog>'
                    . htmlReady($termin->room_booking->resource->name) . '</a>';
                break;
        }
    }
    return $ret;
}

/**
 * a small helper funktion to get the type query for "Sitzungstermine"
 * (this dates are important to get the regularly, presence dates
 * for a seminar
 *
 * @return string  the SQL-clause to select only the "Sitzungstermine"
 *
 */
function getPresenceTypeClause(): string
{
    $i          = 0;
    $typ_clause = "(";
    foreach ($GLOBALS['TERMIN_TYP'] as $key => $val) {
        if ($val['sitzung']) {
            if ($i) {
                $typ_clause .= ", ";
            }
            $typ_clause .= "'" . $key . "' ";
            $i++;
        }
    }
    $typ_clause .= ")";

    return $typ_clause;
}

/**
 * Return an array of room snippets, possibly linked
 *
 * @param array $rooms an associative array of rooms
 * @param bool $html true if you want links, otherwise false
 *
 * @return array  an array of (formatted) room snippets
 */
function getFormattedRooms(array $rooms, bool $link = false): array
{
    $room_list = [];

    foreach ($rooms as $room_id => $count) {
        if ($room_id && Config::get()->RESOURCES_ENABLE) {
            $room = Room::find($room_id);
            if ($link) {
                $room_list[] = '<a href="' . $room->getActionLink() . '" data-dialog="1">'
                    . htmlReady($room->name) . '</a>';
            } else {
                $room_list[] = htmlReady($room->name);
            }
        }
    }

    return $room_list;
}

/**
 * Return an array of room snippets without any formatting
 *
 * @param array $rooms an associative array of rooms
 *
 * @return array  an array of room snippets
 */
function getPlainRooms(array $rooms): array
{
    $room_list = [];

    foreach ($rooms as $room_id => $count) {
        if ($room_id) {
            $room        = Room::find($room_id);
            $room_list[] = $room->name;
        }
    }

    return $room_list;
}


/**
 * @param string $comment
 * @param array $dates SingleDate
 */
function raumzeit_send_cancel_message($comment, $dates)
{
    if (!is_array($dates)) {
        $dates = [$dates];
    }
    $course = Course::find($dates[0]->range_id);
    if ($course) {
        $subject = sprintf(_('[%s] Terminausfall'), $course->name);
        $recipients = $course->members->pluck('username');
        $lecturers = $course->members->findBy('status', 'dozent')->pluck('nachname');
        $message = sprintf(
            ngettext(
                _('In der Veranstaltung %s fällt der folgende Termine aus:'),
                _('In der Veranstaltung %s fallen die folgenden Termine aus:'),
                count($dates)
            ),
            $course->name . ' (' . implode(',', $lecturers) . ') ' . $course->start_semester->name
        );
        $message .= "\n\n- ";
        $message .= implode("\n- " , array_map(fn($a) => (string) $a, $dates));
        if ($comment) {
            $message .= "\n\n" . $comment;
        }
        $msg = new messaging();
        return $msg->insert_message($message, $recipients, '____%system%____', '', '', '', '', $subject, true);
    }
}
