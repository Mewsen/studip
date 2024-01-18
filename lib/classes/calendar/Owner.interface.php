<?php

namespace Studip\Calendar;

/**
 * The Studip\Calendar\Owner interface defines methods that classes whose instances own calendars
 * shall implement to faciliate permission checks for that calendars.
 */
interface Owner
{
    /**
     * Retrieves the Owner object for a specified owner-ID.
     *
     * @param string $owner_id The ID of the owner.
     *
     * @return Owner|null Either the Owner object if it can be found or null in case
     *     it cannot be found.
     */
    public static function getCalendarOwner(string $owner_id) : ?Owner;

    /**
     * Determines whether the specified user has read permissions to the calendar.
     *
     * @param string $user_id The user for which to determine read permissions.
     *
     * @return bool True, if the user has read permissions, false otherwise.
     */
    public function calendarReadable(string $user_id) : bool;

    /**
     * Determines whether the specified user has write permissions to the calendar.
     *
     * @param string $user_id The user for which to determine write permissions.
     *
     * @return bool True, if the user has write permissions, false otherwise.
     */
    public function calendarWritable(string $user_id) : bool;
}
