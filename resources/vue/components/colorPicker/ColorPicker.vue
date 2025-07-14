<template>
    <div class="studip-color-picker" ref="wrapper">
        <span v-if="withColor" class="studip-color-picker-value" :style="{ backgroundColor: selectedColor }"></span>
        <button class="button btn-icon--only" @click="togglePicker" :disabled="disabled">
            <StudipIcon shape="group4" />
        </button>

        <div v-if="isOpen" class="color-picker-popup">
            <div class="tabs">
                <button :class="{ active: tab === 'palette' }" @click="tab = 'palette'">
                    {{ $gettext('Palette') }}
                </button>
                <button :class="{ active: tab === 'spectrum' }" @click="tab = 'spectrum'">
                    {{ $gettext('Spektrum') }}
                </button>
            </div>

            <div v-show="tab === 'palette'" class="palette-grid">
                <button
                    v-for="color in colors"
                    :key="color"
                    class="color-swatch"
                    :class="{ selected: selectedColor === color, inverted: color === '#ffffff' }"
                    :style="{ backgroundColor: color }"
                    :aria-label="color"
                    @click="selectedColor = color"
                    @dblclick="selectedColor = color; confirmSelection()"
                />
            </div>

            <div v-show="tab === 'spectrum'" class="spectrum">
                <ChromePicker v-model="selectedColor" :formats="['hex', 'rgb', 'hsl']" :disableAlpha="true" />
            </div>

            <div class="actions">
                <button class="button" @click="confirmSelection">{{ $gettext('Wählen') }}</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onBeforeUnmount } from 'vue';
import 'vue-color/style.css';
import { ChromePicker } from 'vue-color';
import colors from './colorPalette';
import StudipIcon from '../StudipIcon.vue';

const props = defineProps({
    modelValue: { type: String, required: true },
    disabled: { type: Boolean, default: false },
    withColor: { type: Boolean, default: false },
});
const emit = defineEmits(['update:modelValue']);

const wrapper = ref(null);
const isOpen = ref(false);
const tab = ref('palette');

const selectedColor = ref(props.modelValue);

watch(
    () => props.modelValue,
    (val) => {
        if (val !== selectedColor.value) {
            selectedColor.value = val;
        }
    }
);

const confirmSelection = () => {
    emit('update:modelValue', selectedColor.value);
    isOpen.value = false;
};

const togglePicker = () => {
    isOpen.value = !isOpen.value;
};

const handleClickOutside = (event) => {
    if (wrapper.value && !wrapper.value.contains(event.target)) {
        isOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside));
</script>

<style lang="scss">
@import '../../../assets/stylesheets/scss/colorpicker.scss';
</style>
