<template>
    <article id="globalsearch-hints">
        <header>
            <h2>{{ $gettext('Tipps zur Globalen Suche') }}</h2>
        </header>
        <section>
            <header>
                <h3>{{ $gettext('Hinweis zur Suche') }}</h3>
            </header>
            <p>
                 {{ $gettext('Die Schnellsuche findet nur Ergebnisse im aktuellen Semester.') }}
            </p>
        </section>
        <section>
            <header>
                <h3>{{ $gettext('[STRG] + [Leertaste]') }}</h3>
            </header>
            <p>
                {{ $gettext('Tastenkombination zum Öffnen und Schließen') }}
            </p>
        </section>
        <section>
            <header>
                <h3>{{ $gettext('Platzhalter') }}</h3>
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
                <h3>{{ $gettext('Klick auf Überschrift') }}</h3>
            </header>
            <p>
                {{ $gettext('Erweitert die ausgewählte Suchkategorie, um mehr Suchergebnisse aus dieser Kategorie anzuzeigen. ' +
                    ' Ein weiterer Klick zeigt wieder Ergebnisse aus allen Kategorien an.') }}
            </p>
        </section>
        <section>
            <header>
                <h3>{{ $gettext('Dateisuche') }}</h3>
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

