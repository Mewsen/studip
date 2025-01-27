<template>
    <form class="default">
        <section>
            <label>
                {{ $gettext('Nachricht bei fehlgeschlagener Anmeldung') }}
                <textarea name="message" rows="4" cols="50" v-model="messageText"></textarea>
            </label>
        </section>
    </form>
</template>

<script>
import { AdmissionRuleMixin } from '../../mixins/AdmissionRuleMixin';

export default {
    name: 'LockedAdmission',
    mixins: [AdmissionRuleMixin],
    props: {
        message: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            messageText: this.message || this.$gettext('Die Anmeldung ist gesperrt.'),
            password1: '',
            password2: ''
        }
    },
    computed: {
        payload() {
            return {
                type: 'LockedAdmission',
                payload: {
                    message: this.messageText
                }
            }
        }
    }
}
</script>
