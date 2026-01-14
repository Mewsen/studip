<template>
    <v-select
        multiple
        v-model="selected"
        :options="transformed_options"
        :reduce="(option) => option.id"
        v-bind="$attrs"
    >
        <template v-slot:no-options>
            {{ this.no_options_text }}
        </template>
        <template #open-indicator="{ selectAttributes }">
            <span v-bind="selectAttributes"><studip-icon shape="arr_1down" :size="10"/></span>
        </template>
    </v-select>
</template>

<script>
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css'
import {$gettext} from "../../assets/javascripts/lib/gettext";
import StudipIcon from "./StudipIcon.vue";
export default {
    name: 'multiselect',
    components: {
        StudipIcon,
        vSelect,
    },
    emits: ['update:model-value'],
    inheritAttrs: false,
    props: {
        name: {
            type: String,
            required: false
        },
        modelValue: {
            required: false,
        },
        value: {
            required: false
        },
        options: {
            type: Object,
            required: true
        },
        no_options_text: {
            type: String,
            required: false,
            default: $gettext('Keine Auswahlmöglichkeiten')
        }
    },
    data () {
        return {
            selected: []
        };
    },
    computed: {
        transformed_options () {
            let output = [];
            Object.entries(this.options).forEach(obj => {
                output.push({
                    id: obj[0],
                    label: obj[1]
                });
            });
            return output;
        }
    },
    mounted () {
        this.selected = this.value;
    },
    watch: {
        selected: {
            handler(newValue) {
                this.$emit('update:model-value', newValue);
            },
            deep: true
        }
    }
}
</script>
