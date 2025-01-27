<template>
    <content-bar is-content-bar @stickyRibbonChange="onStickyRibbonChange">
        <template #buttons-right>
            <button
                class="cw-ribbon-button cw-ribbon-button-menu"
                :title="strings.toolbar"
                @click.prevent="activateToolbar"
            ></button>
        </template>
        <template #other>
            <transition name="cw-ribbon-slide">
                <courseware-ribbon-toolbar
                    ref="toolbar"
                    v-show="showToolbar"
                    :stickyRibbon="stickyRibbon"
                    :class="{ 'cw-ribbon-tools-sticky': stickyRibbon }"
                    :style="{ height: toolbarHeight + 'px' }"
                    @deactivate="deactivateToolbar"
                    @blockAdded="$emit('blockAdded')"
                />
            </transition>
        </template>
        <!--  Pass these slots through to the ContentBar. -->
        <template #menu><slot name="menu" /></template>
        <template #buttons-left><slot name="buttons-left" /></template>
        <template #breadcrumb-list><slot name="breadcrumb-list" /></template>
        <template #breadcrumb-fallback><slot name="breadcrumb-fallback" /></template>
        <template #info-text><slot name="info-text"/></template>
    </content-bar>
</template>

<script lang="ts">
import ContentBar from '../../ContentBar.vue';
import { mapActions, mapGetters } from 'vuex';
import CoursewareRibbonToolbar from './CoursewareRibbonToolbar.vue';
import { store } from '../../../../assets/javascripts/chunks/vue';
import { defineComponent } from "vue";

export default defineComponent({
    name: 'CoursewareRibbon',
    components: {
        CoursewareRibbonToolbar, ContentBar
    },
    emits: ['blockAdded'],
    props: {
        canEdit: Boolean,
        showToolbarButton: {
            default: true,
            type: Boolean
        },
        showModeSwitchButton: {
            default: true,
            type: Boolean
        },
        buttonsClass: String,
        isContentBar: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            // This value is derived from stickyRibbonChange events emitted by
            // the ContentBar component (see template).
            stickyRibbon: false,
        };
    },
    computed: {
        ...mapGetters({
            showToolbar: 'showToolbar',
        }),
        consumeMode(): boolean {
            // TODO ensure that there is only one global StudipStore / 'studip' store module
            //  across Courseware and chunks/vue.js.
            // Currently, the 'studip' module of the courseware store is deceivingly named.
            // It is a completely different store than the one in chunks/vue.js.
            // It just happens to have a module with the same name, 'studip'.
            // So, to access the global studipStore, we have to import it and access it like this.
            return store.state.studip.consumeMode;
        },
        strings() {
            return {
                toolbar: this.$gettext('Inhaltsverzeichnis'),
            };
        },
        toolbarHeight() {
            if (this.stickyRibbon) {
                return window.innerHeight * 0.75;
            } else {
                return Math.min(window.innerHeight * 0.75, window.innerHeight - 197);
            }
        },
    },
    watch: {
        consumeMode(newState: boolean) {
            if (newState) {
                console.log('consumeMode watcher ', newState, 'setting coursewareViewMode "read"');
                this.coursewareViewMode('read');
            }
        },
    },
    methods: {
        ...mapActions({
            coursewareViewMode: 'coursewareViewMode',
            coursewareShowToolbar: 'coursewareShowToolbar',
        }),
        onStickyRibbonChange(value: boolean) {
            this.stickyRibbon = value;
        },
        activateToolbar() {
            this.coursewareShowToolbar(true);
        },
        deactivateToolbar() {
            this.coursewareShowToolbar(false);
        },
    },
});
</script>
