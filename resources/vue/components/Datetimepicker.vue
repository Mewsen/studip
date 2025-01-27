<template>
    <span>
        <input type="hidden" :name="name" :value="modelValue">
        <input v-bind="$attrs"
               type="text"
               ref="visibleInput"
               class="visible_input"
               @change="setUnixTimestamp">
    </span>
</template>

<script>
export default {
    name: 'datetimepicker',
    emits: ['update:modelValue'],
    inheritAttrs: false,
    props: {
        name: {
            type: String,
            required: false
        },
        modelValue: {
            required: false
        },
        mindate: {
            required: false
        },
        maxdate: {
            required: false
        }
    },
    methods: {
        setUnixTimestamp () {
            let formatted_date = this.$refs.visibleInput.value;
            let date = formatted_date.match(/(\d+)/g);
            if (date) {
                date = new Date(`${date[2]}-${date[1]}-${date[0]} ${date[3]}:${date[4]}`);
                this.$emit('update:modelValue', Math.floor(date / 1000));
            } else {
                this.$emit('update:modelValue', null);
            }
        }
    },
    mounted () {
        let value = !isNaN(parseInt(this.modelValue, 10)) ? parseInt(this.modelValue, 10) : this.modelValue;
        if (Number.isInteger(value)) {
            let date = new Date(value * 1000);
            let formatted_date =
                (date.getDate() < 10 ? "0" : "") + date.getDate()
                + "."
                + (date.getMonth() < 9 ? "0" : "") + (date.getMonth() + 1)
                + "."
                + date.getFullYear()
                + " "
                + (date.getHours() < 10 ? "0" : "") + date.getHours()
                + ":"
                + (date.getMinutes() < 10 ? "0" : "") + date.getMinutes();
            this.$refs.visibleInput.value = formatted_date;
        } else {
            this.$refs.visibleInput.value = value;
        }
        let params = {
            onSelect: () => {
                this.setUnixTimestamp();
            }
        };
        if (this.mindate) {
            params.minDate = new Date(this.mindate * 1000)
        }
        if (this.maxdate) {
            params.maxDate = new Date(this.maxdate * 1000)
        }
        $(this.$refs.visibleInput).datetimepicker(params);
    },
    watch: {
        mindat (new_data) {
            $(this.$refs.visibleInput).datetimepicker('option', 'minDate', new Date(new_data * 1000));
        },
        maxdate (new_data) {
            $(this.$refs.visibleInput).datetimepicker('option', 'maxDate', new Date(new_data * 1000));
        }
    }
}
</script>
