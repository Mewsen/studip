<template>
    <button class="square-button" @click="$emit('click')">
        <studip-icon :shape="icon" :size="50" />
        <span>{{ title }}</span>
    </button>
</template>

<script>
export default {
    name: 'studip-square-button',
    emits: ['click'],
    props: {
        icon: {
            type: String,
            default: 'seminar',
        },
        title: {
            type: String,
            required: true,
            validator: (value) => {
                return value !== '';
            },
        },
    },
};
</script>