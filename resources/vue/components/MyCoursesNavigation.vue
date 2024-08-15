<template>
    <ul class="my-courses-navigation" v-if="navigationLength > 0">
        <li v-for="(nav, index) in navigation" class="my-courses-navigation-item" :class="nav.class" :key="index">
            <a v-if="nav" :href="nav.url" v-bind="nav.attr">
                <studip-icon :shape="nav.icon.shape" :role="nav.icon.role" :size="iconSize"></studip-icon>
            </a>
            <span v-else class="empty-slot" :style="{width: `${iconSize}px`}"></span>
        </li>
    </ul>
</template>

<script>
export default {
    name: 'my-courses-navigation',
    props: {
        navigation: Object,
        iconSize: {
            type: Number,
            required: false,
            default: 24,
        },
    },
    computed: {
        navigationLength () {
            return Object.keys(this.navigation).length;
        }
    }
}
</script>
