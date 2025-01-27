export const AdmissionRuleMixin = {
    props: {
        id: {
            type: String,
            default: ''
        },
        ruleData: {
            type: Object,
            default: null
        },
        assignedRuleTypes: {
            type: Array,
            default: () => []
        },
        message: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            theRuleData: this.ruleData,
            invalidData: []
        }
    },
    methods: {
        loadRuleData() {
            STUDIP.jsonapi.withPromises().get('admission-rules/' + this.id)
                .then((response) => {
                    this.setRuleData(response.data);
                });
        },
        validate() {
            return true;
        },
        submit() {
            this.invalidData = [];
            if (this.validate()) {
                this.$emit('submit', this.payload);
            } else {
                this.$emit('error', this.invalidData);
            }
        }
    },
    mounted() {
        if (this.id && this.id !== '' && !this.ruleData) {
            this.loadRuleData();
        }

        if (this.ruleData) {
            this.setRuleData(this.ruleData);
        }

        STUDIP.eventBus.on('getRuleConfiguration', () => {
            this.submit();
        });
    },
    beforeDestroy() {
        STUDIP.eventBus.off('getRuleConfiguration');
    }
}
