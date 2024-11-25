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
                <datetimepicker :value="startTime"></datetimepicker>
            </label>
        </section>
        <section v-if="configureTime" class="col-3">
            <label>
                {{ $gettext('bis') }}
                <datetimepicker :value="endTime"></datetimepicker>
            </label>
        </section>
    </div>
</template>

<script>
import Datetimepicker from '../Datetimepicker.vue';

export default {
    name: 'ValidityTime',
    components: { Datetimepicker },
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
            startTime: this.start !== 0 ? this.start : Math.floor(Date.now() / 1000),
            endTime: this.end !== 0 ? this.end : Math.floor(Date.now() / 1000 + 7 * 86400)
        }
    },
    methods: {
        toggleTime() {
            this.configureTime = !this.configureTime;

            if (this.configureTime) {
                this.startTime = this.start !== 0 ? this.start : Math.floor(Date.now() / 1000);
                this.endTime = this.end !== 0 ? this.end : nMath.floor(Date.now() / 1000 + 7 * 86400);
            } else {
                this.startTime = 0;
                this.endTime = 0;
            }

        }
    }
}
</script>
