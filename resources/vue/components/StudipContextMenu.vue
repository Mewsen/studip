<template>
    <div class="context-menu" ref="menuWrapper">
        <button
            type="button"
            class="button context-menu__button"
            :class="buttonClass"
            :title="title"
            :aria-expanded="isOpen.toString()"
            aria-haspopup="true"
            @click="toggle"
            tabindex="0"
        >
            <studip-icon :shape="buttonShape" />
        </button>

        <div
            v-if="isOpen && !menuDrawerEnabeled"
            class="context-menu__panel"
            :class="{ 'align-right': isRightAligned }"
            role="menu"
            :aria-label="title || 'Dropdown-Menü'"
            @keydown="handleKeyDown"
            tabindex="-1"
        >
            <div v-if="title" class="context-menu__menu-title" role="presentation">{{ title }}</div>

            <div class="context-menu__entries">
                <slot name="content" />
            </div>
        </div>

        <transition name="menu-drawer">
            <div v-if="isOpen && menuDrawerEnabeled" class="context-menu__menu-drawer">
                <div class="context-menu__overlay" @click="close"></div>
                <div class="context-menu__menu-drawer-panel" role="menu">
                    <div v-if="title" class="context-menu__menu-title">
                        <span>{{ title }}</span>
                        <button @click="close" class="context-menu__close-drawer">
                            <StudipIcon shape="decline" :size="20" />
                        </button>
                    </div>
                    <div class="context-menu__entries">
                        <slot name="content" />
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, watch } from 'vue';
import StudipIcon from '@/vue/components/StudipIcon.vue';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    buttonClass: {
        type: [String, Object, Array],
        default: '',
    },
    buttonShape: {
        type: String,
        default: 'menu-more',
    },
    responsive: { type: Boolean, default: true },
});

const emit = defineEmits(['select', 'toggle']);

const isOpen = ref(false);
const isRightAligned = ref(false);
const hasAdjusted = ref(false);
const menuWrapper = ref(null);
const menuDrawerEnabeled = ref(false);

function toggle() {
    isOpen.value = !isOpen.value;
}

function close() {
    isOpen.value = false;
}

function adjustPosition() {
    if (!menuWrapper.value) return;
    const menu = menuWrapper.value.querySelector('.context-menu__panel');
    if (!menu) return;

    const rect = menu.getBoundingClientRect();
    const viewportWidth = window.innerWidth || document.documentElement.clientWidth;

    isRightAligned.value = rect.right > viewportWidth;
}

function checkResponsiveClass() {
    if (props.responsive) {
        menuDrawerEnabeled.value = document.documentElement.classList.contains('responsive-display');
    }
}

const getFocusableElements = () => {
    if (!menuWrapper.value) return [];

    const selectors = [
        '.context-menu__entry:is(button, a):not([disabled])',
        '.context-menu__entry input:not([disabled])',
        '.context-menu__entry select:not([disabled])',
    ].join(',');

    const allElements = Array.from(menuWrapper.value.querySelectorAll(selectors));

    return allElements.filter((el) => {
        if (el.type === 'radio' && el.name) {
            const group = allElements.filter((i) => i.name === el.name);
            const checked = group.find((i) => i.checked);
            return checked ? el === checked : el === group[0];
        }
        return true;
    });
};

watch(isOpen, (open) => {
    if (open && !hasAdjusted.value) {
        nextTick(() => {
            requestAnimationFrame(() => {
                adjustPosition();
                hasAdjusted.value = true;

                const elements = getFocusableElements();
                if (elements.length > 0) {
                    elements[0].focus();
                } else {
                    menuWrapper.value?.querySelector('.context-menu__panel')?.focus();
                }
            });
        });
    }
});

function handleClickOutside(event) {
    if (menuWrapper.value && !menuWrapper.value.contains(event.target)) {
        close();
    }
}

function handleKeyDown(event) {
    if (!isOpen.value) return;

    const elements = getFocusableElements();
    if (elements.length === 0) return;

    const firstEl = elements[0];
    const lastEl = elements[elements.length - 1];
    const activeEl = document.activeElement;

    if (event.key === 'Tab') {
        if (event.shiftKey) {
            // Shift + Tab
            if (activeEl === firstEl || activeEl === menuWrapper.value.querySelector('.context-menu__panel')) {
                event.preventDefault();
                lastEl.focus();
            }
        } else {
            // Tab
            if (activeEl === lastEl) {
                event.preventDefault();
                firstEl.focus();
            }
        }
    } else if (event.key === 'Escape' || event.key === 'Esc') {
        event.preventDefault();
        close();
        const button = menuWrapper.value.querySelector('.context-menu__button');
        if (button) button.focus();
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    checkResponsiveClass();
    if (props.responsive) {
        const observer = new MutationObserver(() => checkResponsiveClass());
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['class'],
        });
    }
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

