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
class ScheduleEntry extends SimpleORMap implements Event
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
     * A helper method to set the content of the start attribute by a formatted date
     * in the format HH:mm.
     *
     * @param string $formatted_start The formatted date in the format HH:mm.
     *
     * @return void
     */
    public function setFormattedStart(string $formatted_start) : void
    {
        $this->start = implode('', explode(':', $formatted_start));
    }

    /**
     * A helper method to set the content of the end attribute by a formatted date
     * in the format HH:mm.
     *
     * @param string $formatted_end The formatted date in the format HH:mm.
     *
     * @return void
     */
    public function setFormattedEnd(string $formatted_end) : void
    {
        $this->end = implode('', explode(':', $formatted_end));
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

    public static function getEvents(DateTime $begin, DateTime $end, string $range_id): array
    {
        return self::findBySQL(
            "`user_id` = :range_id
            AND `start` < :end AND `end` > :start
            AND `day` >= :start_day AND day <= :end_day",
            [
                'range_id'  => $range_id,
                'start'     => $begin->format('Hi'),
                'end'       => $end->format('Hi'),
                'start_day' => $begin->format('N'),
                'end_day'   => $end->format('N')
            ]
        );
    }

    public function getObjectId(): string
    {
        return $this->id;
    }

    public function getPrimaryObjectID(): string
    {
        return $this->user_id;
    }

    public function getObjectClass(): string
    {
        return self::class;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getBegin(): DateTime
    {
        //Map the entry to the current week:
        $date = new DateTime();
        $date->setTimestamp(strtotime('midnight this monday'));
        if (intval($this->day) > 1) {
            $days_to_add = intval($this->day) - 1;
            $date = $date->add(new DateInterval(sprintf('P%dD', $days_to_add)));
        }
        $time_parts = explode(':', $this->getFormattedStart());
        $date->setTime(intval($time_parts[0]), intval($time_parts[1]));
        return $date;
    }

    public function getEnd(): DateTime
    {
        //Map the entry to the current week:
        $date = new DateTime();
        $date->setTimestamp(strtotime('midnight this monday'));
        if (intval($this->day) > 1) {
            $days_to_add = intval($this->day) - 1;
            $date = $date->add(new DateInterval(sprintf('P%dD', $days_to_add)));
        }
        $time_parts = explode(':', $this->getFormattedEnd());
        $date->setTime(intval($time_parts[0]), intval($time_parts[1]));
        return $date;
    }

    public function getDuration(): DateInterval
    {
        $begin = $this->getBegin();
        $end   = $this->getEnd();
        return $begin->diff($end);
    }

    public function getLocation(): string
    {
        //No location supported.
        return '';
    }

    public function getUniqueId(): string
    {
        return sprintf('%1$s_%2$s_%3$s', Config::get()->STUDIP_INSTALLATION_ID, self::class, $this->id);
    }

    public function getDescription(): string
    {
        return $this->content;
    }

    public function getAdditionalDescriptions(): array
    {
        //No additional description supported.
        return [];
    }

    public function isAllDayEvent(): bool
    {
        return $this->start === '000' && $this->end === '2359';
    }

    public function isWritable(string $user_id): bool
    {
        //Only the owner may edit the entry:
        return $user_id === $this->user_id;
    }

    public function getCreationDate(): DateTime
    {
        //The creation date is not supported.
        $date = new DateTime();
        $date->setTimestamp(0);
        return $date;
    }

    public function getModificationDate(): DateTime
    {
        //The modification date is not supported.
        $date = new DateTime();
        $date->setTimestamp(0);
        return $date;
    }

    public function getImportDate(): DateTime
    {
        //The import date is not supported.
        $date = new DateTime();
        $date->setTimestamp(0);
        return $date;
    }

    public function getAuthor(): ?User
    {
        return $this->user;
    }

    public function getEditor(): ?User
    {
        return $this->user;
    }

    public function toEventData(string $user_id): \Studip\Calendar\EventData
    {
        return new \Studip\Calendar\EventData(
            $this->getBegin(),
            $this->getEnd(),
            $this->title,
            ['schedule-entry'],
            '#000000',
            '#ffffff',
            $this->isWritable($user_id),
            self::class,
            $this->id,
            User::class,
            $this->user_id,
            User::class,
            $this->user_id,
            [
                'show' => URLHelper::getURL('dispatch.php/calendar/schedule/entry/' . $this->id)
            ],
            [],
            '',
            '#000000',
            $this->isAllDayEvent()
        );
    }
}
