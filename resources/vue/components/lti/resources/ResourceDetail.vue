<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipDateTime from "../../StudipDateTime.vue";
import {computed} from "vue";
import {launchResourceURL} from "../helpers/urls";
import {useLtiConfig} from "../../../store/pinia/lti/Config";
import CopyableCodeBlock from "../../CopyableCodeBlock.vue";

const ltiConfig = useLtiConfig();
const props = defineProps({
    resource: {
        type: Object,
        required: true
    }
});


const title = computed(() => props.resource.title || props.resource.registration.name);
const description = computed(() => props.resource.description || props.resource.registration.description);
const resourceURL = computed(() => launchResourceURL(props.resource.id, props.resource.registration.version));

const configs = computed(() => {
    const common = {
        version: props.resource.registration.version,
        client_id: props.resource.deployment.client_id,
        deployment_key: props.resource.deployment.deployment_key,
        custom_parameters: props.resource.custom_parameters,
        container: props.resource.container
    };

    if (props.resource.registration.version === '1.3a') {
        return JSON.stringify({
            ...common,
            launch_type: props.resource.launch_type,
            registration: {
                id: props.resource.registration.id,
                audience: props.resource.registration.meta.configs.audience,
                launch_url: props.resource.registration.meta.configs.launch_url,
                auth_init_url: props.resource.registration.meta.configs.auth_init_url,
                deep_linking_url: props.resource.registration.meta.configs.deep_linking_url,
                key_type: props.resource.registration.meta.configs.key_type,
                jwks_url: props.resource.registration.meta.configs.jwks_url,
                public_key: props.resource.registration.meta.configs.public_key,
                custom_parameters: props.resource.registration.meta.configs.custom_parameters ?? '',
                container: props.resource.registration.meta.configs.container
            }
        }, null, 2);
    }

    if (props.resource.registration.version === '1.1') {
        return JSON.stringify({
            ...common,
            registration: {
                id: props.resource.registration.id,
                audience: props.resource.registration.meta.configs.audience,
                launch_url: props.resource.registration.meta.configs.launch_url,
                consumer_key: props.resource.registration.meta.configs.consumer_key,
                consumer_secret: props.resource.registration.meta.configs.consumer_secret,
                send_lis_person: props.resource.registration.meta.configs.send_lis_person,
                oauth_signature_method: props.resource.registration.meta.configs.oauth_signature_method,
                custom_parameters: props.resource.registration.meta.configs.custom_parameters ?? '',
                container: props.resource.registration.meta.configs.container
            }
        }, null, 2);
    }

    return props.resource;
});
</script>

<template>
    <div>
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
                    <CopyableCodeBlock :content="resource.deployment.client_id" />
                </dd>

                <dt>{{ $gettext('Deployment-ID') }}</dt>
                <dd>
                    <CopyableCodeBlock :content="resource.deployment.deployment_key" />
                </dd>

                <dt>{{ $gettext('Zusätzliche LTI-Parameter') }}</dt>
                <dd >
                    <CopyableCodeBlock
                        v-if="resource.custom_parameters || resource.registration.custom_parameters"
                        :content="resource.custom_parameters || resource.registration.custom_parameters"
                    />
                </dd>
            </dl>

            <CopyableCodeBlock v-if="ltiConfig.isAdmin"><code class="json">{{ configs }}</code></CopyableCodeBlock>
        </article>
    </div>
</template>
