<template>
    <input type="time"
           ref="visibleInput"
           class="hasTimepicker"
           v-model="timeValue"
           :placeholder="placeholder"
           :min="mintime"
           :max="maxtime"
           :name="name">
</template>

<script>
export default {
    name: 'Timepicker',
    emits: ['update:modelValue'],
    inheritAttrs: false,
    props: {
        name: {
            type: String,
            required: false
        },
        modelValue: String,
        mintime: String,
        maxtime: String,
        placeholder: String,
    },
    computed: {
        timeValue: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit('update:modelValue', value);
            }
        }
    }
}
</script>
<style scoped>
input[type="time"]::-webkit-calendar-picker-indicator {
    display: none;
}
</style>
