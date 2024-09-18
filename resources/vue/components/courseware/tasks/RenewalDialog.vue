<template>
    <studip-dialog
        :title="$gettext('Verlängerungsanfrage bearbeiten')"
        :confirmText="$gettext('Speichern')"
        confirmClass="accept"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        height="350"
        @close="$emit('close')"
        @confirm="updateRenewal"
    >
        <template #dialogContent>
            <form class="default" @submit.prevent="">
                <label>
                    {{ $gettext('Fristverlängerung') }}
                    <select v-model="state">
                        <option value="declined">
                            {{ $gettext('ablehnen') }}
                        </option>
                        <option value="granted">
                            {{ $gettext('gewähren') }}
                        </option>
                    </select>
                </label>
                <label v-if="state === 'granted'">
                    {{ $gettext('neue Frist') }}
                    <DateInput v-model="date" class="size-l" :min="submissionDate" @nullDate="nullDate = true" />
                </label>
            </form>
        </template>
    </studip-dialog>
</template>

<script>
import { mapActions } from 'vuex';
import DateInput from '../layouts/CoursewareDateInput.vue';

export default {
    props: ['renewalState', 'submissionDate'],
    components: {
        DateInput,
    },
    data: () => ({
        date: null,
        state: null,
        nullDate: false,
    }),
    methods: {
        resetLocalVars() {
            this.date = this.submissionDate ?? null;
            this.state = this.renewalState;
        },
        updateRenewal() {
            if (this.nullDate) {
                this.$emit('nullDate');
                return;
            }

            const date = new Date(this.date);

            if (date > new Date(this.submissionDate)) {
                date.setHours(23);
                date.setMinutes(59);
                date.setSeconds(59);
                date.setMilliseconds(999);
            }

            this.$emit('update', {
                state: this.state,
                date: this.state === 'granted' ? date || Date.now() : null,
                warn: date < new Date(this.submissionDate)
            });
        },
    },
    mounted() {
        this.resetLocalVars();
    },
    watch: {
        submissionDate(newValue) {
            if (newValue !== this.date) {
                this.resetLocalVars();
            }
        },
        renewalState(newValue) {
            if (newValue !== this.state) {
                this.resetLocalVars();
            }
        },
    },
};
</script>
