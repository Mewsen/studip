<template>
    <studip-dialog v-if="component !== null"
                   :title="$gettext('Anmelderegel bearbeiten')"
                   :close-text="$gettext('Abbrechen')"
                   @close="cancel"
                   width="900"
                   height="600">
        <template v-slot:dialogContent>
            <studip-message-box v-if="invalidData?.length"
                                type="error"
                                :details="invalidData"
                                :hide-close="true"
                                :hide-details="false"
                                :aria-description="errorText"
                                role="alert">
                {{ $gettext('Es sind ungültige Daten angegeben worden:') }}
            </studip-message-box>
            <component :is="component" v-bind="props" @submit="submit" @error="error"></component>
        </template>
        <template v-slot:dialogButtons>
            <button type="button"
                    class="button accept"
                    @click="requireData">
                {{ $gettext('Übernehmen') }}
            </button>
        </template>
    </studip-dialog>
</template>

<script>
export default {
    name: 'AdmissionRuleConfig',
    props: {
        type: {
            type: String,
            required: true
        },
        rule: {
            type: Object,
            default: null
        },
        assignedRuleTypes: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            component: null,
            theRule: this.rule,
            props: null,
            invalidData: null
        }
    },
    computed: {
        errorText() {
            return this.$gettext('Es sind ungültige Daten angegeben worden:') + this.invalidData?.join(',');
        }
    },
    methods: {
        requireData() {
            STUDIP.eventBus.emit('getRuleConfiguration');
        },
        cancel() {
            this.component = null;
            this.$emit('cancel');
        },
        submit(data) {
            this.component = null;
            this.$emit('submit', data);
        },
        error(message) {
            this.invalidData = message;
        }
    },
    created() {
        const file = STUDIP.Admission.availableRules[this.type];
        let components = {};
        import(`@/vue/components/admission/${file}`).then((module) => {
            this.component = module.default;
            this.props = {
                id: this.theRule?.id,
                ruleData: this.theRule,
                assignedRuleTypes: this.assignedRuleTypes
            };
        });
    }
}
</script>
