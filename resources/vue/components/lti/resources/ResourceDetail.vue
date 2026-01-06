<script setup>
import StudipDialog from "../../StudipDialog.vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipDateTime from "../../StudipDateTime.vue";
import {computed} from "vue";
import {launchResourceURL, selectContentURL} from "../helpers/urls";
import {useLtiConfig} from "../../../store/pinia/lti/Config";

const ltiConfig = useLtiConfig();
const props = defineProps({
    resource: {
        type: Object,
        required: true
    }
});

const isOpen = defineModel('isOpen');

const title = computed(() => props.resource.title || props.resource.registration.name);
const description = computed(() => props.resource.description || props.resource.registration.description);
const resourceURL = computed(() => {
    if (props.resource.launch_type === 'deep_linking') {
        return selectContentURL(props.resource.id);
    }

    return launchResourceURL(props.resource.id);
});

const configs = computed(() => {
    if (props.resource.registration.version === '1.3a') {
        return JSON.stringify({
            version: props.resource.registration.version,
            client_id: props.resource.deployment.client_id,
            deployment_key: props.resource.deployment.deployment_key,
            custom_parameters: props.resource.custom_parameters,
            container: props.resource.container,
            launch_type: props.resource.launch_type,
            registration: {
                id: props.resource.registration.id,
                audience: props.resource.registration.audience,
                launch_url: props.resource.registration.launch_url,
                auth_init_url: props.resource.registration.auth_init_url,
                deep_linking_url: props.resource.registration.deep_linking_url,
                key_type: props.resource.registration.key_type,
                jwks_url: props.resource.registration.jwks_url,
                public_key: props.resource.registration.public_key,
                custom_parameters: props.resource.registration.custom_parameters,
                container: props.resource.registration.container
            }
        }, null, 2);
    }

    return props.resource;
});
</script>

<template>
    <StudipDialog
        v-if="isOpen"
        :title="$gettext('Konfiguration des Ressources anzeigen')"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        height="700"
        width="600"
        @close="isOpen = false"
    >
        <template #dialogContent>
            <div class="lti">
                <dl class="use-utility-classes">
                    <dt>{{ $gettext('Titel') }}</dt>
                    <dd>{{ title }}</dd>

                    <dt>{{ $gettext('Beschreibung') }}</dt>
                    <dd class="break-word" v-html="description"></dd>

                    <dt>{{ $gettext('Erstellt am') }}</dt>
                    <dd>
                        <StudipDateTime :iso="resource.mkdate" />
                    </dd>

                    <dt>{{ $gettext('Direktlink zum LTI-Tool') }}</dt>
                    <dd>
                        <a :href="resourceURL" target="_blank" :title="$gettext('Anwendung starten')">
                            {{ resourceURL }}
                        </a>
                    </dd>

                    <template v-if="ltiConfig.isModerator">
                        <dt>{{ $gettext('Starttyp') }}</dt>
                        <dd>
                            <p>{{ resource.launch_type === 'deep_linking' ? $gettext('Inhaltsauswahl anzeigen (LTI deep linking)') : $gettext('Standard') }}</p>
                        </dd>
                    </template>
                </dl>

                <article v-if="ltiConfig.isModerator" class="studip">
                    <header>
                        <h1>
                            {{ $gettext('Konfiguration') }}
                        </h1>
                    </header>
                    <dl>
                        <dt>{{ $gettext('Client-ID') }}</dt>
                        <dd>
                            {{ resource.deployment.client_id }}
                        </dd>

                        <dt>{{ $gettext('Deployment-ID') }}</dt>
                        <dd>
                            {{ resource.deployment.deployment_key }}
                        </dd>

                        <dt>{{ $gettext('Zusätzliche LTI-Parameter') }}</dt>
                        <dd class="break-word">
                            {{ resource.custom_parameters || resource.registration.custom_parameters }}
                        </dd>
                    </dl>

                    <pre v-if="ltiConfig.isAdmin"><code class="json">{{ configs }}</code></pre>
                </article>
            </div>
        </template>
    </StudipDialog>
</template>

<style lang="scss" scoped>
pre {
    background-color: var(--light-gray-color-20);
    padding: 15px 20px;
    border-radius: 8px;
    overflow-x: auto;
    font-family: ui-monospace;
    line-height: 1.6;
    border: 1px solid var(--color--content-box-border);
}

pre code {
    display: block;
    padding: 0;
    background: transparent;
    color: inherit;
    white-space: pre;
}

pre::-webkit-scrollbar {
    height: 8px;
}

pre::-webkit-scrollbar-thumb {
    background-color: var(--color--button-inactive-border);
    border-radius: 4px;
}

pre::-webkit-scrollbar-track {
    background: transparent;
}
</style>
