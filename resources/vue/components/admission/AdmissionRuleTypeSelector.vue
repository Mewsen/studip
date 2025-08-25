<template>
    <studip-dialog @close="closeDialog"
                   width="900"
                   height="600"
                   :close-text="$gettext('Abbrechen')"
                   :title="$gettext('Anmelderegel konfigurieren')">
        <template v-slot:dialogContent>
            <form v-if="!loading"
                  class="default">
                <fieldset class="select_terms_of_use">
                    <legend>
                        {{ $gettext('Typ der Anmelderegel auswählen') }}
                    </legend>
                    <template v-for="type in ruleTypes">
                        <input v-if="isAvailable(type.attributes.type)"
                               type="radio"
                               name="selectedType"
                               v-model="selectedType"
                               :value="type.attributes.type"
                               :id="'rule-type-' + type.attributes.type"
                               :key="type.attributes.type + '-input'">
                        <label v-if="isAvailable(type.attributes.type)"
                               :for="'rule-type-' + type.attributes.type"
                               :key="type.attributes.type + '-label'">
                            <studip-icon :shape="type.attributes.type === selectedType
                                            ? 'radiobutton-checked'
                                            : 'radiobutton-unchecked'"
                                         :size="24"
                            ></studip-icon>
                            <div class="text">
                                {{ type.attributes.name }}
                            </div>
                        </label>
                        <div v-if="isAvailable(type.attributes.type)"
                             class="terms_of_use_description"
                             :key="type.id + '-description'">
                            {{ type.attributes.description }}
                        </div>
                        <div v-if="!isAvailable(type.attributes.type)"
                           :key="type.id + '-incompatible'"
                           class="admission-rule-incompatible">
                            <studip-icon shape="remove-circle"
                                         :size="24"
                                         role="inactive"
                            ></studip-icon>
                            {{ type.attributes.name }}
                            ({{ $gettext('nicht mit bereits vorhandenen Regeln kompatibel') }})
                        </div>
                    </template>
                </fieldset>
            </form>
            <studip-progress-indicator v-if="loading"
                                       :size="32"
                                       :description="$gettext('Verfügbare Anmelderegeln werden geladen')"
            ></studip-progress-indicator>
        </template>
        <template v-slot:dialogButtons>
            <button type="button"
                    class="button"
                    @click.prevent="configureRule"
                    :disabled="selectedType === null">
                {{ $gettext('Ausgewählte Regel konfigurieren') }}
            </button>
        </template>
    </studip-dialog>
</template>

<script>
import StudipProgressIndicator from '../StudipProgressIndicator.vue';
import StudipDialog from '../StudipDialog.vue';

export default {
    name: 'AdmissionRuleTypeSelector',
    emits: ['close', 'configureRule'],
    components: { StudipProgressIndicator, StudipDialog },
    props: {
        assignedRuleTypes: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            loading: true,
            ruleTypes: [],
            selectedType: null,
            compatibility: {}
        }
    },
    methods: {
        closeDialog() {
            this.$emit('close');
        },
        configureRule() {
            this.$emit('configureRule', this.selectedType);
        },
        isAvailable(ruleType) {
            return this.assignedRuleTypes.every(t => this.compatibility[ruleType]?.includes(t));
        }
    },
    created() {
        Promise.all([
            STUDIP.jsonapi.withPromises().get('admission-rules'),
            STUDIP.jsonapi.withPromises().get('admission/rule-compatibility')
        ]).then(values => {
            this.loading = false;
            this.ruleTypes = values[0].data;
            this.compatibility = values[1];
            this.ruleTypes.forEach(t => {
                this.isAvailable(t.attributes.type);
            });
        });
    }
}
</script>
