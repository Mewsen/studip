<script setup>
import {computed, onMounted, reactive, useTemplateRef} from 'vue';
import {$gettext} from '../../../../assets/javascripts/lib/gettext';
import StudipSelect from "../../../components/StudipSelect.vue";
import StudipTooltipIcon from "../../../components/StudipTooltipIcon.vue";
import {storeResourceURL, updateResourceURL} from "../helpers/urls";
import StudipIcon from "../../StudipIcon.vue";

const CSRF = STUDIP.CSRF_TOKEN;

const props = defineProps({
    registrations: {
        type: Array,
        default: () => ([])
    },
    resource: {
        type: Object,
        default: () => ({})
    },
    icons: {
        type: Array,
        default: () => ([])
    }
});

const form = reactive({
    launch_container: 1,
    ...props.resource,
    colorPicked: props.resource.color,
    registration: props.registrations.find(({ id }) => id === props.resource?.registration?.id)
});

const formActionURL = computed(() => {
    if (props.resource.id) {
        return updateResourceURL(props.resource.id);
    }

    return storeResourceURL();
});

const nameInputRef = useTemplateRef('nameInput');

onMounted(() => nameInputRef.value.focus());
</script>

<template>
    <form
        class="default resource-form use-utility-classes"
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
                <label for="select-lti-tool-input" class="studiprequired">
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
                    ref="nameInput"
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
                <span>{{ $gettext('Launch container') }}</span>
                <select name="launch_container" v-model="form.launch_container">
                    <option value="1">{{ $gettext('Neues Fenster') }}</option>
                    <option value="2">{{ $gettext('Anzeige im IFRAME auf der Seite') }}</option>
                </select>
            </label>

            <label for="color-input">
                {{ $gettext('Farbe') }}
            </label>
            <div class="flex items-center justify-start gap-5">
                <input id="color-input" type="color" v-model="form.color" @change="form.colorPicked = true"/>
                <input type="hidden" name="color" :value="form.colorPicked  ? form.color : null" />
                <button
                    v-if="form.colorPicked"
                    type="button"
                    class="button-base styleless"
                    :title="$gettext('Ausgewählte Farbe zurücksetzen')"
                    @click="form.color = null; form.colorPicked = false">
                    <StudipIcon shape="decline" :size="20"/>
                </button>
            </div>

            <div class="icons-input-label-container">
                <label for="studip-icons">
                    {{ $gettext('Icon') }}
                </label>
                <button
                    v-if="form.icon"
                    type="button"
                    class="button-base styleless"
                    :title="$gettext('Ausgewähltes Icon zurücksetzen')"
                    @click="form.icon = null">
                    <StudipIcon shape="decline" :size="20"/>
                </button>
            </div>
            <div id="studip-icons" class="studip-icons-input-container">
                <input type="hidden" v-model="form.icon" name="icon" />

                <template v-for="icon in icons" :key="icon">
                    <button
                        class="button"
                        type="button"
                        :title="icon"
                        :class="{
                            'disabled': form.icon && form.icon !== icon,
                            'active': form.icon === icon
                        }"
                        @click="form.icon = icon">
                        <StudipIcon :shape="icon" :size="35" />
                    </button>
                </template>
            </div>
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
