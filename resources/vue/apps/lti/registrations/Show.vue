<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipIcon from "../../../components/StudipIcon.vue";
import {computed} from "vue";
import DeploymentIndex from "../../../components/lti/deployments/DeploymentIndex.vue";
import {editRegistrationURL} from "../../../components/lti/helpers/urls";
import LtiApp from "../../../components/lti/LtiApp.vue";
import CopyableCodeBlock from "../../../components/CopyableCodeBlock.vue";

const props = defineProps({
    registration: {
        type: Object,
        required: true
    }
});

const pageTitle = computed(() => {
    if (props.registration.role === 'tool') {
        return $gettext('Details zu LTI-Tools');
    } else if (props.registration.role === 'platform') {
        return $gettext('Details zu LTI-Platforms');
    }

    return $gettext('Details zur LTI-Registrierung');
});

const editRegistration = () => STUDIP.Dialog.fromURL(editRegistrationURL(props.registration.id), { width: '900' });

const showPlatformData = () => STUDIP.Dialog.fromURL(STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/registrations/platform_data?registration=${props.registration.id}`), { width: '900', height: '700' });
const showToolData = () => STUDIP.Dialog.fromURL(STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/registrations/tool_data?registration=${props.registration.id}`), { width: '900', height: '700' });
</script>

<template>
    <LtiApp>
        <header class="header">
            <div class="header__content header__content--with-actions">
                <ul class="breadcrumb">
                    <li>
                        {{ pageTitle }}
                    </li>
                </ul>

                <div class="actions">
                    <button
                        type="button"
                        class="button button--icon-only"
                        @click="editRegistration"
                        :title="registration.role === 'platform' ? $gettext('LTI-Platform bearbeiten') : $gettext('LTI-Tool bearbeiten')">
                        <StudipIcon shape="edit" aria-hidden="true" />
                    </button>
                    <button
                        v-if="registration.role === 'tool'"
                        type="button"
                        class="button button--icon-only"
                        @click="showPlatformData"
                        :title="$gettext('Daten zur LTI-Platform anzeigen')">
                        <StudipIcon shape="info" aria-hidden="true" />
                    </button>
                    <button
                        v-if="registration.role === 'platform'"
                        type="button"
                        class="button button--icon-only"
                        @click="showToolData"
                        :title="$gettext('Daten zur LTI-Tool anzeigen')">
                        <StudipIcon shape="info" aria-hidden="true" />
                    </button>
                </div>
            </div>
        </header>
        <dl>
            <dt>{{ $gettext('Name') }}</dt>
            <dd>{{ registration.name }}</dd>

            <template v-if="registration.description">
                <dt>{{ $gettext('Beschreibung') }}</dt>
                <dd class="break-word" v-html="registration.description">
                </dd>
            </template>

            <template v-if="registration.data_protection_notes">
                <dt>{{ $gettext('Datenschutzhinweise') }}</dt>
                <dd class="break-word" v-html="registration.data_protection_notes">
                </dd>
            </template>

            <template v-if="registration.terms_of_use_url">
                <dt>{{ $gettext('URL zu den Nutzungsbedingungen') }}</dt>
                <dd>
                    <a :href="registration.terms_of_use_url" target="_blank">
                        {{ registration.terms_of_use_url }}
                    </a>
                </dd>
            </template>

            <template v-if="registration.privacy_policy_url">
                <dt>{{ $gettext('URL zur Datenschutzerklärung') }}</dt>
                <dd>
                    <a :href="registration.privacy_policy_url" target="_blank">
                        {{ registration.privacy_policy_url }}
                    </a>
                </dd>
            </template>

            <dt>{{ $gettext('Status') }}</dt>
            <dd>
                {{ registration.status.label }}
            </dd>

            <dt>{{ $gettext('Version') }}</dt>
            <dd>
                <p>{{ registration.version }}</p>
            </dd>

            <template v-if="registration.role === 'tool'">
                <dt>{{ $gettext('Launch container') }}</dt>
                <dd>
                    <p>{{ registration.container.label }}</p>
                </dd>

                <dt>{{ $gettext('Tool-ID') }}</dt>
                <dd>
                    <CopyableCodeBlock :content="registration.issuer" />
                </dd>

                <dt>{{ $gettext('Launch URL') }}</dt>
                <dd>
                    <CopyableCodeBlock :content="registration.launch_url" />
                </dd>

                <template v-if="registration.version === '1.3a'">
                    <dt>{{ $gettext('Initiate login URL') }}</dt>
                    <dd>
                        <CopyableCodeBlock :content="registration.auth_init_url" />
                    </dd>
                    <dt>{{ $gettext('Deep-linking URL') }}</dt>
                    <dd>
                        <CopyableCodeBlock :content="registration.deep_linking_url" />
                    </dd>

                    <template v-if="registration.key_type === 'jwk_keyset'">
                        <dt>{{ $gettext('JWKS-URL') }}</dt>
                        <dd>
                            <CopyableCodeBlock :content="registration.jwks_url" />
                        </dd>
                    </template>

                    <template v-if="registration.key_type === 'public_key'">
                        <dt>{{ $gettext('Öffentlicher Schlüssel') }}</dt>
                        <dd>
                            <CopyableCodeBlock :content="registration.public_key" />
                        </dd>
                    </template>
                </template>
                <template v-if="registration.version === '1.1'">
                    <dt>{{ $gettext('Consumer-Key') }}</dt>
                    <dd>
                        <CopyableCodeBlock :content="registration.consumer_key" />
                    </dd>

                    <dt>{{ $gettext('Consumer-Secret') }}</dt>
                    <dd>
                        <CopyableCodeBlock :content="registration.consumer_secret" />
                    </dd>

                    <dt>{{ $gettext('Personendaten an das LTI-Tool senden') }}</dt>
                    <dd>
                        <p>{{ registration.send_lis_person ? $gettext('Ja') : $gettext('Nein') }}</p>
                    </dd>
                </template>

                <dt>{{ $gettext('Zusätzliche LTI-Parameter') }}</dt>
                <dd>
                    <CopyableCodeBlock :content="registration.custom_parameters" />
                </dd>
            </template>

            <template v-if="registration.role === 'platform'">
                <dt>{{ $gettext('Plattform-ID') }}</dt>
                <dd>
                    <CopyableCodeBlock :content="registration.issuer" />
                </dd>

                <dt>{{ $gettext('OIDC authentication URL') }}</dt>
                <dd>
                    <CopyableCodeBlock :content="registration.auth_login_url" />
                </dd>

                <dt>{{ $gettext('Token-URL') }}</dt>
                <dd>
                    <CopyableCodeBlock :content="registration.token_url" />
                </dd>

                <template v-if="registration.key_type === 'jwk_keyset'">
                    <dt>{{ $gettext('JWKS-URL') }}</dt>
                    <dd>
                        <CopyableCodeBlock :content="registration.jwks_url" />
                    </dd>
                </template>

                <template v-if="registration.key_type === 'public_key'">
                    <dt>{{ $gettext('Öffentlicher Schlüssel') }}</dt>
                    <CopyableCodeBlock :content="registration.public_key" />
                </template>
            </template>
        </dl>
        <br />
        <DeploymentIndex
            v-if="registration.version === '1.3a'"
            :deployments="registration.deployments"
            :registration="registration"
            :withCaption="true"
        />
    </LtiApp>
</template>´
