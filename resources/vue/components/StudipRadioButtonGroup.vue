<script setup lang="ts">
import {computed, onMounted} from "vue";
import StudipRadioButton from "./StudipRadioButton.vue";

const props = defineProps<{
    label: string;
    modelValue: string | number | boolean;
    name: string;
    options: Record<string, string>
}>();

const emit = defineEmits(['update:modelValue']);

const model = computed({
    get() {
        return props.modelValue;
    },
    set(newValue) {
        emit('update:modelValue', newValue);
    }
})

onMounted(() => {
    document.addEventListener('focusin', event => console.log('focussed', event.target), {capture: true})
})

</script>
<template>
    <div role="radiogroup"
         :aria-label="props.label"
    >
        <StudipRadioButton v-for="(optionLabel, key) in options"
                           :key="key"
                           :value="key"
                           :name="name"
                           v-model="model"
        >
            {{ optionLabel }}
        </StudipRadioButton>

    </div>
</template>
