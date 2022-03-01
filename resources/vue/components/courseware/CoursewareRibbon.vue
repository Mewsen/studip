<template>
    <div :class="{ 'cw-ribbon-wrapper-consume': consumeMode }">
        <div v-if="stickyRibbon" class="cw-ribbon-sticky-top"></div>
        <header class="cw-ribbon" ref="ribbon" :class="{ 'cw-ribbon-sticky': stickyRibbon, 'cw-ribbon-consume': consumeMode }">
            <div class="cw-ribbon-wrapper-left">
                <nav class="cw-ribbon-nav" ref="buttons">
                    <slot name="buttons" />
                </nav>
                <nav class="cw-ribbon-breadcrumb" ref="breadcrumb">
                    <ul>
                        <slot name="breadcrumbList" />
                    </ul>
                </nav>
            </div>
            <div class="cw-ribbon-wrapper-right" ref="links">
                <a
                    href="#"
                    class="cw-ribbon-button cw-ribbon-button-menu"
                    :title="textRibbon.toolbar"
                    @click="activeToolbar"
                >
                </a>
                <a
                    href="#"
                    ref="consumeModeSwitch"
                    class="cw-ribbon-button"
                    :class="[consumeMode ? 'cw-ribbon-button-zoom-out' : 'cw-ribbon-button-zoom']"
                    :title="consumeMode ? textRibbon.fullscreen_off : textRibbon.fullscreen_on"
                     @click="toggleConsumeMode"
                ></a>
                <slot name="menu" />
            </div>
            <div v-if="consumeMode" class="cw-ribbon-consume-bottom"></div>
            <courseware-ribbon-toolbar
                v-if="showTools"
                :toolsActive="unfold"
                :class="{ 'cw-ribbon-tools-sticky': stickyRibbon }"
                :canEdit="canEdit"
                @deactivate="deactivateToolbar"
            />
        </header>
        <div v-if="stickyRibbon" class="cw-ribbon-sticky-bottom"></div>
        <div v-if="stickyRibbon" class="cw-ribbon-sticky-spacer"></div>
    </div>
</template>

<script>
import CoursewareRibbonToolbar from './CoursewareRibbonToolbar.vue';

export default {
    name: 'courseware-ribbon',
    components: {
        CoursewareRibbonToolbar,
    },
    props: {
        canEdit: Boolean,
    },
    data() {
        return {
            readModeActive: false,
            stickyRibbon: false,
            textRibbon: {
                toolbar: this.$gettext('Inhaltsverzeichnis'),
                fullscreen_on: this.$gettext('Vollbild einschalten'),
                fullscreen_off: this.$gettext('Vollbild ausschalten'),
            },
            unfold: false,
            showTools: false,
        };
    },
    computed: {
        consumeMode() {
            return this.$store.getters.consumeMode;
        },
        toolsActive() {
            return this.$store.getters.showToolbar;
        },
    },
    methods: {
        toggleConsumeMode() {
            if (!this.consumeMode) {
                this.$store.dispatch('coursewareConsumeMode', true);
                this.$store.dispatch('coursewareSelectedToolbarItem', 'contents');
                this.$store.dispatch('coursewareViewMode', 'read');
            } else {
                this.$store.dispatch('coursewareConsumeMode', false);
            }
        },
        activeToolbar() {
            this.$store.dispatch('coursewareShowToolbar', true);
        },
        deactivateToolbar() {
            this.$store.dispatch('coursewareShowToolbar', false);
        },
        handleScroll() {
            if (window.outerWidth > 767) {
                this.stickyRibbon = window.scrollY > 130;
            } else {
                this.stickyRibbon = window.scrollY > 75;
            }
        },
        handleBreadcrumbSizing() {
            let links = $(this.$refs.breadcrumb).find('ul > li:not(.cw-ribbon-breadcrumb-item-current) a').get();
            if (links.length == 0) {
                return;
            }
            $(links).find('span.long').show();
            $(links).find('span.short').hide();

            for (const link of links) {
                if (this.$refs.breadcrumb.clientWidth >= this.getTotalAvailableRibbonWidth()) {
                    $(link).find('span.long').hide();
                    $(link).find('span.short').show();
                }
            }

            this.$nextTick(() => {
                if (this.$refs.breadcrumb.clientHeight > parseFloat($(this.$refs.ribbon).css('min-height')) || window.outerWidth < 1200) {
                    $(this.$refs.breadcrumb).find('ul').addClass('current-only');
                } else {
                    $(this.$refs.breadcrumb).find('ul').removeClass('current-only');
                }
                $(this.$refs.breadcrumb).find('ul > li.cw-ribbon-breadcrumb-item-current > span').css('max-width', (this.getTotalAvailableRibbonWidth() - 2));
            });
        },
        getTotalAvailableRibbonWidth() {
            let ribbonClientWidth = this.$refs.ribbon.clientWidth;
            let paddingLeft = parseFloat($(this.$refs.ribbon).css('padding-left'));
            let paddingRight = parseFloat($(this.$refs.ribbon).css('padding-right'));
            let buttonsWidth = this.$refs.buttons.clientWidth;
            let linksWidth = this.$refs.links.clientWidth;

            return ribbonClientWidth - paddingLeft - paddingRight - buttonsWidth - linksWidth;
        }
    },
    mounted() {
        window.addEventListener('scroll', this.handleScroll);
        this.handleBreadcrumbSizing();
        window.addEventListener('resize', this.handleBreadcrumbSizing);
    },
    updated () {
        this.handleBreadcrumbSizing();
    },
    watch: {
        toolsActive(newState, oldState) {
            let view = this;
            if(newState) {
                this.showTools = true;
                setTimeout(() => {view.unfold = true}, 10);
            } else {
                this.unfold = false;
                setTimeout(() => {
                    if(!view.toolsActive) {
                        view.showTools = false;
                    }
                }, 800);
            }
        },
        consumeMode(newState) {
            this.$refs.consumeModeSwitch.focus();
        }
    }
};
</script>
