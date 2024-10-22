<template>
    <div v-if="!closed" :class="classNames" role="region" :aria-label="label" :aria-describedby="'messagebox-' + id">
        <div class="messagebox-icon"></div>
        <div class="messagebox-content" role="status" :id="'messagebox-' + id">
            <p class="messagebox-message">
                <slot></slot>
            </p>
            <button
                v-if="hasDetails"
                class="messagebox-button messagebox-details-toggle"
                href="#"
                :title="$gettext('Detailanzeige umschalten')"
                @click.prevent.stop="closedDetails = !closedDetails"
            >
                {{ $gettext('Details') }}
            </button>
            <div v-if="showDetails" class="messagebox-details">
                <slot name="details">
                    <ul>
                        <li v-for="(detail, index) in details" v-html="detail" :key="index"></li>
                    </ul>
                </slot>
            </div>
        </div>
        <button
            v-if="!hideClose"
            class="messagebox-button messagebox-close"
            role="button"
            :title="$gettext('Nachrichtenbox schließen')"
            @click.prevent="close()"
        ></button>
    </div>
</template>

<script>
export default {
    name: 'studip-message-box',
    props: {
        type: {
            type: String, // exception, error, success, info, warning
            default: 'info',
            validator(type) {
                return ['exception', 'error', 'warning', 'success', 'info'].indexOf(type) !== -1;
            },
        },
        details: {
            type: Array,
            default: () => [],
        },
        hideDetails: {
            type: Boolean,
            default: false,
        },
        hideClose: {
            type: Boolean,
            default: false,
        },
    },
    computed: {
        classNames() {
            return {
                messagebox: true,
                [`messagebox_${this.type}`]: true,
                details_hidden: !this.showDetails,
            };
        },
        hasDetails() {
            return !!this.$slots.details || this.details.length > 0;
        },
        showDetails() {
            return this.hasDetails && !this.closedDetails;
        },
        label() {
            switch (this.type) {
                case 'exception':
                    return this.$gettext('Systemfehler');
                case 'error':
                    return this.$gettext('Fehler');
                case 'warning':
                    return this.$gettext('Warnung');
                case 'info':
                    return this.$gettext('Hinweis');
                case 'success':
                    return this.$gettext('Erfolg');
            }

            return '';
        },
    },
    methods: {
        close() {
            this.closed = true;

            this.$emit('close');
        },
    },
    data() {
        return {
            closed: false,
            closedDetails: this.hideDetails,
            id: null,
        };
    },
    mounted() {
        this.id = this._uid;
    },
};
</script>
