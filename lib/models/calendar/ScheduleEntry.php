<?php
/**
 * ScheduleEntry.php - Model class for regular dates
 * in the schedule view that are not bound to a course.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Moritz Strohm <strohm@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @since       6.0
 */


/**
 * The ScheduleEntry model represents regular dates that are
 * displayed only in the schedule of a user.
 *
 * @property string id database column
 * @property string start database column
 * @property string end database column
 * @property string day database column
 * @property string title database column
 * @property string content database column
 * @property string color database column
 * @property string user_id database column
 */
class ScheduleEntry extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'schedule';
        $config['belongs_to']['user'] = [
            'class_name'  => User::class,
            'foreign_key' => 'user_id'
        ];
        parent::configure($config);
    }

    /**
     * Formats the start time for human-readable output.
     *
     * @return string The start time in the format HH:mm or an empty string in case
     *      the format stored in the start attribute is not supported.
     */
    public function getFormattedStart() : string
    {
        if (strlen($this->start) === 3) {
            return '0' . $this->start[0] . ':' . $this->start[1] . ':' . $this->start[2];
        } elseif (strlen($this->start) === 4) {
            return $this->start[0] . $this->start[1] . ':' . $this->start[2] . $this->start[3];
        }
        //Invalid date format:
        return '';
    }

    /**
     * Formats the end time for human-readable output.
     *
     * @return string The end time in the format HH:mm or an empty string in case
     *     the format stored in the end attribute is not supported.
     */
    public function getFormattedEnd() : string
    {
        if (strlen($this->end) === 3) {
            return '0' . $this->end[0] . ':' . $this->end[1] . ':' . $this->end[2];
        } elseif (strlen($this->end) === 4) {
            return $this->end[0] . $this->end[1] . ':' . $this->end[2] . $this->end[3];
        }
        //Invalid date format:
        return '';
    }
}
