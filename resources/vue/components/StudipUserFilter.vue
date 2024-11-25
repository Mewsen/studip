<template>
    <studip-dialog :title="$gettext('Bedingung hinzufügen')"
                   height="600"
                   width="900"
                   :confirmText="$gettext('Übernehmen')"
                   confirmClass="button accept"
                   @confirm="submit"
                   :closeText="$gettext('Abbrechen')"
                   closeClass="button cancel"
                   @close="close">
        <template v-slot:dialogContent>
            <section v-for="(element, index) in currentFilter"
                     :key="index">
                <p v-if="index >= 1">
                    {{ $gettext('und') }}
                </p>
                <select v-if="availableFields.length > 0"
                        v-model="element.attributes.type"
                        @change="addFieldConfig(element.attributes.type, index)"
                        :aria-label="$gettext('Feldname')">
                    <option v-for="(field, fIndex) in availableFields"
                            :key="fIndex"
                            :value="field.attributes.type">
                        {{ field.attributes.name }}
                    </option>
                </select>
                <select v-if="hasMultipleCompareOps"
                        v-model="element.attributes['compare-operator']"
                        :aria-label="$gettext('Vergleichsoperator')">
                    <option v-for="(name, op) in fieldConfig[element.attributes.type]?.compareOps"
                            :key="op"
                            :value="op">
                        {{ name }}
                    </option>
                </select>
                <select v-if="hasMultipleValues"
                        v-model="element.attributes.value"
                        :aria-label="$gettext('Wert')">
                    <option v-for="(name, value) in fieldConfig[element.attributes.type]?.values"
                            :key="value"
                            :value="value">
                        {{ name }}
                    </option>
                </select>
                <studip-icon v-if="element.attributes.type && currentFilter.length > 1"
                             shape="trash"
                             role="button"
                             :title="$gettext('Dieses Feld löschen')"
                             @click="removeField(index)"></studip-icon>
            </section>
            <section>
                <button class="button add"
                        @click.prevent="addField">
                    {{ $gettext('Feld hinzufügen') }}
                </button>
            </section>
        </template>
    </studip-dialog>
</template>

<script>
export default {
    name: 'StudipUserFilter',
    props: {
        filter: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            availableFields: [],
            currentFilter: this.filter,
            fieldConfig: {}
        }
    },
    methods: {
        addFieldConfig(type, fieldIndex) {
            if (type !== '') {
                if (!this.fieldConfig[type]) {
                    for (let i = 0; i < this.availableFields.length; i++) {
                        if (this.availableFields[i].attributes.type === type) {
                            this.fieldConfig[type] = {
                                typeparam: this.availableFields[i].attributes['typeparam'],
                                compareOps: this.availableFields[i].attributes['valid-compare-operators'],
                                values: this.availableFields[i].attributes['valid-values']
                            };
                        }
                    }
                }
                this.currentFilter[fieldIndex].attributes.type = type;
                this.currentFilter[fieldIndex].attributes.typeparam = this.fieldConfig[type].typeparam;
                this.currentFilter[fieldIndex].attributes['compare-operator'] = Object.keys(this.fieldConfig[type].compareOps)[0];
                this.currentFilter[fieldIndex].attributes.value = Object.keys(this.fieldConfig[type].values)[0];
            }
        },
        addField() {
            this.currentFilter.push({attributes: { type: null, typeparam: null, 'compare-operator': '', value: '' }});
            this.addFieldConfig(this.availableFields[0].attributes.type, this.currentFilter.length - 1);
        },
        removeField(index) {
            this.currentFilter.splice(index, 1);
        },
        submit() {
            this.$emit('submit', this.currentFilter)
        },
        close() {
            this.$emit('close');
        },
        hasMultipleCompareOps(element) {
            return element.attributes.type
                && element.attributes.type !== ''
                && Object.keys(this.fieldConfig[element.attributes.type]?.compareOps).length > 1;
        },
        hasMultipleValues(element) {
            return element.attributes.type
                && element.attributes.type !== ''
                && Object.keys(this.fieldConfig[element.attributes.type]?.values).length > 1;
        }
    },
    created() {
        STUDIP.jsonapi.withPromises().get('user-filter-fields').then(response => {
            this.availableFields = response.data;
            this.addField();
        });
    }
}
</script>
