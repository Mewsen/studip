<template>
    <div class="input-group studip-search">
        <label>
            <span class="sr-only">{{ $gettext('Suchen') }}</span>
            <input
                type="text"
                name="search"
                :value="search"
                :placeholder="$gettext('Suchen')"
                @input="$emit('update:search', $event.target.value)"
                @keyup.enter="$emit('submit')"
            />
        </label>

        <button
            v-if="search"
            type="button"
            class="reset-search"
            @click.prevent="$emit('reset')"
            :title="$gettext('Suche zurücksetzen')"
        >
            <studip-icon shape="decline" :alt="$gettext('Suche zurücksetzen')" />
        </button>

        <button
            type="button"
            class="submit-search"
            @click.prevent="$emit('submit')"
            :title="$gettext('Suche ausführen')"
        >
            <studip-icon shape="search" :alt="$gettext('Suche ausführen')" />
        </button>
    </div>
</template>

<script setup>
import StudipIcon from '@/vue/components/StudipIcon.vue';

// PROPS
const props = defineProps({
    search: {
        type: String,
        default: '',
    },
});

// EVENTS
const emit = defineEmits(['update:search', 'reset', 'submit']);
</script>

<style lang="scss" scoped>
.input-group.studip-search {
    display: flex;
    align-items: stretch;
    width: 100%;
    margin-bottom: 15px;

    label {
        flex: 1 1 auto;
        display: contents;
    }

    input[type='text'] {
        flex: 1 1 auto;
        display: block;
        line-height: 1.5;
        padding: 0.25em 0.5em;
        margin: 0;
        width: 100%;
        border: 1px solid var(--color--input-field-border);
        border-top-left-radius: var(--border-radius-search);
        border-bottom-left-radius: var(--border-radius-search);
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
    button {
        margin: 0;
        line-height: 1.5;
        background-color: var(--color--input-field-background);
        color: var(--color--highlight);
        min-width: auto;
        border: 1px solid var(--color--input-field-border);

        border-left: none;
        border-radius: 0;

        .studip-icon {
            vertical-align: middle;
        }
    }
    .submit-search {
        border-top-right-radius: var(--border-radius-search);
        border-bottom-right-radius: var(--border-radius-search);
    }
}
</style>
