<template>
    <label class="studip-switch" :class="{ 'is-disabled': disabled }">
        <input
            v-bind="$attrs"
            type="checkbox"
            role="switch"
            class="studip-switch__input"
            :checked="isChecked"
            :disabled="disabled"
            @change="isChecked = $event.target.checked"
        />
        <span class="studip-switch__slider">
            <span class="studip-switch__knob">
                <studip-icon :shape="isChecked ? activeIcon : inactiveIcon" :size="12" :role="null" />
            </span>
        </span>
        <span class="studip-switch__text">{{ label }}</span>
    </label>
</template>

<script setup>
import StudipIcon from '@/vue/components/StudipIcon.vue';

const props = defineProps({
    label: { type: String, required: true },
    disabled: { type: Boolean, default: false },
    activeIcon: { type: String, default: 'accept' },
    inactiveIcon: { type: String, default: 'decline' },
});

const isChecked = defineModel({ default: false });
</script>

<style scoped lang="scss">
.studip-switch {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    user-select: none;
    padding: 4px 0;

    &__input {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;

        &:focus-visible + .studip-switch__slider {
            outline: 2px solid var(--color--focus);
        }
    }

    &__slider {
        --switch-width: 40px;
        --switch-height: 20px;

        position: relative;
        display: flex;
        align-items: center;
        width: var(--switch-width);
        height: var(--switch-height);
        background-color: var(--dark-gray-color-15);
        border-radius: var(--switch-height);
        transition: background-color 0.2s ease-in-out;
        flex-shrink: 0;

        .studip-switch__input:checked + & {
            background-color: var(--green-80);
        }
    }

    &__knob {
        position: absolute;
        left: 2px;
        width: 16px;
        height: 16px;
        background-color: white;
        border-radius: 50%;

        display: flex;
        align-items: center;
        justify-content: center;

        transition: transform 0.2s ease-in-out;

        .studip-switch__input:checked + .studip-switch__slider & {
            transform: translateX(20px);
        }

        :deep(.studip-icon) {
            display: block;
            margin: 0;
            padding: 0;
            line-height: 0;

            img {
                display: block;
            }
        }
    }

    &__text {
        color: var(--color--font-primary);
        font-size: 0.95rem;
    }

    &.is-disabled {
        opacity: 0.5;
        cursor: not-allowed;

        .studip-switch__slider {
            background-color: var(--dark-gray-color-15) !important;
        }
    }
}
</style>
