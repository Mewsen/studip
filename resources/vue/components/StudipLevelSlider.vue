<template>
    <div class="level-slider" :style="{width: this.width}">
        <div class="level-labels">
            <div class="level-label">{{ lowerLabel }}</div>
            <div class="level-label">{{ upperLabel }}</div>
        </div>
        <div class="level-numbers">
            <div v-for="i in this.maxValue"
                 :key="`level-${i}`"
                 class="level-number"
            >
                {{ i }}
            </div>
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
    .level-number {
        flex: 0 2ex;
        text-align: right;
    }
    .slider-element {
        margin-top: 5px;
        margin-left: 0.5ex;
        margin-right: 0.5ex;
    }
}
</style>
