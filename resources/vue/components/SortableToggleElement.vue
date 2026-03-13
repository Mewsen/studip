<script setup lang="ts">
import {computed, useSlots} from "vue";
import {$gettextInterpolate} from "../../assets/javascripts/lib/gettext";

const props = defineProps({
    tag: {
        type: String,
        default: 'th'
    },
    scope: {
        type: String,
        default: 'col',
        validator(value: string | null): boolean {
            return [null, 'row', 'col', 'rowgroup', 'colgroup'].includes(value);
        }
    },
    column: {
        type: String,
        required: true
    },
    sortBy: {
        type: String,
        default : ''
    },
    sortDir: {
        type: String,
        default: 'asc'
    },
    active: {
        type: Boolean,
        default: true
    },
    label: {
        type: String,
        default: null
    }
});

const emit = defineEmits(['update:sortBy', 'update:sortDir']);
const slots = useSlots();

const isActive = computed(() => props.sortBy === props.column);

const baseLabel = computed(() => {
    if (props.label) {
        return props.label;
    }

    const vnode = slots.default?.()[0];
    return vnode?.children?.toString() ?? '';
});

const ariaSort = computed(() => {
    if (!props.active) {
        return undefined;
    }

    if (!isActive.value) {
        return 'none';
    }

    return props.sortDir === 'asc' ? 'ascending' : 'descending';
});

const ariaLabel = computed(() => {
    if (!props.active) {
        return undefined;
    }

    if (!isActive.value || props.sortDir === 'desc') {
        return $gettextInterpolate(
            'Sortieren nach %{label}, aufsteigend sortieren.',
            {label: baseLabel.value}
        );
    }

    return $gettextInterpolate(
        'Sortieren nach %{label}, absteigend sortieren.',
        {label: baseLabel.value}
    );
});

const cssClasses = computed(() => {
    if (!props.active || !isActive.value) {
        return [];
    }

    return props.sortDir === 'asc' ? ['sortasc'] : ['sortdesc'];
});

const toggleSort = () => {
    let newDir = 'asc';
    if (isActive.value) {
        newDir = props.sortDir === 'asc' ? 'desc' : 'asc';
    }
    emit('update:sortBy', props.column);
    emit('update:sortDir', newDir);
};
</script>

<template>
    <component :is="tag"
               :scope="scope"
               :aria-sort="ariaSort"
               :class="cssClasses"
    >
        <template v-if="!active">
            <slot name="default"></slot>
        </template>
        <button v-else
                type="button"
                class="as-link"
                @click="toggleSort"
                :title="label"
                :aria-label="ariaLabel"
        >
            <slot name="default"></slot>
        </button>
    </component>
</template>
