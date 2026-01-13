<template>
    <div
        ref="groupContainer"
        class="grouped-buttons"
        :class="{ 'is-collapsible': collapsible, 'is-expanded': effectiveExpanded }"
        :role="radiogroup ? 'radiogroup' : 'group'"
        @keydown="handleKeydown"
    >
        <button
            v-if="collapsible"
            class="button"
            :class="{ active: isExpanded }"
            :aria-expanded="isExpanded"
            :aria-controls="contentId"
            @click="isExpanded = !isExpanded"
        >
            {{ currentLabel }}
        </button>

        <div :id="contentId" class="grouped-buttons-content" :aria-hidden="collapsible && !isExpanded">
            <slot></slot>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, onMounted, watch, nextTick } from 'vue';
const props = defineProps({
    collapsible: {
        type: Boolean,
        default: false,
    },
    toggleLabel: {
        type: String,
        default: 'Aktionen',
    },
    activeLabel: {
        type: String,
        default: null,
    },
    radiogroup: {
        type: Boolean,
        default: false,
    },
});
const isExpanded = ref(false);
const effectiveExpanded = computed(() => {
    return props.collapsible ? isExpanded.value : true;
});
const currentLabel = computed(() => {
    return isExpanded.value && props.activeLabel ? props.activeLabel : props.toggleLabel;
});
const groupContainer = ref(null);
const contentId = computed(() => `group-content-${Math.random().toString(36).substr(2, 9)}`);

onMounted(() => {
    nextTick(updateA11yAttributes);

    const observer = new MutationObserver(() => {
        updateA11yAttributes();
    });
    if (groupContainer.value) {
        observer.observe(groupContainer.value, {
            attributes: true,
            subtree: true,
            attributeFilter: ['class'],
        });
    }
});

watch(
    () => isExpanded.value,
    () => {
        if (isExpanded.value) {
            nextTick(updateA11yAttributes);
        }
    }
);

const updateA11yAttributes = () => {
    if (!props.radiogroup || !groupContainer.value) return;

    const buttons = groupContainer.value.querySelectorAll('.grouped-buttons-content .button');

    buttons.forEach((btn, index) => {
        btn.setAttribute('role', 'radio');

        const isActive = btn.classList.contains('active') || btn.classList.contains('is-active');
        btn.setAttribute('aria-checked', isActive ? 'true' : 'false');

        if (isActive) {
            btn.setAttribute('tabindex', '0');
        } else if (![...buttons].some((b) => b.classList.contains('active')) && index === 0) {
            btn.setAttribute('tabindex', '0');
        } else {
            btn.setAttribute('tabindex', '-1');
        }
    });
};

const handleKeydown = (event) => {
    if (!props.radiogroup) return;

    const buttons = Array.from(
        groupContainer.value.querySelectorAll('.grouped-buttons-content .button:not([disabled])')
    );
    if (buttons.length === 0) return;

    const currentIndex = buttons.indexOf(document.activeElement);
    let nextIndex;

    switch (event.key) {
        case 'ArrowRight':
        case 'ArrowDown':
            nextIndex = (currentIndex + 1) % buttons.length;
            event.preventDefault();
            break;
        case 'ArrowLeft':
        case 'ArrowUp':
            nextIndex = (currentIndex - 1 + buttons.length) % buttons.length;
            event.preventDefault();
            break;
        case 'Home':
            nextIndex = 0;
            event.preventDefault();
            break;
        case 'End':
            nextIndex = buttons.length - 1;
            event.preventDefault();
            break;
        default:
            return;
    }

    if (nextIndex !== undefined) {
        buttons.forEach((btn) => btn.setAttribute('tabindex', '-1'));

        const nextBtn = buttons[nextIndex];
        nextBtn.setAttribute('tabindex', '0');
        nextBtn.focus();

        nextBtn.click();
        buttons.forEach((btn) => btn.setAttribute('aria-checked', 'false'));
        nextBtn.setAttribute('aria-checked', 'true');
    }
};
</script>
<style lang="scss">
.grouped-buttons {
    display: inline-flex;
    vertical-align: middle;

    &[role='radiogroup']:has(button:focus-visible) {
        outline: 2px solid var(--color--focus);
        border-radius: var(--border-radius-default);
        z-index: 8;
    }

    &[role='radiogroup'] {
        .button:focus,
        .button:focus-visible {
            outline: none !important;
            box-shadow: none !important;
        }
    }

    > button.button,
    > .grouped-buttons-content > button.button,
    > .context-menu > button.button {
        margin: 0;
        border-radius: 0;
        position: relative;
        z-index: 1;

        &:hover,
        &:active,
        &.is-active {
            z-index: 2;
        }

        &:focus-visible {
            z-index: 8;
            outline: 2px solid var(--color--focus);
        }
        &:focus {
            z-index: 8;
        }
    }

    .button.context-menu__button {
        min-height: 34px;
    }

    .button.icon-only {
        min-width: unset;
        padding: 5px;
        margin: 0;
        width: 34px;
        height: 34px;
        border-radius: 4px;

        .studip-icon {
            vertical-align: middle;
        }
    }

    .button + .button,
    .button + .context-menu,
    .context-menu + .button,
    .context-menu + .context-menu,
    .button + .grouped-buttons-content .button,
    .grouped-buttons-content .button + .button {
        margin-left: -1px !important;
    }

    &.is-collapsible .button + .grouped-buttons-content {
        .button:first-child,
        .context-menu:first-child .context-menu__button {
            margin-left: -1px !important;
        }
    }

    > .button:first-child,
    > .context-menu:first-child .context-menu__button,
    > .grouped-buttons-content:first-child > .button:first-child,
    > .grouped-buttons-content:first-child > .context-menu:first-child .context-menu__button {
        border-top-left-radius: var(--border-radius-default);
        border-bottom-left-radius: var(--border-radius-default);
    }

    &:not(.is-expanded) > .button:first-child {
        border-top-right-radius: var(--border-radius-default);
        border-bottom-right-radius: var(--border-radius-default);
    }

    &.is-expanded,
    &:not(.is-collapsible) {
        .grouped-buttons-content .button:last-child,
        .grouped-buttons-content .context-menu:last-child .context-menu__button,
        > .button:last-child,
        > .context-menu:last-child .context-menu__button {
            border-top-right-radius: var(--border-radius-default);
            border-bottom-right-radius: var(--border-radius-default);
        }
    }

    .context-menu {
        display: inline-flex;
        vertical-align: middle;

        .context-menu__button {
            border-radius: 0;
        }
    }

    .grouped-buttons-content {
        display: flex;
        padding: 2px 0;
        margin: -2px 0;
        visibility: visible;
        max-width: 600px;
        opacity: 1;
        pointer-events: auto;
        transform: translateX(0);
        transition: 
            max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1),
            opacity 0.2s linear,
            transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    &.is-collapsible:not(.is-expanded) {
        .grouped-buttons-content {
            max-width: 0;
            opacity: 0;
            transform: translateX(-10px);
            pointer-events: none;
            visibility: hidden;
            transition: 
                max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                opacity 0.2s linear,
                transform 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                visibility 0s 0.3s;
        }
    }
}
</style>
