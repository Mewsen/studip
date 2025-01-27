<template>
    <form class="default">
        <section>
            <label>
                {{ $gettext('Nachricht bei fehlgeschlagener Anmeldung') }}
                <textarea name="message" rows="4" cols="50" v-model="messageText"></textarea>
            </label>
        </section>
        <section>
            <studip-message-box v-if="passwordSet" type="warning">
                {{ $gettext('Es ist bereits ein Passwort eingerichtet. Um es zu überschreiben, geben Sie hier ein neues ein.') }}
            </studip-message-box>
            <label>
                {{ $gettext('Zugangspasswort') }}
                <input :type="passwordVisible ? 'text' : 'password'" v-model="password1" ref="password1">
                <studip-icon class="password-visibility" @click="togglePasswordVisible"
                             :shape="passwordVisible ? 'visibility-invisible' : 'visibility-visible'"></studip-icon>
            </label>
            <label>
                {{ $gettext('Passwort wiederholen') }}
                <input :type="passwordVisible ? 'text' : 'password'" v-model="password2" ref="password2">
                <studip-icon class="password-visibility" @click="togglePasswordVisible"
                             :shape="passwordVisible ? 'visibility-invisible' : 'visibility-visible'"></studip-icon>
            </label>
        </section>
    </form>
</template>

<script>
import { AdmissionRuleMixin } from '../../mixins/AdmissionRuleMixin';

export default {
    name: 'PasswordAdmission',
    mixins: [AdmissionRuleMixin],
    props: {
        hasPassword: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            messageText: this.message || this.$gettext('Für die Anmeldung ist ein Passwort erforderlich.'),
            password1: '',
            password2: '',
            passwordVisible: false,
            passwordSet: this.hasPassword
        }
    },
    methods: {
        togglePasswordVisible() {
            this.passwordVisible = !this.passwordVisible;
        },
        setRuleData(data) {
            if (data.attributes.payload.password !== '') {
                this.passwordSet = true;
            }
        },
        validate() {
            this.invalidData = [];
            if (this.password1 === '') {
                this.invalidData.push(this.$gettext('Das Passwort darf nicht leer sein.'));
            }
            if (this.password1 !== this.password2) {
                this.invalidData.push(this.$gettext('Die eingegebenen Passwörter stimmen nicht überein.'));
            }

            return this.invalidData.length === 0;
        }
    },
    computed: {
        payload() {
            return {
                type: 'PasswordAdmission',
                payload: {
                    password: this.password1,
                    message: this.messageText
                }
            }
        }
    }
}
</script>
