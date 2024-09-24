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
    inheritAttrs: false,
    props: {
        name: {
            type: String,
            required: false
        },
        value: String,
        mintime: String,
        maxtime: String,
        placeholder: String,
    },
    computed: {
        timeValue: {
            get() {
                return this.value;
            },
            set(value) {
                this.$emit('input', value);
            }
        }
    }
}
</script>
