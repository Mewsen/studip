<template>
    <article id="globalsearch-hints" aria-live="polite">
        <section>
            <header>{{ $gettext('Hinweis:') }}</header>
            <p>
                 {{ $gettext('Die Schnellsuche findet nur Ergebnisse im aktuellen Semester.') }}
            </p>
        </section>
        <section>
            <header>
                {{ $gettext('[STRG] + [Leertaste]') }}
            </header>
            <p>
                {{ $gettext('Tastenkombination zum Öffnen und Schließen') }}
            </p>
        </section>
        <section>
            <header>
                {{ $gettext('Platzhalter') }}
            </header>
            <p>
                {{ $gettext('"_" ist Platzhalter für ein beliebiges Zeichen.') }}
                <br>
                {{ $gettext('"%" ist Platzhalter für beliebig viele Zeichen.') }}
                <br>
                {{ $gettext('"Me_er" findet Treffer für "Meyer" und "Meier". "M__er" findet ' +
                    'zusätzlich auch "Mayer" und "Maier". "M%er" findet alle vorherigen ' +
                    'Treffer, aber auch "Müller".') }}
            </p>
        </section>
        <section>
            <header>
                {{ $gettext('Klick auf Überschrift') }}
            </header>
            <p>
                {{ $gettext('Erweitert die ausgewählte Suchkategorie, um mehr Suchergebnisse aus dieser Kategorie anzuzeigen. ' +
                    ' Ein weiterer Klick zeigt wieder Ergebnisse aus allen Kategorien an.') }}
            </p>
        </section>
        <section>
            <header>
                {{ $gettext('Dateisuche') }}
            </header>
            <p>
                {{ $gettext('Die Dateisuche kann über einen Schrägstrich (/) verfeinert werden. ' +
                    'Beispiel: "Meine Veranstaltung/Datei" zeigt alle Dateien, die das Wort ' +
                    '"Datei" enthalten und in "Meine Veranstaltung" sind, an. Die ' +
                    'Veranstaltung kann auch auf einen Teil (z.B. "Veran/Datei") oder auf ' +
                    'die Großbuchstaben bzw. auch deren Abkürzung (z.B. "MV/Datei" oder ' +
                    '"V/Datei") beschränkt werden.') }}
            </p>
        </section>
    </article>
</template>
<script>
export default {
    name: 'global-search-bar-hints'
}
</script>

