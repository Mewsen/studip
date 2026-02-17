<script setup>
import {computed, reactive} from 'vue';
import {$gettext} from '../../../../assets/javascripts/lib/gettext';
import {storePublicationURL, updatePublicationURL} from '../helpers/urls';
import StudipSwitch from "../../StudipSwitch.vue";
import StudipTooltipIcon from "../../StudipTooltipIcon.vue";

const CSRF = STUDIP.CSRF_TOKEN;

const props = defineProps({
    publication: {
        type: Object,
        default: () => ({})
    }
});

const form = reactive({
    version: '1.3a',
    instructor_role: 'dozent',
    student_role: 'autor',
    ...props.publication,
    status: props.publication.status?.value !== 'inactive',
    enrollment_deadline: props.publication.enrollment_deadline && STUDIP.Dates.isoToDatetimeLocal(props.publication.enrollment_deadline),
    start_date: props.publication.start_date && STUDIP.Dates.isoToDatetimeLocal(props.publication.start_date),
    end_date: props.publication.end_date && STUDIP.Dates.isoToDatetimeLocal(props.publication.end_date),
    provisioning_mode_instructor: props.publication.provisioning_mode_instructor?.value ?? 2,
    provisioning_mode_student: props.publication.provisioning_mode_student?.value ?? 1,
});

const formActionURL = computed(() => {
    if (props.publication.id) {
        return updatePublicationURL(props.publication.id);
    }

    return storePublicationURL();
});

const provisioningModeHelperTextHTML = `
${$gettext('Diese Einstellung legt fest, wie Konten beim ersten Start verwaltet werden. Verschiedene Modi werden unterstützt:')}
<ul>
    <li>
        <b>${$gettext('Nur neue Konten (automatisch)')}</b>
        <p>${$gettext('Für Nutzende, die die Plattform zum ersten Mal verwenden, wird automatisch ein Konto erstellt. Dies ist die Standardeinstellung für Studierende.')}</p>
    </li>
    <li>
        <b>${$gettext('Bestehende und neue Konten (Abfrage)')}</b>
        <p>${$gettext('Die Nutzenden können wählen, wie sie vorgehen möchten. Sie können ein bestehendes Konto verknüpfen oder ein neues Konto erstellen lassen. Dies ist die flexibelste Option und die Standardeinstellung für Lehrende.')}</p>
    </li>
    <li>
        <b>${$gettext('Nur bestehende Konten (Abfrage)')}</b>
        <p>${$gettext('Die Nutzenden werden aufgefordert, ein bestehendes Konto zu verknüpfen und können ohne diese Verknüpfung nicht auf die Ressourcen des Tools zugreifen.')}</p>
    </li>
</ul>
`;
</script>

