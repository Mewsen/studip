<template>
    <div class="cw-toolbar-wrapper">
        <div id="cw-toolbar" class="cw-toolbar" :class="{ 'cw-toolbar-sticky': stickyToolbar}" :style="stickyStyle">
            <div v-if="showTools" class="cw-toolbar-tools" :class="{ unfold: unfold, hd: isHd, wqhd: isWqhd }">
                <div id="cw-toolbar-nav" class="cw-toolbar-button-wrapper">
                    <button
                        class="cw-toolbar-button"
                        :class="{ active: activeTool === 'blockAdder' }"
                        :title="$gettext('Blöcke hinzufügen')"
                        @click="activateTool('blockAdder')"
                    >
                        {{ $gettext('Blöcke') }}
                    </button>
                    <button
                        class="cw-toolbar-button"
                        :class="{ active: activeTool === 'containerAdder' }"
                        :title="$gettext('Abschnitte hinzufügen')"
                        @click="activateTool('containerAdder')"
                    >
                        {{ $gettext('Abschnitte') }}
                    </button>
                    <button
                        class="cw-toolbar-button"
                        :class="{ active: activeTool === 'clipboard' }"
                        :title="$gettext('Block Merkliste')"
                        @click="activateTool('clipboard')"
                    >
                        {{ $gettext('Merkliste') }}
                    </button>
                    <button
                        class="cw-toolbar-button cw-toolbar-button-toggle"
                        :title="$gettext('Werkzeugleiste einklappen')"
                        @click="toggleToolbarActive"
                    >
                        <studip-icon shape="arr_2left" :size="24" />
                    </button>
                </div>
                <div class="cw-toolbar-tool-wrapper">
                    <CoursewareToolbarBlocks
                        v-if="activeTool === 'blockAdder'"
                        :toolbarContentHeight="toolbarContentHeight"
                    />
                    <CoursewareToolbarContainers
                        v-if="activeTool === 'containerAdder'"
                    />
                    <CoursewareToolbarClipboard
                        v-if="activeTool === 'clipboard'"
                        :toolbarContentHeight="toolbarContentHeight"
                    />
                </div>
            </div>
            <div class="cw-toolbar-folded-wrapper" :style="foldedToolbarStyle">
                <button
                    class="cw-toolbar-button"
                    :title="$gettext('Werkzeugleiste ausklappen')"
                    @click="toggleToolbarActive"
                >
                    <studip-icon shape="arr_2right" :size="24" />
                </button>
                <button
                    class="cw-toolbar-button"
                    :title="
                        hideEditLayout
                            ? $gettext('Bearbeitungselemente anzeigen')
                            : $gettext('Bearbeitungselemente ausblenden')
                    "
                    @click="toggleHideEditLayout"
                >
                    <studip-icon :shape="hideEditLayout ? 'visibility-checked' : 'visibility-invisible'" :size="24" />
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import CoursewareToolbarBlocks from './CoursewareToolbarBlocks.vue';
import CoursewareToolbarContainers from './CoursewareToolbarContainers.vue';
import CoursewareToolbarClipboard from './CoursewareToolbarClipboard.vue';
import containerMixin from '@/vue/mixins/courseware/container.js';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-toolbar',
    mixins: [containerMixin],
    components: {
        CoursewareToolbarBlocks,
        CoursewareToolbarContainers,
        CoursewareToolbarClipboard,
    },
    data() {
        return {
            unfold: true,
            showTools: true,
            activeTool: 'blockAdder',
            stickyToolbar: false,
            windowWidth: window.outerWidth,
            windowInnerHeight: window.innerHeight,
        };
    },
    computed: {
        ...mapGetters({
            relatedContainers: 'courseware-containers/related',
            structuralElementById: 'courseware-structural-elements/byId',
            toolbarActive: 'toolbarActive',
            hideEditLayout: 'hideEditLayout',
            consumeMode: 'consumeMode',
        }),
        scrollTopStyles() {
            return window.getComputedStyle(document.getElementById('scroll-to-top'));
        },
        containers() {
            return this.relatedContainers({
                parent: this.structuralElementById({ id: this.$route.params.id }),
                relationship: 'containers',
            });
        },
        toolbarHeader() {
            let header = '';
            if (this.activeTool === 'blockAdder') {
                header = this.$gettext('Block hinzufügen');
            }
            if (this.activeTool === 'containerAdder') {
                header = this.$gettext('Abschnitt hinzufügen');
            }

            return header;
        },
        isHd() {
            return this.windowWidth >= 1920;
        },
        isWqhd() {
            return this.windowWidth >= 2560;
        },

        foldedToolbarStyle() {
            const top = this.stickyToolbar ? 150 : 302;
            return { height: (this.windowInnerHeight - top) + 'px' };
        },

        toolbarContentHeight() {
            const top = this.stickyToolbar ? 210 : 360;
            return this.windowInnerHeight - top;
        },
        stickyStyle() {
            return this.stickyToolbar ? { top: '116px'} : {};
        }
    },
    methods: {
        ...mapActions({
            toggleToolbarActive: 'toggleToolbarActive',
            toggleHideEditLayout: 'toggleHideEditLayout',
        }),
        activateTool(tool) {
            this.activeTool = tool;
        },
        handleScroll() {
            if (this.windowWidth > 767) {
                this.stickyToolbar = window.scrollY > 128 && !this.consumeMode;
            } else {
                this.stickyToolbar = window.scrollY > 75 && !this.consumeMode;
            }
        },
        onResize() {
            this.windowWidth = window.outerWidth;
            this.windowInnerHeight = window.innerHeight;
        },
    },
    mounted() {
        window.addEventListener('scroll', this.handleScroll);
        this.$nextTick(() => {
            window.addEventListener('resize', this.onResize);
        });
        this.resetAdderStorage();
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.onResize);
    },

    watch: {
        containers(newValue, oldValue) {
            if (newValue) {
                this.resetAdderStorage();
            }
        },
        toolbarActive(newState, oldState) {
            let view = this;
            if (newState) {
                this.showTools = true;
                setTimeout(() => {
                    view.unfold = true;
                }, 10);
            } else {
                this.unfold = false;
                setTimeout(() => {
                    if (!view.toolbarActive) {
                        view.showTools = false;
                    }
                }, 600);
            }
        },
    },
};
</script>
