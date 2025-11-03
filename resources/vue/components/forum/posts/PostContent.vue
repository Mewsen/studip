<script setup>
import {onDeactivated, onMounted, useTemplateRef, watch} from "vue";

const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    content: {
        type: String,
        default: ''
    },
    modelValue: {
        type: String,
        default: ''
    }
});

const actionsRef = useTemplateRef('actions');

const onTextSelected = event => {
    if (document.getSelection().toString()) {
        emit('update:modelValue', document.getSelection().toString());
        actionsRef.value.style.display = 'inline-flex';
        actionsRef.value.style.top = event.pageY +'px';
        actionsRef.value.style.left = event.pageX+'px';
    }
}

const newSelectionHandler = () => {
    if(!document.getSelection().toString() && actionsRef.value) {
        actionsRef.value.style.display = 'none';
    }
}

const removeSelection = () => {
    actionsRef.value.style.display = 'none';
    document.getSelection().removeAllRanges();
}

defineExpose({
    removeSelection
});

onMounted(() => document.addEventListener('selectionchange', newSelectionHandler));


onDeactivated(() => {
    document.removeEventListener('selectionchange', newSelectionHandler);
});

watch(() => props.modelValue, newValue => {
    if (!newValue) {
        removeSelection();
    }
});
</script>

<template>
    <div @mouseup="onTextSelected" class="with-ballon-action" v-bind="$attrs">
        <div class="text-highlight m-0 post-content" v-html="content"></div>

        <div class="ballon-action" ref="actions">
            <slot name="actions"></slot>
        </div>
    </div>
</template>