<template>
    <form
        class="default"
        :action="formActionURL"
        method="post"
        v-bind="$attrs"
    >
        <input type="hidden" :name="CSRF.name" :value="CSRF.value" />

        <fieldset class="undecorated">
            <label class="studiprequired m-0">
                <span class="textlabel">{{ $gettext('Name') }}</span>
                <span :title="$gettext('Name ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                <input
                    required
                    type="text"
                    name="name"
                    v-model="form.name" />
            </label>

            <label class="studiprequired">
                <span class="textlabel">{{ $gettext('Version') }}</span>
                <span :title="$gettext('Version ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                <select name="version" v-model="form.version">
                    <option value="1.1">{{ $gettext('1.0/1.1') }}</option>
                    <option value="1.3a">{{ $gettext('1.3a') }}</option>
                </select>
            </label>

            <StudipSwitch
                name="status"
                v-model="form.status"
                :label="$gettext('Status')"
                :title="form.status ? $gettext('LTI-Veröffentlichung deaktivieren') : $gettext('LTI-Veröffentlichung aktivieren')"
            />
        </fieldset>

        <fieldset>
            <legend>
                {{ $gettext('Konfiguration') }}
            </legend>

            <label>
                {{ $gettext('Anmeldefrist') }}
                <StudipTooltipIcon
                    id="enrollment-deadline-help"
                    :text="$gettext('Wenn gesetzt, können externe Nutzer:innen sich nur bis diesem Datum für diese Veranstaltung anmelden.')"
                />
                <input
                    type="datetime-local"
                    name="enrollment_deadline"
                    v-model="form.enrollment_deadline"
                    :placeholder="$gettext('Bis')"
                    autocomplete="off"
                    aria-describedby="enrollment-deadline-help"
                />
            </label>

            <label>
                {{ $gettext('Startdatum') }}
                <StudipTooltipIcon
                    id="start-date-help"
                    :text="$gettext('Wenn gesetzt, können externe Nutzer:innen nur ab diesem Datum auf diese Veranstaltung zugreifen.')"
                />
                <input
                    type="datetime-local"
                    name="start_date"
                    v-model="form.start_date"
                    :placeholder="$gettext('Von')"
                    autocomplete="off"
                    aria-describedby="start-date-help"
                />
            </label>

            <label>
                {{ $gettext('Enddatum') }}
                <StudipTooltipIcon
                    id="end-date-help"
                    :text="$gettext('Wenn gesetzt, können externe Nutzer:innen nur bis zu diesem Datum auf diese Veranstaltung zugreifen.')"
                />
                <input
                    type="datetime-local"
                    name="end_date"
                    v-model="form.end_date"
                    :placeholder="$gettext('Bis')"
                    autocomplete="off"
                    aria-describedby="end-date-help"
                />
            </label>

            <label>
                {{ $gettext('Maximale Anzahl eingeschriebener Benutzer') }}
                <StudipTooltipIcon
                    id="maximum-enrolled-users-help"
                    :text="$gettext('Die maximale Anzahl an externe Nutzer:innen, die auf diese Veranstaltung zugreifen können. Wenn das Feld leer oder auf null gesetzt ist, gibt es keine Begrenzung.')"
                />
                <input
                    type="number"
                    min="0"
                    step="1"
                    name="maximum_enrolled_users"
                    v-model="form.maximum_enrolled_users"
                    :placeholder="$gettext('Unbegrenzt')"
                    autocomplete="off"
                    aria-describedby="maximum-enrolled-users-help"
                />
            </label>

            <label>
                {{ $gettext('Rolle der Lehrende') }}
                <select name="instructor_role" v-model="form.instructor_role">
                    <option value="dozent">{{ $gettext('Dozent') }}</option>
                    <option value="tutor">{{ $gettext('Tutor') }}</option>
                    <option value="autor">{{ $gettext('Autor') }}</option>
                </select>
            </label>

            <label>
                {{ $gettext('Rolle der Studierende') }}
                <select name="student_role" v-model="form.student_role">
                    <option value="autor">{{ $gettext('Autor') }}</option>
                    <option value="user">{{ $gettext('User') }}</option>
                </select>
            </label>

            <label>
                {{ $gettext('Bereitstellungsmodus beim ersten Start durch die Lehrende') }}
                <StudipTooltipIcon
                    id="provisioning-mode-instructor-help"
                    :text="provisioningModeHelperTextHTML"
                    :isHtml="true"
                />
                <select
                    name="provisioning_mode_instructor"
                    v-model="form.provisioning_mode_instructor"
                    aria-describedby="provisioning-mode-instructor-help"
                >
                    <option value="1">{{ $gettext('Nur neue Konten (automatisch)') }}</option>
                    <option value="2">{{ $gettext('Bestehende und neue Konten (Abfrage)') }}</option>
                    <option value="3">{{ $gettext('Nur bestehende Konten (Abfrage)') }}</option>
                </select>
            </label>

            <label>
                {{ $gettext('Bereitstellungsmodus beim ersten Start durch die Studierende') }}
                <StudipTooltipIcon
                    id="provisioning-mode-student-help"
                    :text="provisioningModeHelperTextHTML"
                    :isHtml="true"
                />
                <select
                    name="provisioning_mode_student"
                    v-model="form.provisioning_mode_student"
                    aria-describedby="provisioning-mode-student-help"
                >
                    <option value="1">{{ $gettext('Nur neue Konten (automatisch)') }}</option>
                    <option value="2">{{ $gettext('Bestehende und neue Konten (Abfrage)') }}</option>
                    <option value="3">{{ $gettext('Nur bestehende Konten (Abfrage)') }}</option>
                </select>
            </label>
        </fieldset>

        <slot name="footer">
            <footer data-dialog-button>
                <button class="button accept">
                    {{ $gettext('Speichern') }}
                </button>
                <button class="button cancel" type="button" data-dialog-close>
                    {{ $gettext('Abbrechen') }}
                </button>
            </footer>
        </slot>
    </form>
</template>
