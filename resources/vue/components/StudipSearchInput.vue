<template>
    <div class="studip-search-input">
        <input
            type="search"
            :id="inputId"
            name="studip-contact-search"
            autocomplete="off"
            data-lpignore="true"
            :value="modelValue"
            :placeholder="placeholder || $gettext('Suchen...')"
            @input="$emit('update:modelValue', $event.target.value)"
            class="search-field"
        />
        <div class="input-actions">
            <button
                v-if="modelValue"
                class="clear-button"
                @click="$emit('update:modelValue', '')"
                type="button"
                :title="$gettext('Suche leeren')"
            >
                <studip-icon shape="decline" :size="16" />
            </button>

            <studip-icon shape="search" :size="16" class="search-icon" />
        </div>
    </div>
</template>

<script setup>
defineProps(['modelValue', 'placeholder']);
defineEmits(['update:modelValue']);

const inputId = `search-${Math.random().toString(36).substr(2, 9)}`;
</script>

<style lang="scss" scoped>
.studip-search-input {
    position: relative;
    display: inline-flex;
    align-items: center;
    width: 100%;

    .search-field {
        width: 100%;
        padding: 4px 50px 4px 10px;
        margin: 0;
        line-height: 1.5;
        border: 1px solid var(--color--input-field-border);
        border-radius: var(--border-radius-default, 4px);

        &:focus {
            border-color: var(--color--focus);
            outline: none;
        }
    }

    .input-actions {
        position: absolute;
        right: 8px;
        display: flex;
        align-items: center;
        gap: 4px;
        pointer-events: none;

        .search-icon {
            opacity: 0.5;
        }

        .clear-button {
            pointer-events: auto;
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            color: var(--color--red);

            &:hover {
                filter: brightness(0.8);
            }
        }
    }
}
</style>
