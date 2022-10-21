<template>
    <div class="cw-manager-element-item-wrapper">
        <a
            v-if="!sortChapters"
            href="#"
            class="cw-manager-element-item"
            :class="[inserter ? 'cw-manager-element-item-inserter' : '']"
            :title="elementTitle"
            @click="clickItem">
                {{ element.attributes.title }}
        </a>
        <div
            v-else
            class="cw-manager-element-item cw-manager-element-item-sorting"
        >
            {{ element.attributes.title }}
            <div v-if="sortChapters" class="cw-manager-element-item-buttons">
                <button :disabled="!canMoveUp" @click="moveUp" :title="$gettext('Element nach oben verschieben')">
                    <studip-icon shape="arr_2up" role="sort" />
                </button>
                <button :disabled="!canMoveDown" @click="moveDown" :title="$gettext('Element nach unten verschieben')">
                    <studip-icon shape="arr_2down" role="sort" />
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'courseware-manager-element-item',
    props: {
        element: Object,
        inserter: Boolean,
        sortChapters: Boolean,
        type: String,
        canMoveUp: Boolean,
        canMoveDown: Boolean
    },
    computed: {
        elementTitle() {
            let title = this.element.attributes.title;
            if (this.inserter) {
                if (this.type === 'remote' || this.type === 'own') {
                    title = this.$gettextInterpolate(
                        this.$gettext('%{ elementTitle } kopieren'),
                        {elementTitle: this.element.attributes.title}
                    );
                } else {
                    title = this.$gettextInterpolate(
                        this.$gettext('%{ elementTitle } verschieben'),
                        {elementTitle: this.element.attributes.title}
                    );
                }
            }

            return title;
        }
    },
    methods: {
        clickItem() {
            if (this.sortChapters) {
                return false;
            }
            if (this.inserter) {
                this.$emit('insertElement', {element: this.element, source: this.type});
            } else {
                this.$emit('selectChapter', this.element.id);
            }
        },
        moveUp() {
            if (this.sortChapters) {
                this.$emit('moveUp', this.element.id);
            }
        },
        moveDown() {
            if (this.sortChapters) {
                this.$emit('moveDown', this.element.id);
            }
        },
    },
};
</script>
