<template>
    <div class="formpart range_input">
        <input type="range"
               :name="name"
               :min="min"
               :max="max"
               :step="step"
               :aria-valuemin="min"
               :aria-valuemax="max"
               :aria-valuenow="myValue"
               v-bind="$attrs"
               v-model="myValue">
        <output for="fader">
            {{ $gettext('%{myValue} von %{max}', {myValue: myValue || '1', max: max}) }}
        </output>
    </div>
</template>

<script>
export default {
    name: 'range-input',
    emits: ['update:modelValue'],
    props: {
        name: {
            type: String,
            required: true
        },
        modelValue: {
            required: false,
            default: 1
        },
        min: {
            type: Number,
            required: false,
            default: 1
        },
        max: {
            type: Number,
            required: false,
            default: 10
        },
        step: {
            type: Number,
            required: false,
            default: 1
        }
    },
    data () {
        return {
            myValue: 1
        };
    },
    mounted () {
        this.myValue = this.modelValue > this.min ? this.modelValue : this.min;
        if (this.myValue > this.max) {
            this.myValue = this.max;
        }
    },
    inheritAttrs: false,
    watch: {
        myValue: {
            handler(newValue) {
                this.$emit('update:modelValue', newValue);
            },
            deep: true
        }
    }
}
</script>
