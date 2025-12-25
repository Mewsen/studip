<script setup>
import StudipDialog from "../../StudipDialog.vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipDateTime from "../../StudipDateTime.vue";
import {computed} from "vue";

const props = defineProps({
    resource: {
        type: Object,
        required: true
    }
});

const isOpen = defineModel('isOpen');

const title = computed(() => props.resource.title || props.resource.registration.name);
const description = computed(() => props.resource.description || props.resource.registration.description);
const configs = computed(() => {
    if (props.resource.registration.version === '1.3a') {
        return JSON.stringify({
            version: props.resource.registration.version,
            client_id: props.resource.deployment.client_id,
            deployment_id: props.resource.deployment.deployment_id,
            custom_parameters: props.resource.custom_parameters,
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
                container: props.resource.registration.container,
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
                    <dd class="break-word">
                        <p>{{ description }}</p>
                    </dd>

                    <dt>{{ $gettext('Erstellt am') }}</dt>
                    <dd>
                        <StudipDateTime :iso="resource.mkdate" />
                    </dd>


                    <dt>{{ $gettext('Direktlink zum LTI-Tool') }}</dt>
                    <dd>
                        <a href="#" target="_blank">
                            TBA
                        </a>
                    </dd>

                    <dt>{{ $gettext('Client-ID') }}</dt>
                    <dd>
                        {{ resource.deployment.client_id }}
                    </dd>

                    <dt>{{ $gettext('Deployment-ID') }}</dt>
                    <dd>
                        {{ resource.deployment.deployment_id }}
                    </dd>

                    <dt>{{ $gettext('Zusätzliche LTI-Parameter') }}</dt>
                    <dd class="break-word">
                        <p>{{ resource.custom_parameters }}</p>
                    </dd>


                </dl>

                <pre><code class="json">{{ configs }}</code></pre>
            </div>
        </template>
    </StudipDialog>
</template>
