<template>
    <section class="contentbox">
        <header v-if="title || $slots.header">
            <slot name="header">
                <h1>
                    <StudipIcon v-if="icon" :shape="icon" />
                    {{ title }}
                </h1>
            </slot>
            <slot name="header-nav">
                <nav v-if="items">
                    <StudipActionMenu :items="items" />
                </nav>
            </slot>
        </header>

        <slot></slot>

        <footer>
            <slot name="footer"></slot>
        </footer>
    </section>
</template>

<script>
import StudipActionMenu from './StudipActionMenu.vue';
import StudipIcon from './StudipIcon.vue';

export default {
    components: { StudipActionMenu, StudipIcon },
    props: {
        icon: {
            type: String,
            required: false,
        },
        items: {
            type: Array,
            required: false,
        },
        title: {
            type: String,
            required: true,
        },
    },
};
</script>
