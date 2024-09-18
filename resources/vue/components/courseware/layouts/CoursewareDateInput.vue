<template>
    <input :value="formattedDate" @input="onInput" type="date" :min="formattedMinDate" />
</template>

<script>
const fromISO8601 = (string) => new Date(string);
const toISO8601 = (date) => date.toISOString();
const pad = (what, length = 2) => `00000000${what}`.substr(-length);

export default {
    props: ['value', 'min'],
    data: () => ({
        date: new Date(),
        submissionDate: new Date()
    }),
    computed: {
        formattedDate() {
            return `${this.date.getFullYear()}-${pad(this.date.getMonth() + 1)}-${pad(this.date.getDate())}`;
        },
        formattedMinDate() {
            return `${this.submissionDate.getFullYear()}-${pad(this.submissionDate.getMonth() + 1)}-${pad(this.submissionDate.getDate())}`;
        }
    },
    methods: {
        onInput({ target }) {
            if (target.valueAsDate) {
                const newValue = toISO8601(target.valueAsDate);
                if (newValue !== this.value) {
                    this.$emit('input', newValue);
                }
            } else {
                this.$emit('nullDate');
            }
        },
    },
    beforeMount() {
        if (this.value) {
            this.date = fromISO8601(this.value);
        }
        if (this.min) {
            this.submissionDate = fromISO8601(this.min);
        }
    },
};
</script>
