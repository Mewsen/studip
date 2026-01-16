import {$gettext, $gettextInterpolate} from "./gettext";


class DateTime
{
    /**
     * A helper method for padding parts of dates with leading zeros.
     * @param item The part of a date string to pad.
     * @param target_length The length of the string to output.
     * @returns {string} A padded version of $what.
     */
    pad(item: number, target_length: number = 2) : string {
        const target: string = `00000000${item}`;
        return target.substring(target.length - target_length);
    }

    /**
     * Returns an ISO representation of the specified Date object.
     * in the format YYYY-MM-DD.
     *
     * @param date The Date object to format as ISO date.
     * @returns {string} The ISO date string of the Date object.
     */
    getISODate(date: Date) : string {
        return date.getFullYear() + '-' + this.pad(date.getMonth() + 1) + '-' + this.pad(date.getDate());
    }

    /**
     * Returns a formatted version of the specified Date object
     * in the Stud.IP date formatting.
     *
     * @param date The Date object to be formatted.
     * @param relative_value Whether to return a relative time value (true)
     *     or an absolute one (false). Defaults to false.
     * @param date_only Whether to return the date only (true) or date and time (false).
     *     Defaults to false. Only regarded when $relative_value is false.
     * @param html Whether to format the date as HTML (true) or as plain text (false). Defaults to false.
     *
     * @returns {*|string} The date, formatted according to the Stud.IP format for dates.
     */
    getStudipDate(
        date: Date,
        relative_value: boolean = false,
        date_only: boolean = false,
        html: boolean = false) : string {
        if (relative_value) {
            const now: number     = Date.now();
            const date_ts: number = date.getMilliseconds();
            if (now - date_ts < 60 * 1000) {
                return $gettext('Jetzt');
            }
            if (now - date_ts < 2 * 60 * 60 * 1000) {
                return $gettextInterpolate(
                    'Vor %{ minutes } Minuten',
                    {minutes: Math.floor((now - date_ts) / (1000 * 60))}
                );
            }
            return this.pad(date.getHours()) + ':' + this.pad(date.getMinutes());
        }

        if (date_only) {
            if (html) {
                return '<span class="day">'
                    + this.pad(date.getDate())
                    + '.</span><span class="month">'
                    + this.pad(date.getMonth() + 1)
                    + '.</span><span class="year">'
                    + date.getFullYear()
                    + '</span>';
            } else {
                return this.pad(date.getDate()) + '.' + this.pad(date.getMonth() + 1) + '.' + date.getFullYear();
            }
        }

        if (html) {
            return '<span class="day">'
                + this.pad(date.getDate())
                + '.</span><span class="month">'
                + this.pad(date.getMonth() + 1)
                + '.</span><span class="year">'
                + date.getFullYear()
                + '</span> <span class="time">'
                + this.pad(date.getHours()) + ':' + this.pad(date.getMinutes())
                + '</span>';
        } else {
            return this.pad(date.getDate()) + '.' + this.pad(date.getMonth() + 1) + '.' + date.getFullYear() + ' ' + this.pad(date.getHours()) + ':' + this.pad(date.getMinutes());
        }
    }

    getDayOfWeekName(dow: number, short: boolean = false): string {
        if (dow === 0 || dow === 7) {
            if (short) {
                return $gettext('So.');
            } else {
                return $gettext('Sonntag');
            }
        } else if (dow === 1) {
            if (short) {
                return $gettext('Mo.');
            } else {
                return $gettext('Montag');
            }
        } else if (dow === 2) {
            if (short) {
                return $gettext('Di.');
            } else {
                return $gettext('Dienstag');
            }
        } else if (dow === 3) {
            if (short) {
                return $gettext('Mi.');
            } else {
                return $gettext('Mittwoch');
            }
        } else if (dow === 4) {
            if (short) {
                return $gettext('Do.');
            } else {
                return $gettext('Donnerstag');
            }
        } else if (dow === 5) {
            if (short) {
                return $gettext('Fr.');
            } else {
                return $gettext('Freitag');
            }
        } else if (dow === 6) {
            if (short) {
                return $gettext('Sa.');
            } else {
                return $gettext('Samstag');
            }
        } else {
            return '';
        }
    }
}


export default DateTime;
export const datetime: DateTime = new DateTime();
