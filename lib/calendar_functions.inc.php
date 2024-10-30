<?
# Lifter002: TODO
# Lifter007: TODO
# Lifter003: TODO
# Lifter010: TODO
/**
* calendar_functions.inc.php
*
* basic calendar functions
*
* @author       Peter Thienel <pthienel@web.de>
*   @access     public
* @package      studip_core
* @modulegroup      library
* @module       calendar_functions
*/

// +---------------------------------------------------------------------------+
// This file is part of Stud.IP
// calendar_functions.inc.php
//
// Copyright (C) 2001 Peter Thienel <pthienel@web.de>
// +---------------------------------------------------------------------------+
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or any later version.
// +---------------------------------------------------------------------------+
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
// +---------------------------------------------------------------------------+


/**
 * Hier jezt die ultimative Feiertags-"Berechnung"
 * Zurueckgegeben wird ein Array mit Namen des Feiertages ("name") und
 * Faerbungsgrad ("col", 1 bis 3; 3 bedeutet Termin fällt aus).
 *
 * @param $tmstamp
 * @return array{name: string, col: int}|false
 * @see Holidays::isHoliday()
 */
function holiday ($tmstamp) {
    return Holidays::isHoliday($tmstamp);
}

// ueberprueft eine Datumsangabe, die in einen Timestamp gewandelt werden soll
// gibt bei Erfolg den timestamp zurück mit DST
function check_date ($month, $day, $year, $hour = 0, $min = 0) {
    if (!preg_match("/^\d{1,2}$/", $day) || !preg_match("/^\d{1,2}$/", $month)
            || !preg_match("/^\d{1,2}$/", $hour) || !preg_match("/^\d{1,2}$/", $min)
            || !preg_match("/^\d{4}$/", $year)) {
        return FALSE;
    }
    if ($year < 1970 || $year > 2036)
        return FALSE;
    if (!checkdate($month, $day, $year))
        return FALSE;
    if ($hour > 23 || $hour < 0 || $min > 59 || $min < 0)
        return FALSE;

    return mktime($hour, $min, 0, $month, $day, $year);
}



/**
 * checks values that shall become a single date with start- and endtime
 *
 * @param string $day
 * @param string $month
 * @param string $year
 * @param string $start_hour
 * @param string $start_minute
 * @param string $end_hour
 * @param string $end_minute
 *
 * @return bool true if date is valid, false otherwise
 */
function check_singledate( $day, $month, $year, $start_hour, $start_minute, $end_hour, $end_minute ) {

    // check start-date
    $start = check_date($month, $day, $year, $start_hour, $start_minute);
    if (!$start) return false;

    // check end-date
    $end = check_date($month, $day, $year, $end_hour, $end_minute);
    if (!$end) return false;

    // check, that end-date is not before start_date
    return ($end > $start);
}
