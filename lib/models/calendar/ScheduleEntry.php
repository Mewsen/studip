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
 *
 * @property string id database column
 * @property string start_time database column
 * @property string end_time database column
 * @property string dow database column
 * @property string label database column
 * @property string content database column
 * @property string user_id database column
 * @property string mkdate database column
 * @property string chdate database column
 */
class ScheduleEntry extends SimpleORMap implements Event
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'schedule_entries';
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
     */
    public function setFormattedStart(string $formatted_start) : void
    {
        $this->start_time = implode('', explode(':', $formatted_start));
    }

    /**
     * A helper method to set the content of the end attribute by a formatted date
     * in the format HH:mm.
     *
     * @param string $formatted_end The formatted date in the format HH:mm.
     */
    public function setFormattedEnd(string $formatted_end) : void
    {
        $this->end_time = implode('', explode(':', $formatted_end));
    }

    /**
     * Formats the start time for human-readable output.
     *
     * @return string The start time in the format HH:mm or an empty string in case
     *      the format stored in the start attribute is not supported.
     */
    public function getFormattedStart() : string
    {
        if (strlen($this->start_time) === 3) {
            return '0' . substr($this->start_time, 0, 1) . ':' . substr($this->start_time, 1, 2);
        }

        if (strlen($this->start_time) === 4) {
            return substr($this->start_time, 0, 2) . ':' . substr($this->start_time, 2, 2);
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
        if (strlen($this->end_time) === 3) {
            return '0' . substr($this->end_time, 0, 1) . ':' . substr($this->end_time, 1, 2);
        }

        if (strlen($this->end_time) === 4) {
            return substr($this->end_time, 0, 2) . ':' . substr($this->end_time, 2, 2);
        }

        //Invalid date format:
        return '';
    }

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function getObjectId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getPrimaryObjectID(): string
    {
        return $this->user_id;
    }

    /**
     * @inheritDoc
     */
    public function getObjectClass(): string
    {
        return self::class;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->label;
    }

    /**
     * @inheritDoc
     */
    public function getBegin(): DateTime
    {
        //Map the entry to the current week:
        $date = new DateTime();
        $date->setTimestamp(strtotime('midnight this week'));
        if ($this->dow > 1) {
            $days_to_add = $this->dow - 1;
            $date = $date->add(new DateInterval(sprintf('P%dD', $days_to_add)));
        }
        $time_parts = explode(':', $this->getFormattedStart());
        $date->setTime($time_parts[0], $time_parts[1]);
        return $date;
    }

    /**
     * @inheritDoc
     */
    public function getEnd(): DateTime
    {
        //Map the entry to the current week:
        $date = new DateTime();
        $date->setTimestamp(strtotime('midnight this week'));
        if ($this->dow > 1) {
            $days_to_add = $this->dow - 1;
            $date = $date->add(new DateInterval(sprintf('P%dD', $days_to_add)));
        }
        $time_parts = explode(':', $this->getFormattedEnd());
        $date->setTime($time_parts[0],$time_parts[1]);
        return $date;
    }

    /**
     * @inheritDoc
     */
    public function getDuration(): DateInterval
    {
        return $this->getEnd()->diff($this->getBegin());
    }

    /**
     * @inheritDoc
     */
    public function getLocation(): string
    {
        //No location supported.
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getUniqueId(): string
    {
        return implode('_', [
            Config::get()->STUDIP_INSTALLATION_ID,
            self::class,
            $this->id,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function getAdditionalDescriptions(): array
    {
        //No additional description supported.
        return [];
    }

    /**
     * @inheritDoc
     */
    public function isAllDayEvent(): bool
    {
        return $this->start_time === '000' && $this->end_time === '2359';
    }

    /**
     * @inheritDoc
     */
    public function isWritable(string $user_id): bool
    {
        //Only the owner and root may edit the entry:
        return $user_id === $this->user_id
            || $GLOBALS['perm']->have_perm('root', $user_id);
    }

    /**
     * @inheritDoc
     */
    public function getCreationDate(): DateTime
    {
        $date = new DateTime();
        $date->setTimestamp($this->mkdate);
        return $date;
    }

    /**
     * @inheritDoc
     */
    public function getModificationDate(): DateTime
    {
        $date = new DateTime();
        $date->setTimestamp($this->chdate);
        return $date;
    }

    /**
     * @inheritDoc
     */
    public function getImportDate(): DateTime
    {
        //The import date is not supported. Use mkdate instead.
        $date = new DateTime();
        $date->setTimestamp($this->mkdate);
        return $date;
    }

    /**
     * @inheritDoc
     */
    public function getAuthor(): ?User
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function getEditor(): ?User
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function toEventData(string $user_id): \Studip\Calendar\EventData
    {
        return new \Studip\Calendar\EventData(
            $this->getBegin(),
            $this->getEnd(),
            $this->label,
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

    /**
     * Creates a string representation of the schedule entry.
     *
     * @return string A human-readable string describing the schedule entry.
     */
    public function toString() : string
    {
        return studip_interpolate(
            _('Termin jeden %{dow} von %{start_time} bis %{end_time} Uhr'),
            [
                'dow'        => getWeekday($this->dow === 7 ? 0 : $this->dow, false),
                'start_time' => $this->getFormattedStart(),
                'end_time'   => $this->getFormattedEnd()
            ]
        );
    }
}
