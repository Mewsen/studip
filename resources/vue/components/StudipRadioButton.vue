<script setup lang="ts">
import {computed} from "vue";

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean],
        required: true,
    },
    name: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number, Boolean],
        required: true
    }
});

const emit = defineEmits(['update:modelValue']);

const model = computed({
    get() {
        return props.modelValue;
    },
    set(newValue) {
        emit('update:modelValue', newValue);
    }
})


const shape = computed(() => {
    return model.value === props.value ? 'radiobutton-checked' : 'radiobutton-unchecked';
})
</script>

<template>
    <label class="as-link studip-radiobutton">
        <input type="radio"
               v-model="model"
               :value="value"
               :name="name"
               class="sr-only"
        >

        <StudipIcon :shape="shape" aria-hidden="true"></StudipIcon>

        <slot>
            No content provided for slot
        </slot>
    </label>
</template>

<style lang="scss">
.studip-radiobutton {
    display: block;

    .studip-icon {
        vertical-align: text-bottom;
    }
}
</style>
