<template>
    <button class="square-button" @click="$emit('click')">
        <StudipIcon :shape="icon" :size="50" />
        <span>{{ title }}</span>
    </button>
</template>

<script>
import StudipIcon from './StudipIcon.vue';
export default {
    name: 'StudipSquareButton',
    components: {
        StudipIcon
    },
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
<style scoped lang="scss">
$size: 130px;
.square-button {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    max-height: $size;
    max-width: $size;
    min-width: $size;
    min-height: $size;
    margin: 10px;
    padding: 10px;
    background-color: transparent;
    border: solid thin var(--content-color-40);
    cursor: pointer;

    img {
        width: 100%;
        height: 50px;
        margin-bottom: 8px;
    }
    span {
        color: var(--base-color);
        min-width: 110px;
    }
    &:hover span {
        color: var(--red);
    }
}
</style>
