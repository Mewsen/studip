<script setup>
import {computed, nextTick, onBeforeUnmount, onMounted, reactive, ref, useTemplateRef} from 'vue';
import {$gettext} from '../../../../assets/javascripts/lib/gettext';
import StudipSelect from "../../../components/StudipSelect.vue";
import StudipTooltipIcon from "../../../components/StudipTooltipIcon.vue";
import {selectContentURL, storeResourceURL, updateResourceURL} from "../helpers/urls";
import StudipIcon from "../../StudipIcon.vue";
import StudipMessageBox from "../../StudipMessageBox.vue";

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

const formState = reactive({
    registration: props.registrations.find(({ id }) => id === props.resource?.registration?.id),
    resources: [{
        launch_container: 'window',
        colorPicked: props.resource.color,
        isCollapsed: false,
        isConfigurationCollapsed: true,
        ...props.resource
    }]
});

const errors = ref([]);
const contentSelectionIframe = ref(false);

const formActionURL = computed(() => {
    if (props.resource.id) {
        return updateResourceURL(props.resource.id);
    }

    return storeResourceURL();
});
const isSubmitAble = computed(() => !contentSelectionIframe.value && formState.registration);

const resourceFormRef = useTemplateRef('resourceForm');

const validateForm = () => {
    errors.value = [];

    if (!formState.registration) {
        errors.value.push({
            key: 'registration',
            value: $gettext('LTI-Tool')
        });

        return;
    }

    resourceFormRef.value.submit();
}

const addResourceRow = () => {
    formState.resources = formState.resources.map(r => ({
        ...r,
        isCollapsed: true
    }));

    formState.resources.push({
        launch_container: 'window',
        colorPicked: props.resource.color,
        isCollapsed: false,
        isConfigurationCollapsed: true
    });

    nextTick(() => {
        document.getElementById(`resource-title-${formState.resources.length - 1}`)?.focus();
    });
};
const removeResourceRow = index => formState.resources.splice(index, 1);
const objectToKeyValueString = object => Object.entries(object).map(([key, value]) => `${key}=${value}`).join('\n');

const handleLtiMessage = event => {
    if (
        event.target.STUDIP.ABSOLUTE_URI_STUDIP !== STUDIP.ABSOLUTE_URI_STUDIP
        || event.data?.type !== 'LTI_DEEP_LINKING_RESPONSE'
        || event.data.ltiResources.length === 0
    ) {
        return;
    }

    contentSelectionIframe.value = false;
    console.log(event.data.ltiResources);

    const resourceCount = event.data.ltiResources.length;
    formState.resources = event.data.ltiResources.map(r => ({
        ...r,
        launch_container: 'window',
        custom_parameters: objectToKeyValueString(r.custom),
        colorPicked: false,
        isCollapsed: resourceCount > 1,
        isConfigurationCollapsed: true
    }));

    STUDIP.Report.success(
        $gettext('%{count} LTI-Ressourcen wurden ausgewählt.', {count: formState.resources.length}),
        formState.resources.map(r => r.title)
    );
}

onMounted(() => {
    window.addEventListener('message', handleLtiMessage);
});

onBeforeUnmount(() => {
    window.removeEventListener('message', handleLtiMessage);
});
</script>

