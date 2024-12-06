import { jsonapi } from "./jsonapi";

type RestrictedDate = {
    year: number,
    month: number,
    day: number,

    reason: string | null,
    lock: boolean
}

class RestrictedDatesHelper
{
    static #loadedYears : number[] = [];
    static #restrictedDates: RestrictedDate[] = [];

    static isDateRestricted(date: Date, returnBoolean: boolean = false): RestrictedDate | boolean {
        const restrictedDate : RestrictedDate | undefined = this.#restrictedDates.find(item => {
            return item.year === date.getFullYear()
                && item.month === date.getMonth() + 1
                && item.day === date.getDate();
        });

        if (returnBoolean) {
            return !!restrictedDate;
        }

        return restrictedDate ?? this.#convertDate(date, null, false);
    }

    static async loadRestrictedDatesByYear(year: number): Promise<void> {
        if (this.#loadedYears.includes(year)) {
            return Promise.reject();
        }

        this.#loadedYears.push(year);

        jsonapi.withPromises().request('holidays', {data: {
            'filter[year]': year
        }}).then((response: [] | object) => {
            // Since PHP will return an empty object as an array,
            // we need to check
            if (Array.isArray(response)) {
                return;
            }

            for (const [date, data] of Object.entries(response)) {
                this.#addRestrictedDate(
                    new Date(date),
                    data.holiday,
                    data.mandatory
                );
            }
        });
    }

    static #addRestrictedDate(date: Date, reason: string, lock: boolean = true): void {
        const restricted = this.#convertDate(date, reason, lock);

        this.#restrictedDates = this.#restrictedDates.filter(item => {
            return item.year !== restricted.year
                || item.month !== restricted.month
                || item.day !== restricted.day;
        });

        this.#restrictedDates.push(restricted);
    }

    static removeRestrictedDate(date: Date): void {
        this.#restrictedDates = this.#restrictedDates.filter(item => {
            return item.year !== date.getFullYear()
                || item.month !== date.getMonth() + 1
                || item.day !== date.getDate();
        });
    }

    static #convertDate(date: Date, reason: string | null, lock: boolean): RestrictedDate {
        return {
            year: date.getFullYear(),
            month: date.getMonth() + 1,
            day: date.getDate(),

            reason,
            lock
        };
    }
}

export default RestrictedDatesHelper;
