<template>
    <Teleport :to="targetSelector">
        <div v-if="visible && displayOverlay" class="drawer-overlay" @click="emit('close')"></div>
        <div class="drawer-wrapper">
            <Transition name="drawer-slide">
                <div v-if="visible" class="drawer" :class="[wrapperClass, `drawer--${side}`]" :style="drawerStyle">
                    <header class="drawer__header">
                        <button
                            class="drawer__close"
                            @click="emit('close')"
                            :title="$gettext('Schließen')"
                            :aria-label="$gettext('Schließen')"
                        >
                            <StudipIcon shape="decline" :size="24" />
                        </button>
                    </header>
                    <div class="drawer__content">
                        <slot />
                    </div>
                </div>
            </Transition>
        </div>
    </Teleport>
</template>

<script setup>
import StudipIcon from '@/vue/components/StudipIcon.vue';
import { computed } from 'vue';
const emit = defineEmits(['close']);
// Props mit Defaults
const props = defineProps({
    visible: Boolean,
    side: {
        type: String,
        default: 'left',
        validator: (val) => ['left', 'right'].includes(val),
    },
    width: {
        type: [Number, String],
        default: '300px', // 270 + 2*15 Padding
    },
    maxWidth: {
        type: Number,
        required: false,
    },
    attachTo: {
        type: [HTMLElement, null],
        default: null,
    },
    wrapperClass: {
        type: String,
        default: '',
    },
    displayOverlay: {
        type: Boolean,
        default: false,
    },
});

// Selector für Teleport
const targetSelector = computed(() => {
    if (props.attachTo instanceof HTMLElement) {
        return `#${props.attachTo.id}`;
    }
    return '.content';
});

const drawerStyle = computed(() => {
    const style = {};
    if (typeof props.width === 'number') {
        style.width = props.width + 'px';
    } else {
        style.width = props.width;
    }

    if (props.maxWidth) {
        style.maxWidth = props.maxWidth + 'px';
    }

    style[props.side] = 0;

    return style;
});
</script>
<style lang="scss" scoped>
.drawer-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
}

.drawer {
    position: absolute;
    z-index: 501;
    background-color: white;
    overflow-y: auto;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    max-height: 100%;
    height: 100%;
    top: 0;

    .drawer__header {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 10;
        border-bottom: 1px solid var(--dark-gray-color-10);
        display: flex;
        flex-direction: row-reverse;
        justify-content: space-between;
        align-items: center;

        .drawer__close {
            float: right;
            background: none;
            border: none;
            cursor: pointer;
            padding: 10px 20px;

            img {
                vertical-align: middle;
            }
        }
    }

    .drawer__content {
        padding: 1rem;
    }
}

.drawer-slide-enter-active,
.drawer-slide-leave-active {
    transition: transform 0.3s ease;
}

.drawer--left.drawer-slide-enter-from,
.drawer--left.drawer-slide-leave-to {
    transform: translateX(-100%);
}

.drawer--right.drawer-slide-enter-from,
.drawer--right.drawer-slide-leave-to {
    transform: translateX(100%);
}

@media (max-width: 768px) {
    .drawer {
        width: 100% !important;
        max-width: none !important;
        position: fixed;
    }
}
</style>
