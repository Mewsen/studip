<script setup>
import {computed, reactive} from 'vue';
import {$gettext} from '../../../../assets/javascripts/lib/gettext';
import StudipSelect from "../../../components/StudipSelect.vue";
import StudipTooltipIcon from "../../../components/StudipTooltipIcon.vue";
import {storeResourceURL, updateResourceURL} from "../helpers/urls";

const CSRF = STUDIP.CSRF_TOKEN;

const props = defineProps({
    registrations: {
        type: Array,
        default: () => ([])
    },
    resource: {
        type: Object,
        default: () => ({})
    }
});

const form = reactive({
    ...props.resource,
    registration: props.registrations.find(({ id }) => id === props.resource?.registration?.id)
});

const formActionURL = computed(() => {
    if (props.resource.id) {
        return updateResourceURL(props.resource.id);
    }

    return storeResourceURL();
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

        <legend v-if="resource.id" class="hide-in-dialog">
            {{ $gettext('Ressource bearbeiten') }}
        </legend>
        <legend v-else class="hide-in-dialog">
            {{ $gettext('Neuen Ressource hinzufügen') }}
        </legend>

        <fieldset class="undecorated">
            <div class="select-input-group">
                <label  for="select-lti-tool-input" class="studiprequired">
                    <span class="textlabel">{{ $gettext('LTI-Tool') }}</span>
                    <span :title="$gettext('LTI-Tool ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                </label>
                <StudipSelect
                    id="select-lti-tool-input"
                    :placeholder="$gettext('Suchen order auswählen...')"
                    label="name"
                    :options="registrations"
                    v-model="form.registration"
                    name="registration_id"
                    required
                    :reduce="r => r.id"
                    :clearable="true"
                >
                    <template #no-options>
                        <div>{{ $gettext('Es gibt kein LTI-Tool vorhanden.') }}</div>
                    </template>
                </StudipSelect>
            </div>

            <label class="studiprequired">
                <span class="textlabel">{{ $gettext('Titel') }}</span>
                <span :title="$gettext('Titel ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                <input
                    required
                    type="text"
                    name="title"
                    v-model="form.title" />
            </label>

            <label>
                {{ $gettext('Beschreibung') }}
                <textarea name="description" v-model="form.description" rows="5"></textarea>
            </label>

            <label>
                {{ $gettext('Zusätzliche LTI-Parameter') }}
                <StudipTooltipIcon
                    :text="$gettext('Ein Wert pro Zeile. Dieser überschreibt den Standard-LTI-Parameter bei der LTI-Registrierung.')"
                />
                <textarea name="custom_parameters" v-model="form.custom_parameters"></textarea>
            </label>

            <label>
                {{ $gettext('Farbe') }}
                <input type="color" name="color" v-model="form.color" />
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
