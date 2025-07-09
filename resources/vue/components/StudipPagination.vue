<template>
    <div class="pagination-wrapper-flex">
        <p :id="pagination_id" class="audible">
            {{ $gettext('Blättern') }}
        </p>
        <ul class="pagination" role="navigation" :aria-labelledby="pagination_id">
            <li class="prev" v-if="currentPage > 0">
                <button class="pagination--link" @click.prevent="goBack" rel="prev" :title="$gettext('Zurück')">
                    <span class="audible">{{ $gettext('Eine Seite') }}</span>
                    {{ $gettext('zurück') }}
                </button>
            </li>
            <template v-for="offset of offsets" :key="offset">
                <li class="divider"
                    v-if="offset === (total_offsets - 1) && currentPage < (total_offsets - 1) - (range + 1)">
                    &hellip;
                </li>
                <li :class="{'current': offset === currentPage, 'no-divider': offset === 0}">
                    <button class="pagination--link" @click.prevent="goTo(offset)">
                        <span class="audible">{{ $gettext('Seite') }}</span>
                        {{ offset + 1 }}
                    </button>
                </li>
                <li class="divider"
                    v-if="offset === 0 && currentPage > range + 1">
                    &hellip;
                </li>
            </template>
            <li class="next no-divider" v-if="currentPage < total_offsets - 1">
                <button class="pagination--link" @click.prevent="goNext" rel="next" :title="$gettext('Weiter')">
                    <span class="audible">{{ $gettext('Eine Seite') }}</span>
                    {{ $gettext('weiter') }}
                </button>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: 'studip-pagination',
    emits: ['pageUpdated'],
    props: {
        currentPage: {
            type: Number,
            required: true
        },
        totalItems: {
            type: Number,
            required: true
        },
        itemsPerPage: {
            type: Number,
            required: true
        },
        range: {
            type: Number,
            default: 2,
            min: 1
        }
    },
    computed: {
        pagination_id() {
            return 'pagination-label-' + this._.uid;
        },
        total_offsets() {
            return Math.ceil(this.totalItems / this.itemsPerPage);
        },
        offsets() {
            let offsets = [0, this.currentPage, (this.total_offsets - 1)];
            for (let i = 1; i <= this.range; i++) {
                offsets.push(this.currentPage - i);
                offsets.push(this.currentPage + i);
            }
            offsets = offsets.map(item => parseInt(item, 10));
            offsets = [...new Set(offsets)];
            offsets = offsets.filter(item => item >= 0 && item < this.total_offsets);
            offsets.sort((a, b) => a - b);
            return offsets;
        },

    },
    methods: {
        goBack() {
            this.updatePage(this.currentPage - 1);
        },
        goNext() {
            this.updatePage(this.currentPage + 1);
        },
        goTo(selected) {
            if (selected === this.currentPage) {
                return;
            }
            this.updatePage(selected);
        },
        updatePage(page) {
            const pageNumber = parseInt(page, 10);
            const offset = pageNumber * this.itemsPerPage;
            this.$emit('pageUpdated', pageNumber, offset);
        }
    }
}
</script>
