<template>
    <div>
        <section>
            <label>
                <button class="as-link"
                   @click.prevent="toggleTime"
                   :title="configureTime
                    ? $gettext('Klicken, um diese Regel ab sofort unbegrenzt gelten zu lassen')
                    : $gettext('Klicken, um einen Zeitraum für die Gültigkeit dieser Regel festzulegen')"
                >
                    <studip-icon :shape="configureTime ? 'checkbox-unchecked' : 'checkbox-checked'"></studip-icon>
                    {{ $gettext('Diese Regel soll ab sofort zeitlich unbegrenzt gelten') }}
                </button>
            </label>
        </section>
        <section v-if="configureTime" class="col-3">
            <label>
                {{ $gettext('Diese Regel gilt von') }}
                <datetimepicker v-model="startTime" />
            </label>
        </section>
        <section v-if="configureTime" class="col-3">
            <label>
                {{ $gettext('bis') }}
                <datetimepicker v-model="endTime" />
            </label>
        </section>
    </div>
</template>

<script>
import datetimepicker from '../Datetimepicker.vue';

export default {
    name: 'ValidityTime',
    components: { datetimepicker },
    emits: ['update:start', 'update:end'],
    props: {
        start: {
            type: Number,
            default: 0
        },
        end: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            configureTime: this.start !== 0 || this.end !== 0,
            startTime: this.convertTime(this.start),
            endTime: this.convertTime(this.end, 7 * 86400),
        }
    },
    methods: {
        convertTime(time, offset = 0) {
            return time !== 0 ? time : Math.floor(Date.now() / 1000 + offset);
        },
        toggleTime() {
            this.configureTime = !this.configureTime;

            if (this.configureTime) {
                this.startTime = this.convertTime(this.start);
                this.endTime = this.convertTime(this.end, 7 * 86400);
            } else {
                this.startTime = 0;
                this.endTime = 0;
            }

        }
    },
    watch: {
        start(current) {
            this.startTime = this.convertTime(current);
        },
        end(current) {
            this.endTime = this.convertTime(current, 7 * 86400);
        },
        startTime(newTime) {
            this.$emit('update:start', newTime);
        },
        endTime(newTime) {
            this.$emit('update:end', newTime);
        }
    }
}
</script>
