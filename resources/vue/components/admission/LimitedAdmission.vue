<template>
    <form class="default">
        <section>
            <label for="terms">
                {{ $gettext('Nachricht bei fehlgeschlagener Anmeldung') }}
                <textarea rows="4" cols="50" v-model="messageText"></textarea>
            </label>
        </section>
        <validity-time v-model:start="startTime"
                       v-model:end="endTime"
        />
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
            max: this.maxNumber,
            startTime: 0,
            endTime: 0
        }
    },
    computed: {
        payload() {
            return {
                type: 'LimitedAdmission',
                payload: {
                    maxnumber: this.max,
                    message: this.messageText,
                    'start-time': this.startTime === 0 ? null : this.startTime,
                    'end-time': this.endTime === 0 ? null: this.endTime
                }
            }
        }
    },
    methods: {
        setRuleData(data) {
            this.messageText = data.attributes.payload['message'];
            this.max = data.attributes.payload['maxnumber'];
            this.startTime = parseInt(data.attributes.payload['start-time'], 10);
            this.endTime = parseInt(data.attributes.payload['end-time'], 10);
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
