<template>
    <article class="studip" :class="{ collapsable, collapsed }" v-bind="$attrs">
        <header>
            <h1 @click="doToggle">
                <template v-if="collapsable">
                    <StudipIcon class="studip-articles--icon" shape="arr_1right" v-if="collapsed" />
                    <StudipIcon class="studip-articles--icon" shape="arr_1down" v-else />
                </template>
                <slot name="title" v-bind="{ isOpen: collapsed }"></slot>
            </h1>
            <slot v-if="$slots.titleplus" name="titleplus"></slot>
        </header>
        <section v-if="!collapsed">
            <slot name="body"></slot>
        </section>
        <footer v-if="$slots.footer">
            <slot name="footer"></slot>
        </footer>
    </article>
</template>

<script lang="ts">
import Vue from 'vue';
import StudipIcon from './StudipIcon.vue';

export default Vue.extend({
    props: {
        collapsable: {
            type: Boolean,
            default: false,
        },
        closed: {
            type: Boolean,
            default: false,
        },
    },
    components: { StudipIcon },
    data() {
        return { collapsed: this.closed };
    },
    methods: {
        doToggle() {
            if (this.collapsable) {
                this.collapsed = !this.collapsed;
            }
        },
    },
});
</script>
<style scoped>
article.studip.collapsable.collapsed {
    padding-block-end: 0;
}
article.studip.collapsable.collapsed > header {
    margin-block-end: 0;
}
article.studip.collapsable > header > h1 {
    cursor: pointer;
}

.studip-articles--icon {
}
</style>
