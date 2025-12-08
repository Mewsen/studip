<script setup>
import {computed, onMounted, reactive, useTemplateRef} from 'vue';
import {$gettext} from '../../../../assets/javascripts/lib/gettext';
import StudipWysiwyg from "../../StudipWysiwyg.vue";
import StudipTooltipIcon from "../../StudipTooltipIcon.vue";

const CSRF = STUDIP.CSRF_TOKEN;

const props = defineProps({
    registration: {
        type: Object,
        default: () => ({})
    },
    role: {
        type: String,
        default: 'tool'
    }
});

const form = reactive({
    version: '1.3a',
    key_type: 'jwk_keyset',
    launch_container: 0,
    ...props.registration
});

const formActionURL = computed(() => {
    if (props.registration.id) {
        return STUDIP.URLHelper.getURL(`dispatch.php/lti/registrations/update/${props.registration.id}`);
    }

    return STUDIP.URLHelper.getURL(`dispatch.php/lti/registrations/store`);
});

const nameInput = useTemplateRef('name-input');

onMounted(() => {
    nameInput.value.focus();
});
</script>

<template>
    <form
        class="default"
        :action="formActionURL"
        method="post"
        v-bind="{...$attrs}"
    >
        <input type="hidden" :name="CSRF.name" :value="CSRF.value">
        <fieldset>
            <legend>
                {{ $gettext('Grunddaten') }}
            </legend>

            <label class="studiprequired m-0">
                <span class="textlabel">{{ $gettext('Name') }}</span>
                <span :title="$gettext('Name ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                <input
                    required
                    type="text"
                    name="name"
                    ref="name-input"
                    v-model="form.name"
                    class="max-w-full" />
            </label>

            <label>
                {{ $gettext('Beschreibung') }}
                <StudipWysiwyg :required="true" v-model="form.description" name="description" />
            </label>

            <label>
                {{ $gettext('Datenschutzhinweise') }}
                <StudipTooltipIcon
                    :text="$gettext('Bitte machen Sie Angaben zu dem angebundenen Werkzeug, soweit sie ihnen bekannt sind. Wie ist der Name, wer bietet es an, wozu wird es eingesetzt und welche Daten werden übertragen? (Beispiel: „Tool XY wird zur Durchführung von Sprachtests genutzt und Testergebnisse und ggf. Noten werden gespeichert. Zur Anmeldung werden Name und Nutzerkennung übertragen.“)')"
                />
                <StudipWysiwyg
                    :required="true"
                    v-model="form.data_protection_notes"
                    name="data_protection_notes"
                />
            </label>

            <label>
                {{ $gettext('URL zu den Nutzungsbedingungen') }}
                <input type="url" name="terms_of_use_url" v-model="form.terms_of_use_url" />
            </label>

            <label>
                {{ $gettext('URL zur Datenschutzerklärung') }}
                <input type="url" name="privacy_policy_url" v-model="form.privacy_policy_url" />
            </label>
        </fieldset>
        <fieldset v-if="role === 'tool'">
            <legend>
                {{ $gettext('Konfiguration des LTI-Tools') }}
            </legend>

            <label class="studiprequired">
                <span class="textlabel">{{ $gettext('Version') }}</span>
                <span :title="$gettext('Version ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                <select name="version" v-model="form.version">
                    <option value="1.1">{{ $gettext('1.0/1.1') }}</option>
                    <option value="1.3a">{{ $gettext('1.3a') }}</option>
                </select>
            </label>

            <label class="studiprequired">
                <span class="textlabel">{{ $gettext('Lunch URL') }}</span>
                <span :title="$gettext('Lunch URL ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                <input required type="url" name="launch_url" v-model="form.launch_url" />
            </label>

            <template v-if="form.version === '1.3a'">
                <label>
                    {{ $gettext('Initiate login URL') }}
                    <StudipTooltipIcon
                        :text="$gettext('Die URL, mit der der Login via OpenID Connect stattfindet.')"
                    />
                    <input type="url" name="auth_init_url" v-model="form.auth_init_url" />
                </label>

                <label>
                    {{ $gettext('Deep-linking URL') }}
                    <input type="url" name="deep_linking_url" v-model="form.deep_linking_url" />
                </label>

                <label class="studiprequired">
                    <span class="textlabel">{{ $gettext('Schlüssel-Typ') }}</span>
                    <span :title="$gettext('Schlüssel-Typ ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                    <select name="key_type" v-model="form.key_type">
                        <option value="rsa_key">{{ $gettext('RSA key') }}</option>
                        <option value="jwk_keyset">{{ $gettext('Keyset URL') }}</option>
                    </select>
                </label>

                <template v-if="form.key_type === 'jwk_keyset'">
                    <label>
                        {{ $gettext('JWKS-URL') }}
                        <StudipTooltipIcon
                            :text="$gettext('Die URL, mit der der der Austausch von JSON web keys stattfinden kann.')"
                        />
                        <input required type="url" name="jwks_url" v-model="form.jwks_url" />
                    </label>
                    <label>
                        {{ $gettext('Schlüssel-ID') }}
                        <StudipTooltipIcon
                            :text="$gettext('Die ID des Schlüssels, der über die JWKS-URL geladen werden soll.')"
                        />
                        <input type="url" name="jwks_key_id" v-model="form.jwks_key_id" />
                    </label>
                </template>
                <label v-else>
                    {{ $gettext('Öffentlicher Schlüssel') }}
                    <textarea required name="public_key" v-model="form.public_key"></textarea>
                </label>
            </template>

            <template v-if="form.version === '1.1'">
                <label class="studiprequired">
                    <span class="textlabel">{{ $gettext('Consumer-Key') }}</span>
                    <span :title="$gettext('Consumer-Key ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                    <input required type="text" name="consumer_key" v-model="form.consumer_key" />
                </label>

                <label class="studiprequired">
                    <span class="textlabel">{{ $gettext('Consumer-Secret') }}</span>
                    <span :title="$gettext('Consumer-Secret ist ein Pflichtfeld')" aria-hidden="true" class="asterisk">*</span>
                    <input required type="text" name="consumer_secret" v-model="form.consumer_secret" />
                </label>
            </template>

            <label>
                <input type="checkbox" name="send_lis_person" v-model="form.send_lis_person" />
                {{ $gettext('Personendaten an das LTI-Tool senden') }}
                <StudipTooltipIcon
                    :text="$gettext('Personendaten dürfen nur an das externe Tool gesendet werden, wenn es keine Datenschutzbedenken gibt. Mit Setzen des Hakens bestätigen Sie, dass die Übermittlung der Daten zulässig ist.')"
                />
            </label>

            <label>
                {{ $gettext('Zusätzliche LTI-Parameter') }}
                <StudipTooltipIcon
                    :text="$gettext('Ein Wert pro Zeile, Beispiel: Review:Chapter=1.2.56')"
                />
                <textarea name="custom_parameters" v-model="form.custom_parameters"></textarea>
            </label>
        </fieldset>
        <fieldset v-if="role === 'tool'">
            <legend>{{ $gettext('Anzeigeeinstellungen') }}</legend>
            <label>
                <span>{{ $gettext('Default launch container') }}</span>
                <select name="launch_container" v-model="form.launch_container">
                    <option value="0">{{ $gettext('Neues Fenster') }}</option>
                    <option value="1">{{ $gettext('Anzeige im IFRAME auf der Seite') }}</option>
                </select>
            </label>
        </fieldset>

        <fieldset v-if="role === 'platform'">
            <legend>
                {{ $gettext('Konfiguration des LTI-Platforms') }}
            </legend>
        </fieldset>

        <footer data-dialog-button>
            <button class="button accept">
                {{ $gettext('Speichern') }}
            </button>
            <button class="button cancel" type="button" data-dialog-close>
                {{ $gettext('Abbrechen') }}
            </button>
        </footer>
    </form>
</template>
