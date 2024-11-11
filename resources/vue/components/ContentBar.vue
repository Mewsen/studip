<template>
    <div :class="{ 'cw-ribbon-wrapper-consume': consumeMode }"
         :id="isContentBar ? 'contentbar' : undefined">
        <div v-show="stickyRibbon"
             class="cw-ribbon-sticky-top"></div>
        <div class="cw-ribbon-header-container"
             ref="headerContainer">
            <header
                ref="header"
                :id="isContentBar ? 'cw-ribbon' : undefined"
                class="cw-ribbon"
                :class="{ 'cw-ribbon-sticky': stickyRibbon, 'cw-ribbon-consume': consumeMode }"
            >
                <div class="cw-ribbon-wrapper-left">
                    <nav class="cw-ribbon-nav contentbar-nav"
                         :class="buttonsClass">
                        <div class="contentbar-icon"
                             v-if="icon">
                            <a href="">
                                <studip-icon :shape="icon"
                                             role="navigation"
                                             :size="32" />
                            </a>
                        </div>
                        <slot name="buttons-left" />
                    </nav>
                    <nav class="cw-ribbon-breadcrumb">
                        <span v-if="title">
                            <a href="">
                                {{ title ?? $gettext('(Kein Titel)') }}
                            </a>
                        </span>
                        <slot v-if="breadcrumbFallback && $slots['breadcrumb-fallback']" name="breadcrumb-fallback" />
                        <slot v-else name="breadcrumb-list" />
                        <div class="cw-ribbon-info-text">
                            <slot name="info-text" />
                        </div>
                    </nav>
                </div>
                <div class="cw-ribbon-wrapper-right">
                    <slot name="buttons-right" />
                    <ContentBarTableOfContents v-if="toc" :toc="toc" />
                    <slot name="menu" />
                </div>
                <slot name="other" />
            </header>
        </div>
        <div v-if="stickyRibbon" class="cw-ribbon-sticky-bottom"></div>
        <div v-if="stickyRibbon" class="cw-ribbon-sticky-spacer"></div>
    </div>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';

import '../../assets/stylesheets/scss/courseware/layouts/ribbon.scss';
import StudipIcon from './StudipIcon.vue';
import { store } from '../../assets/javascripts/chunks/vue';
import { TOCItem, traverse } from './table-of-contents';
import ContentBarTableOfContents from './ContentBarTableOfContents.vue';

export default defineComponent({
    name: 'ContentBar',
    components: { ContentBarTableOfContents, StudipIcon },
    data() {
        return {
            stickyRibbon: false,
            // This flag lets us run a hook the first time that this component's
            // dom is updated by vue.  See updated() hook.
            hasPerformedFirstUpdate: false,
            // This intersection observer allows us to respond to changes in
            // the contentbar's visibility so that it is always displayed
            // at the correct height on the page after being hidden or shown for
            // any reason (e.g. when Courseware search results are opened/closed).
            observer: undefined as IntersectionObserver | undefined,
        };
    },
    props: {
        // (Optional) A class that is applied to the <nav> element where the
        // 'icon', if any (see 'icon' prop), and the buttons-left slot are displayed.
        buttonsClass: String,
        // (Optional) If provided, displayed in the same place as the breadcrumb-list
        // and breadcrumb-fallback slots.
        title: String,
        // (Optional) If provided, displays the given icon before the 'icons-left' slot.
        icon: String,
        // If true, this element serves as the global contentbar.
        // It will stick to the top of the screen when the page is scrolled down,
        // and it will be pinned to the top of the screen in compact mode.
        // If false, this element will not be used as the global contentbar.
        // It will just be a normal element on the page that looks the same as the
        // global contentbar, but does not have any special sticky behavior.
        isContentBar: {
            type: Boolean,
            required: false,
        },
        // (Optional) If provided, a 'table of contents' icon will be shown on
        // the right side of the ContentBar. When clicked, it will open/close a
        // panel with the table of contents inside.
        toc: Object as PropType<TOCItem>,
    },
    mounted() {
        window.addEventListener('scroll', this.handleScroll);
        this.observer = new IntersectionObserver(this.intersectionCallback);
        this.observer.observe(this.$el);
        this.$forceUpdate();
    },
    updated() {
        // The "Responsive Toolbar" works by reaching inside the DOM template of
        // this component and grabbing some elements from it to stick them
        // in the ResponsiveToolbar, jquery-style.
        // That only works if the elements are actually present in the DOM
        // when the courseware-contentbar-mounted event is fired.
        // To ensure that that is the case, we defer emitting that event until
        // this component's dom has been fully rendered for the first time.
        // This trick is brought to you by the Vue 2 docs:
        // https://v2.vuejs.org/v2/api/#updated
        if (this.hasPerformedFirstUpdate) {
            return;
        }
        this.hasPerformedFirstUpdate = true;
        this.$nextTick(() => {
            if (this.isContentBar) {
                // TODO rename this event.
                window.STUDIP.eventBus.emit('courseware-contentbar-mounted', this);
            }
        });
    },
    beforeDestroy() {
        if (this.isContentBar) {
            window.STUDIP.eventBus.emit('courseware-contentbar-before-destroy', this);
        }
        window.removeEventListener('scroll', this.handleScroll);
        this.observer!.disconnect();
    },
    watch: {
        stickyRibbon(value) {
            this.$emit('stickyRibbonChange', value);
        },
    },
    computed: {
        consumeMode(): boolean {
            // We have to access the global studipStore over an import rather than
            // using $store/mapState/mapGetters/etc.,  because this component is
            // compatible with Courseware, and in the various Courseware apps,
            // $store does not include the global StudipStore.
            return store.state.studip.consumeMode;
        },
        breadcrumbFallback(): boolean {
            return window.outerWidth < 1200;
        },
    },
    methods: {
        intersectionCallback(entries: IntersectionObserverEntry[]) {
            this.handleScroll();
        },
        handleScroll() {
            const top = this.$el.getBoundingClientRect().top;
            this.stickyRibbon = this.isContentBar && top <= 50 && !this.consumeMode;
        },
    },
});
</script>
