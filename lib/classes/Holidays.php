<?php

/**
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @since Stud.IP 5.5
 */
final class Holidays
{
    public const HOLIDAY_ALL_SAINTS_DAY = 0;
    public const HOLIDAY_ASCENSION_DAY = 1;
    public const HOLIDAY_ASH_WEDNESDAY = 2;
    public const HOLIDAY_CARNIVAL = 3;
    public const HOLIDAY_CHRISTMAS_DAY = 4;
    public const HOLIDAY_CHRISTMAS_DAY_2 = 5;
    public const HOLIDAY_CHRISTMAS_EVE = 6;
    public const HOLIDAY_CORPUS_CHRISTI = 7;
    public const HOLIDAY_EASTER_MONDAY = 8;
    public const HOLIDAY_EASTER_SUNDAY = 9;
    public const HOLIDAY_EPIPHANY = 10;
    public const HOLIDAY_FIRST_SUNDAY_OF_ADVENT = 11;
    public const HOLIDAY_FOURTH_SUNDAY_OF_ADVENT = 12;
    public const HOLIDAY_GERMAN_UNITY_DAY = 13;
    public const HOLIDAY_GOOD_FRIDAY = 14;
    public const HOLIDAY_MARTINMAS = 15;
    public const HOLIDAY_MAY_DAY = 16;
    public const HOLIDAY_MOTHERS_DAY = 17;
    public const HOLIDAY_NEW_YEAR = 18;
    public const HOLIDAY_NEW_YEARS_EVE = 19;
    public const HOLIDAY_REFORMATION_DAY = 20;
    public const HOLIDAY_REMEMBRANCE_DAY = 21;
    public const HOLIDAY_SECOND_SUNDAY_OF_ADVENT = 22;
    public const HOLIDAY_SHROVE_MONDAY = 23;
    public const HOLIDAY_START_OF_AUTUMN = 24;
    public const HOLIDAY_START_OF_SPRING = 25;
    public const HOLIDAY_START_OF_SUMMER = 26;
    public const HOLIDAY_START_OF_WINTER = 27;
    public const HOLIDAY_ST_NICHOLAS_DAY = 28;
    public const HOLIDAY_SUNDAY_OF_THE_DEAD = 29;
    public const HOLIDAY_THANKSGIVING = 30;
    public const HOLIDAY_THIRD_SUNDAY_OF_ADVENT = 31;
    public const HOLIDAY_WHIT_MONDAY = 32;
    public const HOLIDAY_WHIT_SUNDAY = 33;
    public const HOLIDAY_INTERNATIONAL_WOMENS_DAY = 34;
    public const HOLIDAY_PEACE_FESTIVAL = 35;
    public const HOLIDAY_ASSUMPTION_DAY = 36;
    public const HOLIDAY_WORLD_CHILDRENS_DAY = 37;
    public const HOLIDAY_DAY_OF_PRAYER_AND_REPENTANCE = 38;

    public const WEIGHT_HOLIDAY = 1;
    public const WEIGHT_OTHER_HOLIDAY = 2;
    public const WEIGHT_PUBLIC_HOLIDAY = 3;

    private const PUBLIC_HOLIDAYS = [
        self::HOLIDAY_ASCENSION_DAY,
        self::HOLIDAY_CHRISTMAS_DAY,
        self::HOLIDAY_CHRISTMAS_DAY_2,
        self::HOLIDAY_EASTER_MONDAY,
        self::HOLIDAY_EASTER_SUNDAY,
        self::HOLIDAY_GERMAN_UNITY_DAY,
        self::HOLIDAY_GOOD_FRIDAY,
        self::HOLIDAY_MAY_DAY,
        self::HOLIDAY_NEW_YEAR,
        self::HOLIDAY_WHIT_MONDAY,
        self::HOLIDAY_WHIT_SUNDAY,
    ];

