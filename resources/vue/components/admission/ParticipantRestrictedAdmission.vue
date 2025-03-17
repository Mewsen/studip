 <template>
    <form class="default">
        <section>
            <label>
                {{ $gettext('Nachricht bei fehlgeschlagener Anmeldung') }}
                <textarea rows="4" cols="50" v-model="messageText"></textarea>
            </label>
        </section>
        <label v-if="fcfsAllowed">
            <input type="checkbox" v-model="fcfsEnabled" :disabled="hasPrios">
            {{ $gettext('Keine automatische Platzverteilung (Windhund-Verfahren)') }}
            <studip-tooltip-icon v-if="hasPrios"
                                 :text="$gettext('Es existieren bereits Anmeldungen für die automatische Platzverteilung.')">
            </studip-tooltip-icon>
        </label>
        <section v-if="!fcfsAllowed || !fcfsEnabled">
            <label>
                {{ $gettext('Zeitpunkt der automatischen Platzverteilung') }}
                <datetimepicker v-model="distributionTime"></datetimepicker>
            </label>
        </section>
    </form>
</template>

<script>
import { AdmissionRuleMixin } from '../../mixins/AdmissionRuleMixin';
import datetimepicker from '../Datetimepicker.vue';
import StudipTooltipIcon from '../StudipTooltipIcon.vue';

export default {
    name: 'ParticipantRestrictedAdmission',
    components: { StudipTooltipIcon, datetimepicker },
    mixins: [AdmissionRuleMixin],
    props: {
        distribution: {
            type: Number,
            default: 0
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
            fcfsAllowed: STUDIP.config.ENABLE_COURSESET_FCFS,
            fcfsEnabled: this.distributionTime === 0,
            distributionTime: this.distribution !== 0 ? this.distribution : Math.floor(Date.now() / 1000 + 7 * 86400)
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
            this.distributionTime = data.attributes.payload['distribution-time'] !== 0
                ? data.attributes.payload['distribution-time']
                : Math.floor(Date.now() / 1000 + 7 * 86400);
            this.fcfsEnabled = data.attributes.payload['distribution-time'] === 0;
        },
        validate() {
            // Earliest possible date for seat distribution is 2 hours from now.
            const earliest = new Date();
            earliest.setHours( earliest.getHours() + 2);

            if (!this.fcfsEnabled && this.distributionTime <= Math.floor(earliest.getTime() / 1000)) {
                this.invalidData.push(
                    this.$gettext(
                        'Geben Sie für die Platzverteilung ein Datum an, das weiter in der Zukunft liegt. ' +
                        'Das frühestmögliche Datum ist %{earliest}.',
                        {earliest: earliest.toLocaleString('de-de')}
                    )
                );
            }

            return this.invalidData.length === 0;
        }
    }
}
</script>
