<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipIcon from "../../../components/StudipIcon.vue";
import {computed} from "vue";
import DeploymentIndex from "../../../components/lti/deployments/DeploymentIndex.vue";

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

const editRegistration = () => STUDIP.Dialog.fromURL(STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/registrations/edit/${props.registration.id}?redirect=show`), { width: '900' });

const showPlatformData = () => STUDIP.Dialog.fromURL(STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/registrations/platform_data?registration=${props.registration.id}`), { width: '900', height: '700' });
const showToolData = () => STUDIP.Dialog.fromURL(STUDIP.URLHelper.getURL(`dispatch.php/admin/lti/registrations/tool_data?registration=${props.registration.id}`), { width: '900', height: '700' });
</script>

<template>
    <div class="lti">
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
                {{ registration.state ? $gettext('Aktiv') : $gettext('Ausstehend') }}
            </dd>

            <dt>{{ $gettext('Version') }}</dt>
            <dd>
                <p>{{ registration.version }}</p>
            </dd>

            <template v-if="registration.role === 'tool'">
                <dt>{{ $gettext('Personendaten an das LTI-Tool senden') }}</dt>
                <dd>
                    <p>{{ registration.send_lis_person ? $gettext('Ja') : $gettext('Nein') }}</p>
                </dd>

                <dt>{{ $gettext('Launch container') }}</dt>
                <dd>
                    <p>{{ registration.launch_container }}</p>
                </dd>

                <dt>{{ $gettext('Tool-ID') }}</dt>
                <dd>
                    <a :href="registration.issuer" target="_blank">
                        {{ registration.issuer }}
                    </a>
                </dd>

                <dt>{{ $gettext('Lunch URL') }}</dt>
                <dd>
                    <a :href="registration.launch_url" target="_blank">
                        {{ registration.launch_url }}
                    </a>
                </dd>

                <template v-if="registration.version === '1.3a'">
                    <dt>{{ $gettext('Initiate login URL') }}</dt>
                    <dd>
                        <a :href="registration.auth_init_url" target="_blank">
                            {{ registration.auth_init_url }}
                        </a>
                    </dd>
                    <dt>{{ $gettext('Deep-linking URL') }}</dt>
                    <dd>
                        <a :href="registration.deep_linking_url" target="_blank">
                            {{ registration.deep_linking_url }}
                        </a>
                    </dd>

                    <template v-if="registration.key_type === 'jwk_keyset'">
                        <dt>{{ $gettext('JWKS-URL') }}</dt>
                        <dd>
                            <a :href="registration.jwks_url" target="_blank">
                                {{ registration.jwks_url }}
                            </a>
                        </dd>
                    </template>

                    <template v-if="registration.key_type === 'public_key'">
                        <dt>{{ $gettext('Öffentlicher Schlüssel') }}</dt>
                        <dd>
                            {{ registration.public_key }}
                        </dd>
                    </template>
                </template>
                <template v-if="registration.version === '1.1'">
                    <dt>{{ $gettext('Consumer-Key') }}</dt>
                    <dd>
                        {{ registration.consumer_key }}
                    </dd>
                    <dt>{{ $gettext('Consumer-Secret') }}</dt>
                    <dd>
                        {{ registration.consumer_secret }}
                    </dd>
                </template>

                <dt>{{ $gettext('Zusätzliche LTI-Parameter') }}</dt>
                <dd>
                    {{ registration.custom_parameters }}
                </dd>
            </template>

            <template v-if="registration.role === 'platform'">
                <dt>{{ $gettext('Plattform-ID') }}</dt>
                <dd>
                    <a :href="registration.issuer" target="_blank">
                        {{ registration.issuer }}
                    </a>
                </dd>

                <dt>{{ $gettext('OIDC authentication URL') }}</dt>
                <dd>
                    <a :href="registration.auth_login_url" target="_blank">
                        {{ registration.auth_login_url }}
                    </a>
                </dd>

                <dt>{{ $gettext('Token-URL') }}</dt>
                <dd>
                    <a :href="registration.token_url" target="_blank">
                        {{ registration.token_url }}
                    </a>
                </dd>

                <template v-if="registration.key_type === 'jwk_keyset'">
                    <dt>{{ $gettext('JWKS-URL') }}</dt>
                    <dd>
                        <a :href="registration.jwks_url" target="_blank">
                            {{ registration.jwks_url }}
                        </a>
                    </dd>
                </template>

                <template v-if="registration.key_type === 'public_key'">
                    <dt>{{ $gettext('Öffentlicher Schlüssel') }}</dt>
                    <dd>
                        {{ registration.public_key }}
                    </dd>
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
    </div>
</template>