    private const OTHER_HOLIDAYS = [
        self::HOLIDAY_FIRST_SUNDAY_OF_ADVENT,
        self::HOLIDAY_FOURTH_SUNDAY_OF_ADVENT,
        self::HOLIDAY_REMEMBRANCE_DAY,
        self::HOLIDAY_SECOND_SUNDAY_OF_ADVENT,
        self::HOLIDAY_THIRD_SUNDAY_OF_ADVENT,
    ];

    public static function getHolidays(bool $sorted = true, bool $ignore_customized_holidays = false): array
    {
        $reflection = new ReflectionClass(self::class);
        $holiday_constants = array_filter(
            $reflection->getConstants(),
            function (string $constant): bool {
                return str_starts_with($constant, 'HOLIDAY_');
            },
            ARRAY_FILTER_USE_KEY
        );

        $holidays = array_map(
            function (int $id): array {
                return [
                    'name' => self::translateId($id),
                    'col' => self::getHolidaySignificance($id, time(), true),
                ];
            },
            array_values($holiday_constants)
        );

        if ($sorted) {
            uasort(
                $holidays,
                function ($a, $b) {
                    return strnatcasecmp($a['name'], $b['name']);
                }
            );
        }

        return $holidays;
    }

    /**
     * @param int $timestamp
     *
     * @return array{name: string, col: int}|false
     */
    public static function isHoliday(int $timestamp)
    {
        $holiday_id = self::getHolidayId($timestamp);
        if (!$holiday_id) {
            return false;
        }

        return [
            'name' => self::translateId($holiday_id),
            'col'  => self::getHolidaySignificance($holiday_id, $timestamp),
        ];
    }

