<script setup>
const url = `${STUDIP.ASSETS_URL}images/loading-indicator.svg`;

defineProps({
    size: {
        type: Number,
        default: 30
    }
});
</script>

<template>
    <img
        v-bind="$attrs"
        :src="url"
        alt=""
        :style="{ width: size + 'px', height: size + 'px' }"
    />
</template>
