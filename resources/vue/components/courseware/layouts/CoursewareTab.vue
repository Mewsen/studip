<template>
    <div v-if="isActive">
        <slot></slot>
    </div>
</template>

<script>
export default {
    inject: ['addTab', 'activeTab'],
    props: {
        name: {
            type: String,
            required: true,
        },
        icon: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            index: null,
        };
    },
    computed: {
        isActive() {
            return this.activeTab() === this.index;
        },
    },
    mounted() {
        this.index = this.$parent.tabs.length;
        this.addTab({ name: this.name, icon: this.icon});
    },
};
</script>