    private static function getHolidayId(int $timestamp): ?int
    {
        // erstmal brauchen wir den Ostersonntag fuer die meisten kirchlichen Feiertage
        //  $easterday = easter_date(date("Y", $timestamp)); // geht leider nicht
        // Berechnung nach Carters Algorithmus (gueltig von 1900 - 2099)
        $timestamp = mktime(
            0, 0, 0,
            date('n', $timestamp),
            date('j', $timestamp),
            date('Y', $timestamp)
        );
        $year = date('Y', $timestamp);
        $b = 225 - 11 * ($year % 19);
        $d = (($b - 21) % 30) + 21;
        if ($d > 48) {
            $d--;
        }
        $e = ($year + abs($year / 4) + $d + 1) % 7;
        $q = $d + 7 - $e;
        if ($q < 32) {
            $easterday = date('z', mktime(0, 0, 0, 3, $q, $year)) + 1;
        } else {
            $easterday = date('z', mktime(0, 0, 0, 4, $q - 31, $year)) + 1;
        }

        $id = null;
        $col = 1;
        // Differenz in Tagen zu Ostertag berechnen
        $doy = date("z", $timestamp) + 1;
        $dif = $doy - $easterday;
        switch ($dif) {
            case -48:
                return self::HOLIDAY_SHROVE_MONDAY;
            case -47:
                return self::HOLIDAY_CARNIVAL;
            case -46:
                return self::HOLIDAY_ASH_WEDNESDAY;
            case  -2:
                return self::HOLIDAY_GOOD_FRIDAY;
            case   0:
                return self::HOLIDAY_EASTER_SUNDAY;
            case   1:
                return self::HOLIDAY_EASTER_MONDAY;
            case  39:
                return self::HOLIDAY_ASCENSION_DAY;
            case  49:
                return self::HOLIDAY_WHIT_SUNDAY;
            case  50:
                return self::HOLIDAY_WHIT_MONDAY;
            case  60:
                return self::HOLIDAY_CORPUS_CHRISTI;
        }

        // die unveraenderlichen Feiertage
        switch ($doy) {
            case 1:
                return self::HOLIDAY_NEW_YEAR;
            case 6:
                return self::HOLIDAY_EPIPHANY;
        }

        // Schaltjahre nicht vergessen
        if (date('L', $timestamp)) {
            $doy -= 1;
        }
        switch ($doy) {
            case 67:
                return self::HOLIDAY_INTERNATIONAL_WOMENS_DAY;
            case 79:
                return self::HOLIDAY_START_OF_SPRING;
            case 121:
                return self::HOLIDAY_MAY_DAY;
            case 172:
                return self::HOLIDAY_START_OF_SUMMER;
            case 220:
                return self::HOLIDAY_PEACE_FESTIVAL;
            case 227:
                return self::HOLIDAY_ASSUMPTION_DAY;
            case 263:
                return self::HOLIDAY_WORLD_CHILDRENS_DAY;
            case 266:
                return self::HOLIDAY_START_OF_AUTUMN;
            case 276:
                return self::HOLIDAY_GERMAN_UNITY_DAY;
            case 304:
                return self::HOLIDAY_REFORMATION_DAY;
            case 305:
                return self::HOLIDAY_ALL_SAINTS_DAY;
            case 315:
                return self::HOLIDAY_MARTINMAS;
            case 340:
                return self::HOLIDAY_ST_NICHOLAS_DAY;
            case 355:
                return self::HOLIDAY_START_OF_WINTER;
            case 358:
                return self::HOLIDAY_CHRISTMAS_EVE;
            case 359:
                return self::HOLIDAY_CHRISTMAS_DAY;
            case 360:
                return self::HOLIDAY_CHRISTMAS_DAY_2;
            case 365:
                return self::HOLIDAY_NEW_YEARS_EVE;
        }

        // Buß- und Bettag am Mittwoch vor dem 23.11.
        if (date('w', $timestamp) == 3 && $doy > 319 && $doy < 327) {
            return self::HOLIDAY_DAY_OF_PRAYER_AND_REPENTANCE;
        }

        // Die Sonntagsfeiertage
        if (date('w', $timestamp) == 0) {
            if ($doy > 127 && $doy < 135) {
                return self::HOLIDAY_MOTHERS_DAY;
            }
            if ($doy > 266 && $doy < 274) {
                return self::HOLIDAY_THANKSGIVING;
            }
            if ($doy > 316 && $doy < 324) {
                return self::HOLIDAY_REMEMBRANCE_DAY;
            }
            if ($doy > 323 && $doy < 331) {
                return self::HOLIDAY_SUNDAY_OF_THE_DEAD;
            }
            if ($doy > 330 && $doy < 338) {
                return self::HOLIDAY_FIRST_SUNDAY_OF_ADVENT;
            }
            if ($doy > 337 && $doy < 345) {
                return self::HOLIDAY_SECOND_SUNDAY_OF_ADVENT;
            }
            if ($doy > 344 && $doy < 352) {
                return self::HOLIDAY_THIRD_SUNDAY_OF_ADVENT;
            }
            if ($doy > 351 && $doy < 359) {
                return self::HOLIDAY_FOURTH_SUNDAY_OF_ADVENT;
            }
        }

        return null;
    }

    private static function getHolidaySignificance(
        int $holiday_id,
        int $timestamp,
        bool $ignore_customized_holidays = false
    ): int
    {
        if (!$ignore_customized_holidays) {
            $customized_holidays = Config::get()->CUSTOMIZED_HOLIDAYS;
            if (in_array($holiday_id, $customized_holidays)) {
                return self::WEIGHT_PUBLIC_HOLIDAY;
            }
        }

        if ($holiday_id === self::HOLIDAY_REFORMATION_DAY) {
            return date('Y', $timestamp) == 2017 ? self::WEIGHT_PUBLIC_HOLIDAY : self::WEIGHT_OTHER_HOLIDAY;
        }

        if (in_array($holiday_id, self::PUBLIC_HOLIDAYS)) {
            return self::WEIGHT_PUBLIC_HOLIDAY;
        }

        if (in_array($holiday_id, self::OTHER_HOLIDAYS)) {
            return self::WEIGHT_OTHER_HOLIDAY;
        }

        return self::WEIGHT_HOLIDAY;
    }

