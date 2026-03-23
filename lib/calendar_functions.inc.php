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
 *
 * @deprecated Will be removed with Stud.IP 7.0.
 */
function holiday ($tmstamp) {
    return Holidays::isHoliday($tmstamp);
}
