<template>
    <StudipDialog
        v-if="show && process"
        :title="$gettext('Bearbeitungszeit ändern')"
        :confirmText="$gettext('Speichern')"
        confirmClass="accept"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        @close="onClose"
        @confirm="onConfirm"
    >
        <template #dialogContent>
            <form class="default">
                <p>
                    {{ $gettext('Aktuelle Bearbeitungszeit:') }} <StudipDate :date="startDate" />–<StudipDate :date="endDate" />
                    ({{ $gettextInterpolate($gettext('%{ count } Tage'), { count: oldDuration }) }})
                </p>
                <div class="formpart">
                    <LabelRequired
                        :id="`peer-review-process-${uid}`"
                        :label="$gettext('Bearbeitungszeit verlängern bis zum:')"
                    />
                    <input
                        :id="`peer-review-process-${uid}`"
                        name="end-date"
                        type="date"
                        v-model="localEndDate"
                        :min="endDateString"
                        class="size-l"
                        required
                    />
                    <div>({{ $gettextInterpolate($gettext('%{ count } Tage'), { count: newDuration }) }})</div>
                </div>
            </form>
        </template>
    </StudipDialog>
</template>

<script>
import { mapGetters } from 'vuex';
import LabelRequired from '../../../forms/LabelRequired.vue';
import StudipDate from '../../../StudipDate.vue';
import StudipDialog from '../../../StudipDialog.vue';

const midnight = (_date) => {
    const date = new Date(_date);
    date.setHours(0);
    date.setMinutes(0);
    date.setSeconds(0);
    date.setMilliseconds(0);
    return date;
};

const dateString = (date) =>
    `${date.getFullYear()}-${('' + (date.getMonth() + 1)).padStart(2, '0')}-${('' + date.getDate()).padStart(2, '0')}`;

let nextUid = 0;

export default {
    model: {
        prop: 'show',
        event: 'updateShow',
    },
    components: {
        LabelRequired,
        StudipDate,
        StudipDialog,
    },
    props: {
        show: {
            type: Boolean,
            required: true,
        },
        process: {
            type: Object,
            default: null,
        },
    },
    data: () => ({ localEndDate: null, uid: nextUid++ }),
    computed: {
        configuration() {
            return this.process?.attributes?.configuration ?? {};
        },
        endDate() {
            return midnight(this.process?.attributes?.['review-end'] ?? new Date());
        },
        endDateString() {
            return dateString(this.endDate);
        },
        newDuration() {
            return this.localEndDate
                ? Math.floor((midnight(this.localEndDate) - midnight(this.startDate)) / (1000 * 60 * 60 * 24))
                : 0;
        },
        oldDuration() {
            return this.configuration.duration ?? '??';
        },
        startDate() {
            return midnight(this.process.attributes['review-start']);
        },
    },
    methods: {
        onClose() {
            this.$emit('updateShow', false);
        },
        onConfirm(...args) {
            this.$emit('update', this.newDuration);
        },
        resetLocalVars() {
            this.localEndDate = dateString(this.endDate ?? new Date());
        },
    },
    mounted() {
        this.resetLocalVars();
    },
    watch: {
        process() {
            this.resetLocalVars();
        },
    },
};
</script>
