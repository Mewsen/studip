<template>
    <component
        :is="tag"
        v-if="isAction"
        :href="href"
        class="context-menu__entry context-menu__entry--interactive"
        :role="tag === 'button' ? 'menuitem' : undefined"
        @click="$emit('click', $event)"
    >
        <div class="context-menu__entry-content">
            <div v-if="hasLeftAddon" class="context-menu__entry-left">
                <slot name="left">
                    <studip-icon v-if="icon" :shape="icon" size="20" />
                </slot>
            </div>
            <div class="context-menu__entry-texts">
                <div v-if="label" class="context-menu__entry-label">{{ label }}</div>
                <div v-if="description" class="context-menu__entry-description">{{ description }}</div>
            </div>
            <div v-if="$slots.right" class="context-menu__entry-right">
                <slot name="right" />
            </div>
        </div>
    </component>

    <label v-else class="context-menu__entry context-menu__entry--form">
        <div class="context-menu__entry-content">
            <div v-if="hasLeftAddon" class="context-menu__entry-left">
                <slot name="left">
                    <studip-icon v-if="icon" :shape="icon" size="20" />
                </slot>
            </div>
            <div class="context-menu__entry-texts">
                <slot>
                    <div class="context-menu__entry-label">{{ label }}</div>
                </slot>
            </div>
            <div v-if="$slots.right" class="context-menu__entry-right">
                <slot name="right" />
            </div>
        </div>
    </label>
</template>

<script setup>
import { computed, useSlots } from 'vue';

const props = defineProps({
    label: String,
    description: String,
    icon: String,
    href: String,
    isClickable: { type: Boolean, default: false }
});

const emit = defineEmits(['click']);
const slots = useSlots();

const isAction = computed(() => props.isClickable || !!props.href);
const tag = computed(() => (props.href ? 'a' : 'button'));
const hasLeftAddon = computed(() => props.icon || !!slots.left);
</script>

<style scoped lang="scss">
.context-menu__entry {
    display: block;
    width: 100%;
    box-sizing: border-box;
    padding: 0.75rem 1rem;
    border: none;
    background: transparent;
    text-align: left;
    font: inherit;
    color: inherit;
    text-decoration: none;

    &--interactive {
        cursor: pointer;
        &:focus { outline: none; }
        
        &:hover, &:focus-visible {
            background-color: var(--color--gray-lightest, #f0f0f0);
            
        }
    }

    &--form {
        cursor: default;
    }

    .context-menu__entry-content {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .context-menu__entry-texts {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .context-menu__entry-label {
        font-weight: 600;
        color: var(--color--black);
    }

    .context-menu__entry-description {
        font-size: 0.85rem;
        color: var(--color--gray);
        font-weight: normal;
    }
}
</style>