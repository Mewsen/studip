<template>
    <form class="default">
        <section>
            <label>
                {{ $gettext('Nachricht bei fehlgeschlagener Anmeldung') }}
                <textarea rows="4" cols="50" v-model="messageText"></textarea>
            </label>
        </section>
        <label>
            <input type="checkbox" v-model="fcfsEnabled" :disabled="hasPrios">
            {{ $gettext('Keine automatische Platzverteilung (Windhund-Verfahren)') }}
            <studip-tooltip-icon v-if="hasPrios"
                                 :text="$gettext('Es existieren bereits Anmeldungen für die automatische Platzverteilung.')">
            </studip-tooltip-icon>
        </label>
        <section v-if="!fcfsAllowed || !fcfsEnabled">
            <label>
                {{ $gettext('Zeitpunkt der automatischen Platzverteilung') }}
                <datetimepicker v-if="loaded" :value="distributionTime" v-model="distributionTime"></datetimepicker>
            </label>
        </section>
    </form>
</template>

<script>
import { AdmissionRuleMixin } from '../../mixins/AdmissionRuleMixin';
import Datetimepicker from '../Datetimepicker.vue';
import StudipTooltipIcon from '../StudipTooltipIcon.vue';

export default {
    name: 'ParticipantRestrictedAdmission',
    components: { StudipTooltipIcon, Datetimepicker },
    mixins: [AdmissionRuleMixin],
    props: {
        distribution: {
            type: Number,
            default: Math.floor(new Date().getTime() / 1000 + 86400)
        },
        fcfs: {
            type: Boolean,
            default: true
        },
        hasPrios: {
            type: Boolean,
            default: false
        },
        message: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            messageText: this.message,
            fcfsAllowed: true,
            fcfsEnabled: this.distributionTime === 0,
            distributionTime: this.distribution,
            loaded: false
        }
    },
    computed: {
        payload() {
            return {
                type: 'ParticipantRestrictedAdmission',
                payload: {
                    'distribution-time': this.fcfsEnabled ? 0 : this.distributionTime,
                    'fcfs': this.fcfsEnabled,
                    'fcfs-allowed': this.fcfsAllowed,
                    message: this.messageText
                }
            }
        }
    },
    methods: {
        setRuleData(data) {
            this.fcfsAllowed = data.attributes.payload['fcfs-allowed'];
            this.distributionTime = data.attributes.payload['distribution-time'] !== 0
                ? data.attributes.payload['distribution-time']
                : Math.floor(Date.now() / 1000 + 7 * 86400);
            this.fcfsEnabled = data.attributes.payload['distribution-time'] === 0;
            this.loaded = true;
        }
    },
    created() {
        if (!this.id) {
            this.distributionTime = Math.floor(new Date().getTime() / 1000 + 86400);
            this.loaded = true;
        }
    }
}
</script>
