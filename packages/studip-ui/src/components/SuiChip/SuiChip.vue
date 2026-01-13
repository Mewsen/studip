<template>
    <span class="sui-chip" :class="{ disabled }" :style="{ backgroundColor: backgroundColor }">
        <sui-icon
            v-if="icon"
            :shape="icon"
            :size="14"
            :inline="true"
            role="info_alt"
            class="sui-chip--icon"
        />
        {{ label }}
        <button
            v-if="removable && !disabled"
            type="button"
            class="sui-chip--button-remove"
            @click="$emit('remove')"
            aria-label="Entfernen"
        >
            <sui-icon shape="decline" :size="14" role="info_alt" :inline="true" />
        </button>
    </span>
</template>

<script setup>
import { computed } from 'vue'
import { SuiIcon } from '../index'

const props = defineProps({
    label: {
        type: String,
        required: true,
    },

    removable: {
        type: Boolean,
        default: false,
    },

    disabled: {
        type: Boolean,
        default: false,
    },

    color: {
        type: String,
        default: '',
    },

    hex: {
        type: String,
        default: '',
        validator: (value) => /^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/.test(value),
    },

    icon: {
        type: String,
        default: '',
    },
})

defineEmits(['remove'])

const backgroundColor = computed(() => {
    if (props.hex) {
        return props.hex
    }
    if (props.color) {
        return `var(--color--${props.color})`
    }
    return 'var(--color--font-primary)'
})
</script>

<style scoped>
.sui-chip {
    display: inline-flex;
    align-items: center;
    border-radius: 1rem;
    padding: 0 0.75rem;
    font-size: 0.75rem;
    line-height: 1.25rem;
    margin: 0.25rem;
    color: var(--color--font-inverted);
}

.sui-chip.disabled {
    opacity: 0.5;
    pointer-events: none;
}

.sui-chip--button-remove {
    background: transparent;
    border: none;
    cursor: pointer;
    margin-left: 0.25rem;
    font-size: 1.25rem;
    color: var(--color--white);
}
.sui-chip--icon {
    margin-right: 0.25rem;
}
</style>
