<script setup>
import {nextTick, ref, useTemplateRef, watch} from "vue";
import useDetectOutsideClick from "../composables/useDetectOutsideClick";
import StudipIcon from "./StudipIcon.vue";

defineProps({
    title: {
        type: String
    },
    withCloseButton: {
        type: Boolean,
        default: true
    }
});

const isOpen = defineModel({ default: false });
const dropdownStyle = ref({});
const dropdown = useTemplateRef('dropdown');
const dropdownContent = useTemplateRef('dropdownContent');

useDetectOutsideClick(dropdown, () => isOpen.value = false);

watch(isOpen, async (open) => {
    if (open) {
        await nextTick();

        const trigger = dropdown.value?.getBoundingClientRect();
        const content = dropdownContent.value?.getBoundingClientRect();

        dropdownStyle.value = {
            ...(content.width > trigger.left ? {left: '0'} : {right: '0'})
        };
    }
});
</script>

<template>
    <div
        v-bind="$attrs"
        ref="dropdown"
        class="dropdown"
        aria-haspopup="true"
        :aria-expanded="isOpen.toString()"
    >
        <slot name="trigger">
        </slot>

        <Transition name="fade-down">
            <div
                v-if="isOpen"
                ref="dropdownContent"
                class="dropdown__content"
                :style="dropdownStyle"
                aria-labelledby="dropdown-title"
            >
                <button
                    v-if="withCloseButton"
                    @click="isOpen = false"
                    class="dropdown__close-button">
                    <StudipIcon shape="decline" :size="20" />
                </button>
                <div v-if="title" class="dropdown__header">
                    <p id="dropdown-title" class="dropdown__title">
                        {{ title }}
                    </p>
                </div>
                <ul class="dropdown__items" role="menu">
                    <slot name="items">
                    </slot>
                </ul>
                <slot name="content">
                </slot>
            </div>
        </Transition>
    </div>
</template>
