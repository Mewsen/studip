import {jsonapi} from "./jsonapi";
import {datetime} from "./datetime";

/**
 * The Holiday class represents a holiday in Stud.IP
 */
class Holiday
{
    /**
     * The day of the holiday.
     */
    day: Date;

    /**
     * The name of the holiday.
     */
    name: string;

    /**
     * Whether the holiday is an official holiday (true) or not (false).
     * The latter means that it can be part of a vacation period.
     */
    official: boolean;

    constructor(day: Date, name: string, official: boolean = true) {
        this.day      = day;
        this.name     = name;
        this.official = official;
    }
}


/**
 * The HolidayCache class handles retrieving and caching holidays and vacations.
 */
class HolidayCache
{
    holiday_cache: Map<number, Array<Holiday>>;
    vacation_cache: Map<number, Array<Holiday>>;

    /**
     * The constructor has code to load cached holidays and vacations from the session storage.
     */
    constructor() {
        //Attempt to restore the cache from the session:
        const holiday_cache_str = sessionStorage.getItem('fullcalendar_holidays');
        this.holiday_cache = new Map<number, Array<Holiday>>();
        if (holiday_cache_str != null) {
            for (const [year, raw_array] of Object.entries(JSON.parse(holiday_cache_str))) {
                const holidays: Array<Holiday> = [];
                if (Array.isArray(raw_array)) {
                    for (const raw_holiday of raw_array) {
                        holidays.push(
                            new Holiday(new Date(raw_holiday.day), raw_holiday.name, raw_holiday.official)
                        );
                    }
                    this.holiday_cache.set(parseInt(year), holidays);
                }
            }
        }

        const vacation_cache_str = sessionStorage.getItem('fullcalendar_vacations');
        this.vacation_cache = new Map<number, Array<Holiday>>();
        if (vacation_cache_str != null) {
            for (const [year, raw_array] of Object.entries(JSON.parse(vacation_cache_str))) {
                const vacations: Array<Holiday> = [];
                if (Array.isArray(raw_array)) {
                    for (const raw_vacation of raw_array) {
                        vacations.push(
                            new Holiday(new Date(raw_vacation.day), raw_vacation.name, raw_vacation.official)
                        );
                    }
                    this.vacation_cache.set(parseInt(year), vacations);
                }
            }
        }
    }

    /**
     * Loads the holidays of a year and stores them in the cache.
     *
     * @param year The year for which to load holidays.
     */
    loadHolidays(year: number) : void {
        const existing_cache = this.holiday_cache.get(year);
        if (existing_cache) {
            return;
        }

        jsonapi.withPromises().GET('holidays', {
            data: { 'filter[year]': year }
        }).then(response => {
            if (!response) {
                return;
            }
            const events: Array<Holiday> = [];
            for (const [date, data] of Object.entries(response)) {
                const day = new Date(date);
                events.push(new Holiday(day, data.holiday, data.mandatory));
            }

            this.holiday_cache.set(year, events);
            //Update the session storage item:
            sessionStorage.setItem('fullcalendar_holidays', JSON.stringify(Object.fromEntries(this.holiday_cache)));
        });
    }

    /**
     * Loads the vacation days of a year and stores them in the cache.
     *
     * @param year The year for which to load vacations.
     */
    loadVacations(year: number): void {
        const existing_cache = this.vacation_cache.get(year);
        if (existing_cache) {
            return;
        }
        jsonapi.withPromises().get('vacations', {
            data: {'filter[year]': year}
        }).then(response => {
            if (!response) {
                return;
            }
            const events: Array<Holiday> = [];
            for (const vacation_data of Object.values(response)) {
                for (let i = parseInt(vacation_data.start); i < parseInt(vacation_data.end); i += 86400) {
                    const day = new Date(i * 1000);
                    events.push(new Holiday(day, vacation_data.name, false));
                }
            }

            this.vacation_cache.set(year, events);
            //Update the session storage item:
            sessionStorage.setItem('fullcalendar_vacations', JSON.stringify(Object.fromEntries(this.vacation_cache)));
        });
    }

    /**
     * Returns a vacation day from the cache.
     *
     * @param date The day of the vacation.
     *
     * @returns Holiday|null If a vacation exists on the specified day, a Holiday object
     *     for it is returned. Otherwise, null is returned.
     */
    getVacation(date: Date) : Holiday|null {
        const year_vacation_cache = this.vacation_cache.get(date.getFullYear());
        if (!year_vacation_cache) {
            return null;
        }
        for (const vacation of year_vacation_cache) {
            if (datetime.getISODate(date) === datetime.getISODate(vacation.day)) {
                //A vacation day has been found.
                return vacation;
            }
        }
        return null;
    }

    /**
     * Checks if there is a vacation day on the specified date.
     *
     * @param date The date to check.
     *
     * @returns boolean True, if the specified day is a vacation day.
     *     Otherwise, false is returned.
     */
    isVacation(date: Date) {
        return this.getVacation(date) !== null;
    }

    /**
     * Returns the name of the vacation day on the specified date.
     *
     * @param date The date for which to get the vacation name.
     *
     * @returns string The name of the vacation, if any.
     *     If there is no vacation day on the specified date, the string is empty.
     */
    getVacationName(date: Date) {
        const vacation = this.getVacation(date);
        if (vacation) {
            return vacation.name;
        }
        return '';
    }

    /**
     * Returns a holiday / vacation day from the cache.
     *
     * @param date The date of the holiday.
     *
     * @param regard_vacations Whether to regard vacations in addition to holidays (true)
     *     or to just regard holidays (false). Defaults to false.
     *
     * @returns Holiday|null If a holiday can be found, it is returned. Otherwise,
     *     null is returned.
     */
    getHoliday(date: Date, regard_vacations: boolean = false): Holiday|null {
        //Check if an entry for the date exists in the holiday or the vacation list:
        const year_holiday_cache = this.holiday_cache.get(date.getFullYear());
        if (!year_holiday_cache) {
            return null;
        }
        for (const holiday of year_holiday_cache) {
            if (datetime.getISODate(date) === datetime.getISODate(holiday.day)) {
                //A holiday has been found.
                return holiday;
            }
        }
        if (regard_vacations) {
            return this.getVacation(date);
        }
        return null;
    }

    /**
     * Checks if there is a holiday on the specified date.
     *
     * @param date The date to check.
     *
     * @param regard_vacations Whether to regard vacations in addition to holidays (true)
     *     or to just regard holidays (false). Defaults to false.
     *
     * @returns boolean True, if the specified day is a holiday / vacation day.
     *     Otherwise, false is returned.
     */
    isHoliday(date: Date, regard_vacations: boolean = false) : boolean {
        return this.getHoliday(date, regard_vacations) !== null;
    }

    /**
     * Returns the name of the holiday on the specified date.
     *
     * @param date The date for which to get the holiday name.
     *
     * @param regard_vacations Whether to regard vacations in addition to holidays (true)
     *     or to just regard holidays (false). Defaults to false.
     *
     * @returns string The name of the holiday / vacation, if any.
     *     If there is no holiday on the specified date, the string is empty.
     */
    getHolidayName(date: Date, regard_vacations: boolean = false) : string {
        const holiday = this.getHoliday(date, regard_vacations);
        if (holiday) {
            return holiday.name;
        }
        return '';
    }
}


export {Holiday, HolidayCache};
export const holiday_cache: HolidayCache = new HolidayCache();