defineExpose({ close, isOpen });
</script>

<style lang="scss">
.context-menu {
    position: relative;
    display: inline-block;

    &__button.button {
        min-width: unset;
        padding: 5px;
        margin: 0;
        width: 34px;
        height: 34px;
        border-radius: 4px;

        .studip-icon {
            vertical-align: bottom;
        }
    }

    &__panel {
        position: absolute;
        top: 100%;
        left: 0;
        margin-top: 0.25rem;
        padding: 0.5rem 0;
        background: white;
        border: 1px solid #ccc;
        box-shadow: 0 4px 6px rgb(0 0 0 / 0.1);
        z-index: 20;
        border-radius: 0.25rem;
        min-width: 270px;
        display: flex;
        flex-direction: column;
        transition: left 0.2s ease;

        &.align-right {
            left: auto;
            right: 0;
        }
    }

    &__entry {
        display: block;
        width: 100%;
        box-sizing: border-box;
        text-decoration: none;
        color: inherit;
        padding: 0;

        &--interactive {
            cursor: pointer;
            &:hover {
                background-color: var(--color--gray-lightest, #f0f0f0);
            }
        }

        button:not(.context-menu__button),
        a:not(.context-menu__button) {
            background: none;
            border: none;
            width: 100%;
            padding: 0.75rem 1rem;
            text-align: left;
            cursor: pointer;
            display: block;
            color: inherit;
            font: inherit;

            &:hover {
                background-color: var(--color--gray-lightest, #f0f0f0);
            }
        }
    }

    &__entry-texts {
        input[type='text'] {
            margin: 4px 0;
            padding: 4px 8px;
            border: 1px solid var(--color--gray-light);
            border-radius: 3px;
            font-size: 0.9rem;

            &:focus {
                outline: 2px solid var(--color--focus);
                border-color: transparent;
            }
        }
    }

    &__section-group {
        padding-bottom: 10px;
        border-bottom: 1px solid #ddd;

        &:last-of-type {
            border-bottom: none;
        }
    }

    &__entry-content {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    &__entry-icon {
        flex-shrink: 0;
        width: 1.25rem;
        height: 1.25rem;
        margin-top: 2px;
        fill: currentColor;
    }

    &__entry-texts {
        display: flex;
        flex-direction: column;
    }

    &__entry-label {
        font-weight: 600;
        font-size: 0.95rem;
        color: #333;
    }

    &__entry-description {
        font-size: 0.85rem;
        color: var(--color--font-secondary);
        margin-top: 2px;
    }

    &__menu-title {
        font-weight: bold;
        font-size: 1rem;
        padding: 0.5rem 1rem 1rem 1rem;
        border-bottom: 1px solid #ddd;
        color: #222;
    }

    &__section-title {
        font-size: 0.85rem;
        font-weight: 600;
        padding: 1rem 1rem 0.25rem;
        color: #777;
    }

    &__search-input {
        padding: 0.25rem 0.5rem;
        margin: 0 1rem;
        box-sizing: border-box;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    &__entry--checkbox {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 2px 1rem;
        cursor: pointer;
    }

    &__search-footer {
        display: flex;
        justify-content: flex-end;
    }

    &__select {
        width: calc(100% - 2rem);
        margin-bottom: 1rem;
        padding: 0.3rem 0.5rem;
        margin: 0.5rem 1rem;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    &__menu-drawer {
        position: fixed;
        inset: 0;
        z-index: 11;

        .context-menu__menu-title {
            display: flex;
            justify-content: space-between;

            button {
                background: none;
                border: none;
                cursor: pointer;
            }
        }
    }

    &__overlay {
        position: absolute;
        inset: 0;
        background: rgba(255, 255, 255, 0.8);
    }

    &__menu-drawer-panel {
        position: absolute;
        bottom: 0;
        width: 100%;
        background-color: #fff;
        border-top: solid thin var(--dark-gray-color-30);

        .context-menu__menu-title {
            padding: 1rem;
        }
    }
}

.menu-drawer-enter-from,
.menu-drawer-leave-to {
    .context-menu__overlay {
        opacity: 0;
    }

    .context-menu__menu-drawer-panel {
        transform: translateY(100%);
    }
}

.menu-drawer-enter-active,
.menu-drawer-leave-active {
    transition: transform 0.3s ease, opacity 0.3s ease;

    .context-menu__overlay,
    .context-menu__menu-drawer-panel {
        transition: inherit;
    }
}

.menu-drawer-enter-to,
.menu-drawer-leave-from {
    .context-menu__overlay {
        opacity: 1;
    }

    .context-menu__menu-drawer-panel {
        transform: translateY(0);
    }
}

button.button.context-menu__button-active {
    background-color: var(--activity-color);
}

button.button.context-menu__button-active:hover {
    background-color: var(--base-color);
}
</style>
