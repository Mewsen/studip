<template>
    <form class="default">
        <section>
            <label for="terms">
                <span class="required">
                    {{ $gettext('Teilnahmebedingungen') }}
                </span>
                <textarea v-model="theTerms" id="terms" rows="4"
                          :placeholder="$gettext('Formulieren Sie hier die Teilnahmebedingungen.')"></textarea>
            </label>
        </section>
    </form>
</template>

<script>
import { AdmissionRuleMixin } from '../../mixins/AdmissionRuleMixin';

export default {
    name: 'TermsAdmission',
    mixins: [AdmissionRuleMixin],
    props: {
        terms: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            messageText: this.message || this.$gettext('Sie müssen den Teilnahmebedingungen zustimmen.'),
            theTerms: this.terms
        }
    },
    computed: {
        payload() {
            return {
                type: 'TermsAdmission',
                payload: {
                    terms: this.theTerms
                }
            }
        }
    },
    methods: {
        setRuleData(data) {
            this.theTerms = data.attributes.payload.terms;
        },
        validate() {
            this.invalidData = [];
            if (this.theTerms === '') {
                this.invalidData.push(this.$gettext('Es sind keine Teilnahmebedingungen angegeben.'));
            }

            return this.invalidData.length === 0;
        }
    }
}
</script>
