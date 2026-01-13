import SuiFormattedTime from './SuiFormattedTime.vue'

const meta = {
    title: 'Stud.IP UI Elements/Formatted/Time',
    component: SuiFormattedTime,
    tags: ['autodocs', 'since:6.2.0', 'new', 'beta'],
    parameters: {
        docs: {
            description: {
                component:
                    'Die **`SuiFormattedTime`** Komponente dient zur **einheitlichen Zeitdarstellung** gemäß den Stud.IP-Konventionen. ' +
                    'Sie verwendet das HTML `<time>`-Tag für semantische Korrektheit (mit ISO-8601 im `datetime`-Attribut).\n\n' +
                    '* **Semantik:** Die Komponente rendert die Zeitangabe immer im **HTML `<time>`-Tag**.\n' +
                    '* **ISO-Format:** Das `datetime`-Attribut des `<time>`-Tags enthält den **ISO-8601 String** (`YYYY-MM-DDTHH:MM:SSZ`) für maschinelle Lesbarkeit.\n' +
                    '* **Tooltip:** Ist die Hauptanzeige relativ (innerhalb der ersten 12 Stunden), enthält das **`title`-Attribut** (Tooltip) stets die **volle absolute Zeit** (Datum und Uhrzeit), um die genaue Zeit bei Hover anzuzeigen.\n' +
                    '* **Fehlerwert:** Bei ungültigem oder fehlendem Wert (`timestamp: 0`) gibt die Komponente den Wert "`—`" (Gedankenstrich) zurück.\n\n' +
                    'Die Komponente passt die Anzeige basierend auf der Zeitdifferenz zur aktuellen Zeit an. Liegt die Zeit **unter 1 Minute** zurück, wird "`Jetzt`" angezeigt. Für Zeiträume zwischen **1 Minute und 2 Stunden** wird die relative Zeit in Minuten angezeigt (z. B. "`Vor 1 Minute`" oder "`Vor 15 Minuten`"). Bei einer Zeitdifferenz von **2 bis 12 Stunden** wird die Anzeige auf **nur die Uhrzeit** (HH:MM) reduziert. Liegt die Zeit **über 12 Stunden** zurück, fällt die Anzeige auf die **absolute Darstellung** (Datum und Uhrzeit) zurück.\n\n' +
                    'Die `dateOnly` Prop wird nur relevant, wenn die Komponente auf eine **absolute Anzeige** fällt (`relative: false` ODER Zeit > 12 Stunden). ' +
                    'In diesem Fall zeigt sie nur das Datum und nicht die Uhrzeit an.\n\n'
            },
        },
    },
    argTypes: {
        timestamp: {
            description:
                'Unix-Timestamp in Sekunden (z. B. von PHP `time()`). Alternativ kann auch die ISO-8601-Zeichenkette über die `iso`-Prop übergeben werden.',
            control: 'number',
        },
        iso: {
            description:
                'ISO-8601-Zeichenkette (z. B. von JavaScript `new Date().toISOString()`). Alternativ kann auch der Unix-Timestamp über die `timestamp`-Prop übergeben werden.',
            control: 'text',
        },
        relative: {
            description:
                'Ob eine relative Zeitangabe (true) oder eine absolute (false) angezeigt werden soll.',
            control: 'boolean',
        },
        dateOnly: {
            description:
                'Ob nur das Datum (true) oder Datum und Uhrzeit (false) angezeigt werden soll. Wird nur berücksichtigt, wenn `relative` auf false gesetzt ist.',
            control: 'boolean',
        },
    },
}

export default meta

const SECONDS = 1
const MINUTE = 60 * SECONDS
const HOUR = 60 * MINUTE
const DAY = 24 * HOUR

const nowInSeconds = Math.floor(Date.now() / 1000)
const secondsAgo = (seconds) => nowInSeconds - seconds
const twoMinutesAgo = secondsAgo(2 * MINUTE)

const ALL_TEST_CASES = [
    { name: 'Jetzt (< 1 Min)', seconds: 30 },
    { name: 'Singular (1 Min)', seconds: 1 * MINUTE + 5 },
    { name: 'Plural (15 Min)', seconds: 15 * MINUTE },
    { name: 'Über 1 Stunde', seconds: 90 * MINUTE },
    { name: 'Über 2 Stunden', seconds: 2.5 * HOUR },
    { name: 'Über 12 Stunden', seconds: 13 * HOUR },
    { name: 'Mehrere Tage', seconds: 3 * DAY },
    { name: 'Ungültiger Wert', seconds: 0, timestamp: 0 },
]

export const Default = {
    args: {
        timestamp: twoMinutesAgo,
        relative: false,
        dateOnly: false,
    },
}

export const Relative = {
    args: {
        timestamp: twoMinutesAgo,
        relative: true,
        dateOnly: false,
    },
}

export const dateOnly = {
    args: {
        timestamp: twoMinutesAgo,
        relative: false,
        dateOnly: true,
    },
}

export const ISO = {
    args: {
        iso: new Date().toISOString(),
        relative: false,
        dateOnly: false,
    },
}
const ComplexRenderTemplate = (args) => ({
    components: { SuiFormattedTime },
    setup() {
        const cases = ALL_TEST_CASES.map((c) => ({
            ...c,
            timestamp: c.timestamp === 0 ? 0 : secondsAgo(c.seconds),
            relative: args.relative === false ? false : c.relative,
            dateOnly: args.dateOnly || c.dateOnly,
        }))

        return { cases, args }
    },
    template: `
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #ccc;">
                    <th style="padding: 10px; text-align: left; width: 220px;">Testfall</th>
                    <th style="padding: 10px; text-align: left;">dateOnly: false</th>
                    <th style="padding: 10px; text-align: left;">dateOnly: true</th>
                </tr>
            </thead>
            <tbody>
                <template v-for="(c, index) in cases" :key="index">
                    <tr>
                        <td style="padding: 10px; font-weight: bold;">{{ c.name }}:</td>
                        
                        <td style="padding: 10px;">
                            <SuiFormattedTime 
                                v-bind="{ 
                                    $gettext: args.$gettext,
                                    timestamp: c.timestamp, 
                                    relative: args.relative, 
                                    dateOnly: false 
                                }" 
                            />
                        </td>
                        
                        <td style="padding: 10px;">
                            <SuiFormattedTime 
                                v-bind="{ 
                                    $gettext: args.$gettext,
                                    timestamp: c.timestamp, 
                                    relative: args.relative, 
                                    dateOnly: true 
                                }" 
                            />
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    `,
})

export const AbsoluteDisplay = {
    args: {
        relative: false,
    },
    render: ComplexRenderTemplate,
    name: 'Absolute Anzeige (Referenz)',
}

export const RelativeDisplayThresholds = {
    args: {
        relative: true,
    },
    render: ComplexRenderTemplate,
    name: 'Relative Anzeige (Schwellenwerte-Test)',
}
