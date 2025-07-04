<script setup>
import {useTemplateRef} from "vue";
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
})

const isOpen = defineModel({ default: false });

const dropdown = useTemplateRef('dropdown');
useDetectOutsideClick(dropdown, () => isOpen.value = false)
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
            <div v-if="isOpen" class="dropdown__content" aria-labelledby="dropdown-title">
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