<template>
    <form
        ref="resourceForm"
        class="default resource-form use-utility-classes"
        :action="formActionURL"
        method="post"
        @submit.prevent="validateForm"
        v-bind="$attrs"
    >
        <input type="hidden" :name="CSRF.name" :value="CSRF.value" />

        <div v-if="errors.length" class="mb-10" >
            <StudipMessageBox type="error" :details="errors.map(error => error.value)">
                {{ $gettext('Folgende Felder sind Pflichtfelder:') }}
            </StudipMessageBox>
        </div>

        <fieldset class="undecorated">
            <legend v-if="resource.id" class="hide-in-dialog">
                {{ $gettext('Ressource bearbeiten') }}
            </legend>
            <legend v-else class="hide-in-dialog">
                {{ $gettext('Neuen Ressource hinzufügen') }}
            </legend>

            <section class="select-input-group">
                <label for="select-lti-tool-input" class="studiprequired">
                    <span class="textlabel">{{ $gettext('LTI-Tool') }}</span>
                    <span :title="$gettext('LTI-Tool ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                </label>
                <StudipSelect
                    :placeholder="$gettext('Suchen oder auswählen...')"
                    label="name"
                    :options="registrations"
                    v-model="formState.registration"
                    required
                    :clearable="true"
                >
                    <template #no-options>
                        <div>{{ $gettext('Es gibt kein LTI-Tool vorhanden.') }}</div>
                    </template>
                </StudipSelect>
                <input id="select-lti-tool-input" type="hidden" name="registration_id" :value="formState.registration?.id" />
            </section>

            <button
                v-if="formState.registration?.deep_linking_url && !contentSelectionIframe"
                type="button"
                class="button resource-form__select-content-button"
                @click="contentSelectionIframe = true"
                :title="$gettext('Inhaltsauswahl mittels Deep Linking')"
                :aria-label="$gettext('Inhaltsauswahl mittels Deep Linking')"
                :aria-expanded="contentSelectionIframe"
            >
                {{ $gettext('Inhalt auswählen') }}
            </button>
        </fieldset>

        <fieldset v-if="contentSelectionIframe && formState.registration?.deep_linking_url">
            <legend>
                {{ $gettext('Inhaltsauswahl mittels Deep Linking') }}
                <button
                    type="button"
                    class="as-link"
                    @click="contentSelectionIframe = false"
                    :title="$gettext('Inhaltsauswahl schließen und LTI-Ressource manuell anlegen')"
                    :aria-label="$gettext('Inhaltsauswahl schließen und LTI-Ressource manuell anlegen')"
                    :aria-expanded="contentSelectionIframe"
                >
                    <StudipIcon shape="decline" :size="20" aria-hidden="true" />
                </button>
            </legend>

            <section class="resource-form__iframe-container">
                <iframe
                    :src="selectContentURL(formState.registration.id)"
                    loading="lazy"
                    class="lti-content"
                ></iframe>
            </section>
        </fieldset>

        <fieldset
            v-else
            v-for="(resource, index) in formState.resources"
            :key="index"
            :aria-expanded="!resource.isCollapsed"
            :class="{ 'collapsed': resource.isCollapsed }"
        >
            <input type="hidden" name="resource_id[]" :value="resource.id" />
            <input type="hidden" name="deployment_id[]" :value="resource.deployment_id" />

            <legend>
                <span class="flex items-center gap-5">
                    <button
                        type="button"
                        class="as-link"
                        @click="resource.isCollapsed = !resource.isCollapsed"
                        :aria-expanded="!resource.isCollapsed"
                        :title="resource.isConfigurationCollapsed ? $gettext('Aufklappen') : $gettext('Zuklappen')"
                        :aria-label="$gettext(
                    'Formularbereich für (%{title}) %{action}',
                {
                            title: resource.title ?? $gettext('Neue LTI-Ressource'),
                            action: resource.isConfigurationCollapsed ? $gettext('aufklappen') : $gettext('zuklappen')
                        }
                    )"
                    >
                        <StudipIcon v-if="resource.isCollapsed" shape="arr_1right" :size="20" aria-hidden="true" />
                        <StudipIcon v-else shape="arr_1up" :size="20" aria-hidden="true" />
                    </button>
                    {{ resource.title ?? $gettext('Neue LTI-Ressource') }}
                </span>
                <button
                    v-if="formState.resources.length > 1"
                    type="button"
                    class="as-link"
                    @click="removeResourceRow(index)"
                    :title="$gettext('Entfernen der LTI-Ressource')"
                    :aria-label="$gettext('Entfernen der LTI-Ressource')"
                >
                    <StudipIcon shape="decline" :size="20" aria-hidden="true" />
                </button>
            </legend>

            <input type="hidden" name="launch_url[]" :value="resource.launch_url" />

            <section v-show="!resource.isCollapsed">
                <label class="studiprequired">
                    <span class="textlabel">{{ $gettext('Titel') }}</span>
                    <span :title="$gettext('Titel ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                    <input
                        required
                        :id="`resource-title-${index}`"
                        type="text"
                        name="title[]"
                        v-model="resource.title"
                    />
                </label>

                <label>
                    {{ $gettext('Beschreibung') }}
                    <textarea name="description[]" v-model="resource.description"  rows="5"></textarea>
                </label>

                <label>
                    {{ $gettext('Zusätzliche LTI-Parameter') }}
                    <StudipTooltipIcon
                        :text="$gettext('Ein Wert pro Zeile. Dieser überschreibt den Standard-LTI-Parameter bei der LTI-Registrierung.')"
                    />
                    <textarea name="custom_parameters[]" v-model="resource.custom_parameters"></textarea>
                </label>

                <button
                    type="button"
                    class="as-link flex items-center gap-5"
                    @click="resource.isConfigurationCollapsed = !resource.isConfigurationCollapsed"
                    :aria-expanded="!resource.isConfigurationCollapsed"
                >
                    {{ $gettext('Weiterte Konfiguration %{action}', { action: resource.isConfigurationCollapsed ? $gettext('aufklappen') : $gettext('zuklappen')}) }}
                    <StudipIcon v-if="false" shape="arr_1up" :size="20" aria-hidden="true" />
                    <StudipIcon v-else shape="arr_1down" :size="20" aria-hidden="true" />
                </button>

                <section v-show="!resource.isConfigurationCollapsed">
                    <label>
                        <span>{{ $gettext('Container starten') }}</span>
                        <select name="launch_container[]" v-model="resource.launch_container">
                            <option value="window">{{ $gettext('Neues Fenster') }}</option>
                            <option value="iframe">{{ $gettext('Anzeige im IFRAME auf der Seite') }}</option>
                        </select>
                    </label>

                    <label for="color-input">
                        {{ $gettext('Farbe') }}
                    </label>
                    <div class="flex items-center justify-start gap-5">
                        <input id="color-input" type="color" v-model="resource.color" @change="resource.colorPicked = true"/>
                        <input type="hidden" name="color[]" :value="resource.colorPicked  ? resource.color : null" />
                        <button
                            v-if="resource.colorPicked"
                            type="button"
                            class="button-base styleless"
                            :title="$gettext('Ausgewählte Farbe zurücksetzen')"
                            @click="resource.color = null; resource.colorPicked = false">
                            <StudipIcon shape="decline" :size="20"/>
                        </button>
                    </div>

                    <div class="icons-input-label-container">
                        <label for="studip-icons">
                            {{ $gettext('Icon') }}
                        </label>
                        <button
                            v-if="resource.icon"
                            type="button"
                            class="button-base styleless"
                            :title="$gettext('Ausgewähltes Icon zurücksetzen')"
                            @click="resource.icon = null">
                            <StudipIcon shape="decline" :size="20"/>
                        </button>
                    </div>
                    <div id="studip-icons" class="studip-icons-input-container">
                        <input type="hidden" v-model="resource.icon" name="icon[]" />

                        <template v-for="icon in icons" :key="icon">
                            <button
                                class="button"
                                type="button"
                                :title="icon"
                                :class="{
                                    'disabled': resource.icon && resource.icon !== icon,
                                    'active': resource.icon === icon
                                }"
                                @click="resource.icon = icon">
                                <StudipIcon :shape="icon" :size="35" />
                            </button>
                        </template>
                    </div>
                </section>
            </section>
        </fieldset>

        <fieldset class="undecorated">
            <button
                v-if="!contentSelectionIframe"
                type="button"
                @click="addResourceRow"
                class="as-link flex items-center gap-5"
            >
                <StudipIcon shape="add" :size="20" aria-hidden="true" />
                <span class="label">{{ $gettext('Neuen Ressource hinzufügen') }}</span>
            </button>
            <br />
        </fieldset>

        <slot name="footer">
            <footer data-dialog-button>
                <button class="button accept" :disabled="!isSubmitAble">
                    {{ $gettext('Speichern') }}
                </button>
                <button class="button cancel" type="button" data-dialog-close>
                    {{ $gettext('Abbrechen') }}
                </button>
            </footer>
        </slot>
    </form>
</template>
