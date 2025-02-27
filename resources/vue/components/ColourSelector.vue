<template>
    <table class="colour-selector">
        <tbody>
            <tr ref="row">
                <td v-for="colour in colours" :key="`colour-${colour.id}`"
                    class="colour colour-selector"
                >
                    <input type="radio"
                           :name="inputName"
                           :id="`colour-${colour.id}`"
                           :value="colour.id"
                           v-model="selectedColor"
                           :aria-label="colour.label ?? null"
                    >
                    <label :for="`colour-${colour.id}`"
                           :class="colour.class ?? null"
                           :style="{backgroundColor: colour.colour ?? null}">
                    </label>
                </td>
            </tr>
        </tbody>
    </table>
</template>
<script setup>
import {onMounted, ref} from "vue";

const selectedColor = defineModel({
    type: Number,
    default: () => null,
});

const row = ref(null);

const props = defineProps({
    autofocus: {
        type: Boolean,
        default: false,
    },
    colours: {
        type: Array,
        required: true
    },
    inputName: {
        type: String,
        default: 'colour_id'
    },
});

if (props.autofocus) {
    onMounted(() => {
        row.value.querySelector('input[type="radio"]:checked')?.focus();
    });
}
</script>
