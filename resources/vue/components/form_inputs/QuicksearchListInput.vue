<template>
    <div>
        <quicksearch :searchtype="searchtype"
                     :autocomplete="autocomplete"
                     @update:model-value="addElement"></quicksearch>
        <table v-if="elements.length > 0" ref="results" class="default">
            <tbody>
                <tr v-for="(element, index) in elements"
                    :key="element.id">
                    <td>
                        {{ element.name }}
                    </td>
                    <td class="actions">
                        <a @click="removeElement(index)"
                           :title="$gettext('Dieses Element entfernen')">
                            <studip-icon shape="trash"></studip-icon>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
        <input type="hidden"
               :name="name"
               :value="realValue"
        >
    </div>
</template>

<script>
import quicksearch from '../Quicksearch.vue';

export default {
    name: 'QuicksearchList',
    components: [ quicksearch ],
    props: {
        name: {
            type: String,
            required: true
        },
        value: {
            type: String,
            default: ''
        },
        searchtype: {
            type: String,
            required: true
        },
        autocomplete: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            elements: [],
        }
    },
    computed: {
        realValue() {
            this.$emit('input', JSON.stringify(this.elements));
            return JSON.stringify(this.elements);
        }
    },
    methods: {
        addElement(id, name) {
            if (!this.elements.map(e => e.id).includes(id)) {
                const element = {
                    id: id,
                    name: name
                };
                this.elements.push(element);
            }
        },
        removeElement(index) {
            this.elements.splice(index, 1);
        }
    },
    created() {
        if (this.value !== '') {
            this.elements = JSON.parse(this.value);
        }
    }
}
</script>

<style lang="scss" scoped>
table.default {
    margin-bottom: unset;
    margin-top: 15px;
    width: 50%;

    .actions {
        text-align: right;
    }
}

</style>
