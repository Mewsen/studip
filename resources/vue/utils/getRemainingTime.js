/**
 * Berechnet die verbleibende Zeit eines Prozesses als formatierten String
 * @param {Object} process - Der Prozess mit begin und end Timestamp (in Sekunden)
 * @param {number} currentTime - Die aktuelle Zeit in Millisekunden (Date.now())
 * @param {Function} gettext - Die Gettext-Funktion für Übersetzungen
 * @returns {string} - Formatierte verbleibende Zeit
 */
export function getRemainingTime(remainingSeconds, ngettext) {
    if (remainingSeconds < 61) {
        return ngettext(
            '%{seconds} Sekunde',
            '%{seconds} Sekunden',
            remainingSeconds,
            { minutes: remainingSeconds }
        );
    }
    if (remainingSeconds < 3601) {
        //return gettext('%{minutes} Minuten', {minutes: Math.round(remainingSeconds / 60)});
        return ngettext(
            '%{minutes} Sekunde',
            '%{minutes} Sekunden',
            Math.round(remainingSeconds / 60),
            { minutes: Math.round(remainingSeconds / 60) }
        );
    }
    if (remainingSeconds < 86401) {
        return ngettext(
            '%{hours} Stunde',
            '%{hours} Stunden',
            Math.round(remainingSeconds / 3600),
            { hours: Math.round(remainingSeconds / 3600) }
        );
    }
    if (remainingSeconds < 604801) {
        return ngettext(
            '%{days} Tag',
            '%{days} Tage',
            Math.round(remainingSeconds / 86400),
            { days: Math.round(remainingSeconds / 86400) }
        );
    }
    if (remainingSeconds < 31536001) {
        return ngettext(
            '%{weeks} Woche',
            '%{weeks} Wochen',
            Math.round(remainingSeconds / 604800),
            { weeks: Math.round(remainingSeconds / 604800) }
        );
    }
    if (remainingSeconds < 315360001) {
        return ngettext(
            '%{months} Monat',
            '%{months} Monate',
            Math.round(remainingSeconds / 2628000),
            { months: Math.round(remainingSeconds / 2628000) }
        );
    }

    return ngettext(
        '%{years} Jahr',
        '%{years} Jahre',
        Math.round(remainingSeconds / 31536000),
        { years: Math.round(remainingSeconds / 31536000) }
    );
}
