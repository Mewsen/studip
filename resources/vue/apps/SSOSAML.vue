<template>
    <div class="ssosaml-config">
        <ContentBar isContentBar icon="lock-locked" :title="$gettext('SAML Service Provider Configuration')">
            <template #info-text>
                {{ $gettext('Configure your SAML Service Provider settings here.') }}
            </template>
        </ContentBar>

        <form @submit.prevent="saveConfig" class="default">
            <fieldset>
                <legend>{{ $gettext('Basic Settings') }}</legend>

                <label for="entityId">
                    {{ $gettext('Entity ID') }}
                    <input type="text" id="entityId" v-model="config.entityId" required>
                </label>

                <label for="assertionConsumerService">
                    {{ $gettext('Assertion Consumer Service URL') }}
                    <input type="url" id="assertionConsumerService" v-model="config.assertionConsumerService" required>
                </label>

                <label for="singleLogoutService">
                    {{ $gettext('Single Logout Service URL') }}
                    <input type="url" id="singleLogoutService" v-model="config.singleLogoutService">
                </label>

                <label for="nameIdFormat">
                    {{ $gettext('NameID Format') }}
                    <select id="nameIdFormat" v-model="config.nameIdFormat">
                        <option value="urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified">Unspecified</option>
                        <option value="urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress">Email Address</option>
                        <option value="urn:oasis:names:tc:SAML:2.0:nameid-format:persistent">Persistent</option>
                        <option value="urn:oasis:names:tc:SAML:2.0:nameid-format:transient">Transient</option>
                    </select>
                </label>
            </fieldset>

            <fieldset>
                <legend>{{ $gettext('Certificate Settings') }}</legend>

                <label for="x509cert">
                    {{ $gettext('X.509 Certificate') }}
                    <textarea id="x509cert" v-model="config.x509cert" rows="5"></textarea>
                </label>

                <label for="privateKey">
                    {{ $gettext('Private Key') }}
                    <textarea id="privateKey" v-model="config.privateKey" rows="5"></textarea>
                </label>
            </fieldset>

            <fieldset>
                <legend>{{ $gettext('Security Settings') }}</legend>

                <label>
                    <input type="checkbox" v-model="config.security.authnRequestsSigned">
                    {{ $gettext('Sign AuthnRequests') }}
                </label>

                <label>
                    <input type="checkbox" v-model="config.security.wantMessagesSigned">
                    {{ $gettext('Require Signed Messages') }}
                </label>

                <label>
                    <input type="checkbox" v-model="config.security.wantAssertionsSigned">
                    {{ $gettext('Require Signed Assertions') }}
                </label>
            </fieldset>

            <footer data-dialog-button>
                <button type="submit" class="button">
                    {{ $gettext('Save Configuration') }}
                </button>
                <button type="button" class="button" @click="resetForm">
                    {{ $gettext('Reset') }}
                </button>
            </footer>
        </form>
    </div>
</template>

<script>
import ContentBar from '@/vue/components/ContentBar.vue';
import { mapActions } from 'vuex';

export default {
    name: 'ssosaml',
    components: { ContentBar },
    data() {
        return {
            config: {
                entityId: '',
                assertionConsumerService: '',
                singleLogoutService: '',
                nameIdFormat: 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified',
                x509cert: '',
                privateKey: '',
                security: {
                    authnRequestsSigned: false,
                    wantMessagesSigned: false,
                    wantAssertionsSigned: false,
                }
            },
            originalConfig: {},
            isLoading: false,
            error: null
        };
    },
    methods: {
        ...mapActions('jsonapi', ['get', 'patch']),

        async saveConfig() {
            this.isLoading = true;
            this.error = null;
            try {
                const response = await this.patch({
                    type: 'saml-configuration',
                    id: '1',
                    attributes: this.config
                });
                this.config = response.data.attributes;
                this.originalConfig = JSON.parse(JSON.stringify(this.config));
                this.$studip.message('success', this.$gettext('SAML configuration saved successfully.'));
            } catch (error) {
                console.error('Error saving SAML configuration:', error);
                this.error = this.$gettext('Failed to save SAML configuration. Please try again.');
                this.$studip.message('error', this.error);
            } finally {
                this.isLoading = false;
            }
        },
        resetForm() {
            this.config = JSON.parse(JSON.stringify(this.originalConfig));
        },
        async loadConfig() {
            this.isLoading = true;
            this.error = null;
            try {
                const response = await this.get({
                    type: 'saml-configuration',
                    id: '1'
                });
                this.config = response.data.attributes;
                this.originalConfig = JSON.parse(JSON.stringify(this.config));
            } catch (error) {
                console.error('Error loading SAML configuration:', error);
                this.error = this.$gettext('Failed to load SAML configuration. Please refresh the page.');
                this.$studip.message('error', this.error);
            } finally {
                this.isLoading = false;
            }
        }
    },
    mounted() {
        this.loadConfig();
    }
};
</script>