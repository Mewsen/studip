import { $gettext } from '../../../../../assets/javascripts/lib/gettext';

export enum AssessmentType {
    Form = 'form',
    Freetext = 'freetext',
    Table = 'table',
}

export interface EditorFormCriterium {
    text: string;
    description: string;
}

export interface EditorTableCriterium {
    text: string;
}

export type FormAssessmentPayload = { criteria: EditorFormCriterium[] };
export type TableAssessmentPayload = { criteria: EditorTableCriterium[] };
export type FreetextAssessmentPayload = {};

export type ProcessConfigurationPayload = FormAssessmentPayload | FreetextAssessmentPayload | TableAssessmentPayload;

export interface ProcessConfiguration {
    anonymous: boolean;
    duration: number;
    automaticPairing: boolean;
    type: AssessmentType;
    payload?: ProcessConfigurationPayload;
}

export interface ConfigurationSet {
    name: string;
    configuration: ProcessConfiguration;
}

export const ASSESSMENT_TYPES = {
    [AssessmentType.Form]: {
        short: $gettext('Formular'),
        long: $gettext('Strukturiertes Bewertungssystem mit detailierten Fragen zur Begutachtung'),
        defaultPayload: { criteria: defaultCriteriaForm() },
    },
    [AssessmentType.Freetext]: {
        short: $gettext('Freitext'),
        long: $gettext('Freitextliche Begutachtung'),
        defaultPayload: { },
    },
    [AssessmentType.Table]: {
        short: $gettext('Tabelle'),
        long: $gettext('Einfaches Bewertungssystem mit 3 Bewertungsnoten und kurzer Erläuterung'),
        defaultPayload: { criteria: defaultCriteriaTable() },
    },
};

export const CONFIGURATION_SETS: Array<ConfigurationSet> = [
    {
        name: $gettext('Kurz und bündig'),
        configuration: {
            anonymous: true,
            duration: 7,
            automaticPairing: true,
            type: AssessmentType.Table,
            payload: ASSESSMENT_TYPES[AssessmentType.Table].defaultPayload,
        },
    },
    {
        name: $gettext('Strukturiert begleitet'),
        configuration: {
            anonymous: true,
            duration: 7,
            automaticPairing: true,
            type: AssessmentType.Form,
            payload: ASSESSMENT_TYPES[AssessmentType.Form].defaultPayload,
        },
    },
    {
        name: $gettext('Selbstbestimmt'),
        configuration: {
            anonymous: true,
            duration: 7,
            automaticPairing: true,
            type: AssessmentType.Freetext,
            payload: ASSESSMENT_TYPES[AssessmentType.Freetext].defaultPayload,
        },
    },
];

export function defaultConfiguration(): ProcessConfiguration {
    return CONFIGURATION_SETS[0].configuration;
}

function defaultCriteriaForm() {
    return [
        {
            text: $gettext('Aufbau'),
            description: $gettext(
                'Wo sind die grundlegenden Abschnitte (Einführung, Schlussfolgerung, Literatur, Zitate, usw.) und sind sie angemessen? Wenn nicht, was fehlt?\nHat der Schreiber verschiedene Überschriftenstile verwendet um die Abschnitte klar zu kennzeichnen? Kurze Erklärung.\nWie wurde der Inhalt geordnet? War er logisch, klar und leicht zu folgen? Kurze Erklärung.'
            ),
        },
        {
            text: $gettext('Grammatik und Stil'),
            description: $gettext(
                'Gibt es grammatische oder orthografische Probleme?\nIst der Schreibstil klar? Sind die Absätze und die enthaltenen Sätze zusammengehörig?'
            ),
        },
        {
            text: $gettext('Inhalt'),
            description: $gettext(
                'Hat der Autor hinreichend verdichtet und die Aufgabe diskutiert? Kurze Erklärung.\nHat der Autor umfassend Material aus Standardquellen benutzt? Wenn nicht, was fehlt?\nHat der Autor auch eigene Gedanken beigetragen, oder hat er mehrheitlich Zusammenfassungen von Veröffentlichungen/Daten zusammengetragen? Kurze Erklärung.'
            ),
        },
        {
            text: $gettext('Zitate'),
            description: $gettext(
                'Hat der Autor Zitatquellen passend und korrekt angebeben? Notiere unkorrekte Formatierungen.\nSind alle Zitate auch in dem Literaturhinweis zu finden? Notiere die Unstimmigkeiten.'
            ),
        },
    ];
}

function defaultCriteriaTable() {
    return [
        { text: $gettext('These: Klarheit, Bedeutung') },
        { text: $gettext('Belege: Relevanz, Glaubwürdigkeit, Aussagekraft') },
        { text: $gettext('Aufbau: Anordnung des Inhalts, Nachvollziehbarkeit') },
        { text: $gettext('Handwerk: Orthografie, Grammatik, Zeichensetzung') },
        { text: $gettext('Gesamtwirkung') },
    ];
}