    private static function translateId(int $id): string
    {
        switch ($id) {
            case self::HOLIDAY_ASH_WEDNESDAY:
                return _('Aschermittwoch');
            case self::HOLIDAY_CARNIVAL:
                return _('Fastnacht');
            case self::HOLIDAY_SHROVE_MONDAY:
                return _('Rosenmontag');
            case self::HOLIDAY_GOOD_FRIDAY:
                return _('Karfreitag');
            case self::HOLIDAY_EASTER_SUNDAY:
                return _('Ostersonntag');
            case self::HOLIDAY_EASTER_MONDAY:
                return _('Ostermontag');
            case self::HOLIDAY_ASCENSION_DAY:
                return _('Christi Himmelfahrt');
            case self::HOLIDAY_WHIT_SUNDAY:
                return _('Pfingstsonntag');
            case self::HOLIDAY_WHIT_MONDAY:
                return _('Pfingstmontag');
            case self::HOLIDAY_CORPUS_CHRISTI:
                return _('Fronleichnam');
            case self::HOLIDAY_NEW_YEAR:
                return _('Neujahr');
            case self::HOLIDAY_EPIPHANY:
                return _('Hl. Drei Könige');
            case self::HOLIDAY_START_OF_SPRING:
                return _('Frühlingsanfang');
            case self::HOLIDAY_MAY_DAY:
                return _('Maifeiertag');
            case self::HOLIDAY_START_OF_SUMMER:
                return _('Sommeranfang');
            case self::HOLIDAY_START_OF_AUTUMN:
                return _('Herbstanfang');
            case self::HOLIDAY_GERMAN_UNITY_DAY:
                return _('Tag der deutschen Einheit');
            case self::HOLIDAY_REFORMATION_DAY:
                return _('Reformationstag');
            case self::HOLIDAY_ALL_SAINTS_DAY:
                return _('Allerheiligen');
            case self::HOLIDAY_MARTINMAS:
                return _('Martinstag');
            case self::HOLIDAY_ST_NICHOLAS_DAY:
                return _('Nikolaus');
            case self::HOLIDAY_START_OF_WINTER:
                return _('Winteranfang');
            case self::HOLIDAY_CHRISTMAS_EVE:
                return _('Hl. Abend');
            case self::HOLIDAY_CHRISTMAS_DAY:
                return _('1. Weihnachtstag');
            case self::HOLIDAY_CHRISTMAS_DAY_2:
                return _('2. Weihnachtstag');
            case self::HOLIDAY_NEW_YEARS_EVE:
                return _('Silvester');
            case self::HOLIDAY_MOTHERS_DAY:
                return _('Muttertag');
            case self::HOLIDAY_THANKSGIVING:
                 return _('Erntedank');
            case self::HOLIDAY_REMEMBRANCE_DAY:
                return _('Volkstrauertag');
            case self::HOLIDAY_SUNDAY_OF_THE_DEAD:
                return _('Totensonntag');
            case self::HOLIDAY_FIRST_SUNDAY_OF_ADVENT:
                return _('1. Advent');
            case self::HOLIDAY_SECOND_SUNDAY_OF_ADVENT:
                return _('2. Advent');
            case self::HOLIDAY_THIRD_SUNDAY_OF_ADVENT:
                return _('3. Advent');
            case self::HOLIDAY_FOURTH_SUNDAY_OF_ADVENT:
                return _('4. Advent');
            case self::HOLIDAY_INTERNATIONAL_WOMENS_DAY:
                return _('Internationaler Frauentag');
            case self::HOLIDAY_PEACE_FESTIVAL:
                return _('Friedensfest');
            case self::HOLIDAY_ASSUMPTION_DAY:
                return _('Mariä Himmelfahrt');
            case self::HOLIDAY_WORLD_CHILDRENS_DAY:
                return _('Weltkindertag');
            case self::HOLIDAY_DAY_OF_PRAYER_AND_REPENTANCE:
                return _('Buß- und Bettag');
            default:
                throw new InvalidArgumentException("Invalid holiday id {$id}");
        }
    }
}
