<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    }
});

const isChecked = defineModel({ default: false });
</script>

<template>
    <label class="switch-input-container" :title="label">
        <input
            v-bind="$attrs"
            class="input"
            type="checkbox"
            :checked="isChecked"
            @change="isChecked = $event.target.checked"
            :aria-checked="isChecked.toString()"
            :aria-label="label"
            role="switch"
        />
        <span class="switch-container">
            <span class="switch"></span>
        </span>
        <span class="label">{{ label }}</span>
    </label>
</template>

<style scoped>
.switch-input-container {
    cursor: pointer;
    display: flex !important;
    align-items: center;
}

.label {
    margin-left: 12px;
    color: #1a202c;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Visually hide the checkbox input */
.input {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border-width: 0;
}

.switch-container {
    --studip-switch-container-width: 40px;
    --studip-switch-size: calc(var(--studip-switch-container-width) / 2);
    display: flex;
    align-items: center;
    position: relative;
    height: var(--studip-switch-size);
    flex-basis: var(--studip-switch-container-width);
    border-radius: var(--studip-switch-size);
    background-color: var(--dark-gray-color-15);
    flex-shrink: 0;
    transition: background-color 0.25s ease-in-out;
}

.switch-container .switch {
    content: "";
    position: absolute;
    height: calc(var(--studip-switch-size) - 4px);
    width: calc(var(--studip-switch-size) - 4px);
    border-radius: 9999px;
    background-color: white;
    border: 2px solid var(--dark-gray-color-15);
    transition: transform 0.375s ease-in-out;
}

.switch::before {
    content: '';
    height: 10px;
    width: 2px;
    position: absolute;
    top: calc(50% - 5px);
    left: calc(50% - 1px);
    transform: rotate(45deg);
    background: var(--color--font-inactive);
    border-radius: 5px;
}

.switch::after {
    content: '';
    height: 2px;
    width: 10px;
    position: absolute;
    top: calc(50% - 1px);
    left: calc(50% - 5px);
    transform: rotate(45deg);
    background: var(--color--font-inactive);
    border-radius: 5px;
}

/* Styles when checked */
.input:checked + .switch-container {
    background-color: var(--green-80);
}

.input:checked + .switch-container .switch {
    border-color: var(--green-80);
    transform: translateX(calc(var(--studip-switch-container-width) - var(--studip-switch-size)));
}

.input:checked + .switch-container .switch::before {
    position: absolute;
    top: calc(50%);
    left: 50%;
    transform: translateY(-50%) rotate(45deg);
    background: var(--green);
}

.input:checked + .switch-container .switch::after {
    height: 6px;
    width: 2px;
    position: absolute;
    top: calc(55%);
    left: calc(23%);
    transform: translateY(-50%) rotate(-40deg);
    background: var(--green);
}

/* Focus states */
.input:focus + .switch-container .switch {
    border-color: var(--dark-gray-color-60);
}

.input:focus:checked + .switch-container .switch {
    border-color: var(--green);
}

/* Disabled styles */
.input:disabled + .switch-container {
    cursor: not-allowed;
    background-color: var(--dark-gray-color-15);
}

.input:disabled + .switch-container .switch {
    background-color: var(--dark-gray-color-40);
    border-color: var(--dark-gray-color-45);
}
</style>
