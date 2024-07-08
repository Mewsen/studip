<template>
    <div class="level-slider" :style="{width: this.width}">
        <div class="level-labels">
            <div>{{ lowerLabel }}</div>
            <div>{{ upperLabel }}</div>
        </div>
        <div class="level-numbers">
            <div v-for="i in this.maxValue" :key="`level-${i}`">{{ i }}</div>
        </div>
        <div ref="slider" class="slider-element"></div>
    </div>
</template>
<script>
import { $gettext } from "../../assets/javascripts/lib/gettext";

export default {
    name: 'StudipLevelSlider',
    props: {
        lowerLabel: {
            type: String,
            default: $gettext('Leicht'),
        },
        lowerValue: {
            type: Number,
            default: 1,
        },
        maxValue: {
            type: Number,
            default: 12
        },
        upperLabel: {
            type: String,
            default: $gettext('Schwer'),
        },
        upperValue: {
            type: Number,
            default: 12
        },
        width: {
            type: String,
            default: null,
        }
    },
    mounted() {
        $(this.$refs['slider']).slider({
            range: true,
            min: 1,
            max: this.maxValue,
            values: [this.lowerValue, this.upperValue],
            change: (event, ui) => {
                console.log('Updating', ui.values[0], ui.values[1]);

                this.$emit('update:lowerValue', ui.values[0]);
                this.$emit('update:upperValue', ui.values[1]);
            }
        });
    }
}
</script>
<style lang="scss">
.level-slider {
    max-width: 48em;
    .level-labels {
        display: flex;
        justify-content: space-between;
        font-size: 0.8em;
        color: var(--black);
        margin-top: 20px;
    }
    .level-numbers {
        display: flex;
        justify-content: space-between;
    }
    .slider-element {
        margin-left: 5px;
        margin-right: 9px;
        margin-top: 5px;
    }
}
</style>
