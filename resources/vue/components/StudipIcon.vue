<template>
    <input
        v-if="name"
        type="image"
        :name="name"
        :src="url"
        :style="{ width: realSize + 'px', height: realSize + 'px' }"
        :role="ariaRole"
        :class="cssClass"
        v-bind="$attrs"
        v-on="$listeners"
        :alt="$attrs.alt ?? ''"
    />
    <img v-else
         :src="url"
         :style="{ width: realSize + 'px', height: realSize + 'px' }"
         :role="ariaRole"
         :class="cssClass"
         v-bind="$attrs"
         v-on="$listeners"
         :alt="$attrs.alt ?? ''"
    />
</template>

<script lang="ts">
import Vue from 'vue';

function getCSSVariableValue(property: string): Number {
    const value = getComputedStyle(document.body).getPropertyValue(property);
    return parseInt(value, 10);
}

const defaultIconSize: Number = getCSSVariableValue('--icon-size-default');
const inlineIconSize: Number = getCSSVariableValue('--icon-size-inline');

export default Vue.extend({
    name: 'studip-icon',
    props: {
        ariaRole: {
            type: String,
            required: false,
        },
        name: {
            type: String,
            required: false,
        },
        role: {
            type: String,
            required: false,
            default: 'clickable',
        },
        shape: String,
        size: {
            type: Number,
            required: false,
            default: defaultIconSize,
        },
        inline: {
            type: Boolean,
            default: false
        }
    },
    computed: {
        realSize(): Number | undefined {
            if (this.inline) {
                return inlineIconSize;
            }
            return Number(this.size) !== defaultIconSize ? this.size : undefined;
        },
        url(): string {
            if (this.shape.indexOf('http') === 0) {
                return this.shape;
            }
            var path = this.shape.split('+').reverse().join('/');
            return `${window.STUDIP.ASSETS_URL}images/icons/${this.color}/${path}.svg`;
        },
        color(): string {
            switch (this.role) {
                case 'info':
                    return 'black';

                case 'inactive':
                    return 'grey';

                case 'accept':
                case 'status-green':
                    return 'green';

                case 'attention':
                case 'new':
                case 'status-red':
                    return 'red';

                case 'info_alt':
                    return 'white';

                case 'status-yellow':
                    return 'yellow';

                case 'sort':
                case 'clickable':
                case 'navigation':
                default:
                    return 'blue';
            }
        },
        cssClass(): Array<string> {
            return [
                'studip-icon',
                this.inline ? 'studip-icon-inline' : '',
                `icon-role-${this.role}`,
                `icon-shape-${this.shape}`,
            ];
        }
    },
});
</script>
