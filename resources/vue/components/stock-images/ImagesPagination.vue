<template>
    <div>
        <StudipPagination
            v-show="totalItems > perPage"
            :currentPage="offset"
            :totalItems="totalItems"
            :itemsPerPage="perPage"
            @pageUpdated="onUpdateOffset"
        />
        <slot></slot>
        <StudipPagination
            v-show="totalItems > perPage"
            :currentPage="offset"
            :totalItems="totalItems"
            :itemsPerPage="perPage"
            @pageUpdated="onUpdateOffset"
        />
    </div>
</template>

<script>
import StudipPagination from '../StudipPagination.vue';

export default {
    components: { StudipPagination },
    emits: ['update:page'],
    props: {
        stockImages: {
            type: Array,
            required: true,
        },
        page: {
            type: Number,
            required: true,
        },
        perPage: {
            type: Number,
            default: 10,
        },
    },
    computed: {
        offset() {
            return this.page - 1;
        },
        totalItems() {
            return this.stockImages.length;
        },
    },
    methods: {
        onUpdateOffset(offset) {
            this.$emit('update:page', offset + 1);
        },
    },
};
</script>
