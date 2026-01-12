<script setup>
import {computed, onMounted, reactive, useTemplateRef} from 'vue';
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
    dozent_role: 'dozent',
    autor_role: 'autor',
    ...props.publication,
    status: props.publication.status.value === 'active',
    enrollment_deadline: props.publication.enrollment_deadline && STUDIP.Dates.isoToDatetimeLocal(props.publication.enrollment_deadline),
    start_date: props.publication.start_date && STUDIP.Dates.isoToDatetimeLocal(props.publication.start_date),
    end_date: props.publication.end_date && STUDIP.Dates.isoToDatetimeLocal(props.publication.end_date)
});

const formActionURL = computed(() => {
    if (props.publication.id) {
        return updatePublicationURL(props.publication.id);
    }

    return storePublicationURL();
});

const nameInput = useTemplateRef('nameInput');

onMounted(() => {
    nameInput.value.focus();
});
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
                    ref="nameInput"
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
                <select name="dozent_role" v-model="form.dozent_role">
                    <option value="dozent">{{ $gettext('Dozent') }}</option>
                    <option value="tutor">{{ $gettext('Tutor') }}</option>
                    <option value="autor">{{ $gettext('Autor') }}</option>
                </select>
            </label>

            <label>
                {{ $gettext('Rolle der Studierende') }}
                <select name="autor_role" v-model="form.autor_role">
                    <option value="autor">{{ $gettext('Autor') }}</option>
                    <option value="user">{{ $gettext('User') }}</option>
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
