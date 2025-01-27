<template>
    <form class="default">
        <section>
            <label for="terms">
                {{ $gettext('Nachricht bei fehlgeschlagener Anmeldung') }}
                <textarea rows="4" cols="50" v-model="messageText"></textarea>
            </label>
        </section>
        <validity-time></validity-time>
        <section>
            <label for="maxnumber">
                <span class="required">
                    {{ $gettext('Maximale Anzahl erlaubter Anmeldungen') }}
                </span>
                <input type="number" size="4" min="1" v-model="max">
            </label>
        </section>
    </form>
</template>

<script>
import { AdmissionRuleMixin } from '../../mixins/AdmissionRuleMixin';
import ValidityTime from './ValidityTime.vue';

export default {
    name: 'LimitedAdmission',
    components: { ValidityTime },
    mixins: [AdmissionRuleMixin],
    props: {
        maxNumber: {
            type: Number,
            default: 1
        }
    },
    data() {
        return {
            messageText: this.message || this.$gettext('Sie sind bereits in die maximale Anzahl von %u Veranstaltungen eingetragen.'),
            max: this.maxNumber
        }
    },
    computed: {
        payload() {
            return {
                type: 'LimitedAdmission',
                payload: {
                    maxnumber: this.max,
                    message: this.messageText
                }
            }
        }
    },
    methods: {
        setRuleData(data) {
            this.max = data.attributes.payload['maxnumber'];
        },
        validate() {
            if (this.max < 1) {
                this.invalidData.push(this.$gettext('Bitte geben Sie eine gültige Zahl für die Anzahl der maximalen Anmeldungen an.'));
            }

            return this.invalidData.length === 0;
        }
    }
}
</script>
