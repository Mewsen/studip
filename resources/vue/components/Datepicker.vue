<template>
    <span>
        <input type="hidden" :name="name" :value="returnValue">
        <input type="text"
               ref="visibleInput"
               class="visible_input"
               v-bind="$attrs"
               v-on="$listeners"
               :placeholder="placeholder">
    </span>
</template>

<script>
import RestrictedDatesHelper from '../../assets/javascripts/lib/RestrictedDatesHelper';

export default {
    name: 'Datepicker',
    inheritAttrs: false,
    props: {
        name: {
            type: String,
            required: false
        },
        value: [Date, String, Number],
        mindate: [Date, Number, String],
        maxdate: [Date, Number, String],
        placeholder: String,
        disableHolidays: {
            type: Boolean,
            default: false,
        },
        emitDate: {
            type: Boolean,
            default: false,
        },
        returnAs: {
            type: String,
            default: 'localized',
            validator(value) {
                return ['localized', 'unix', 'iso'].includes(value);
            }
        }
    },
    computed: {
        input() {
            return $(this.$refs.visibleInput);
        },
        parameters() {
            let params = {
                onSelect: () => {
                    this.setUnixTimestamp();
                },
                maxDate: this.convertInputToNativeDate(this.maxdate),
                minDate: this.convertInputToNativeDate(this.mindate),
            };
            if (this.disableHolidays) {
                params.beforeShowDay = (date) => {
                    RestrictedDatesHelper.loadRestrictedDatesByYear(date.getFullYear()).then(
                        () => this.input.datepicker('refresh'),
                        () => null
                    );

                    const {reason, lock} = RestrictedDatesHelper.isDateRestricted(date);
                    return [!lock, lock ? 'ui-datepicker-is-locked' : null, reason];
                };
            }

            return params;
        },
        returnValue() {
            if (this.returnAs === 'unix') {
                return this.convertInputToUnixTimestamp(this.value);
            }

            if (this.returnAs === 'iso') {
                return this.convertInputToNativeDate(this.value).toISOString();
            }

            return this.convertInputToNativeDate(this.value).toLocaleDateString();
        }
    },
    methods: {
        convertInputToNativeDate(input) {
            if (input instanceof Date) {
                return input;
            }

            if (input === 'today') {
                return new Date();
            }

            return input ? new Date(input * 1000) : null;
        },
        convertInputToUnixTimestamp(input) {
            if (input instanceof Date) {
                return Math.floor(input.getTime() / 1000);
            }

            if (!isNaN(parseInt(input, 10))) {
                return parseInt(input, 10);
            }

            return input;
        },
        setUnixTimestamp () {
            let date = this.input.datepicker('getDate');
            this.$emit('input', this.emitDate ? date : Math.floor(date.getTime() / 1000));
        }
    },
    mounted () {
        let value = this.convertInputToUnixTimestamp(this.value);

        if (Number.isInteger(value)) {
            let date = new Date(value * 1000);
            this.input.val(date.toLocaleDateString());
        } else {
            this.input.val(value);
        }
        this.input.datepicker(this.parameters);
    },
    watch: {
        maxdate(current) {
            this.input.datepicker(
                'option',
                'maxDate',
                this.convertInputToNativeDate(current)
            );
        },
        mindate(current) {
            this.input.datepicker(
                'option',
                'minDate',
                this.convertInputToNativeDate(current)
            );
        },
        value(current, previous) {
            if (current.toISOString() !== previous.toISOString()) {
                this.input.datepicker('setDate', current);
                this.input.datepicker('refresh');
            }
        }
    }
}
</script>
