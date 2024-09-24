<template>
    <div class="cw-toolbar-wrapper">
        <div id="cw-toolbar" class="cw-toolbar" :style="toolbarStyle">
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
                        <studip-icon shape="arr_2right" :size="24" />
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
            <div v-else class="cw-toolbar-folded-wrapper">
                <button
                    class="cw-toolbar-button"
                    :title="$gettext('Werkzeugleiste ausklappen')"
                    @click="toggleToolbarActive"
                >
                    <studip-icon shape="arr_2left" :size="24" />
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
            <div class="cw-toolbar-spacer-right"></div>
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
            toolbarTop: 0,
            activeTool: 'blockAdder',

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
        }),
        scrollTopStyles() {
            return window.getComputedStyle(document.getElementById('scroll-to-top'));
        },
        toolbarHeight() {
            const scrollTopHeight =
                parseInt(this.scrollTopStyles['height'], 10) +
                parseInt(this.scrollTopStyles['padding-top'], 10) +
                parseInt(this.scrollTopStyles['padding-bottom'], 10) +
                parseInt(this.scrollTopStyles['margin-bottom'], 10);
            return parseInt(
                Math.min(this.windowInnerHeight * 0.9, this.windowInnerHeight - this.toolbarTop - scrollTopHeight)
            );
        },
        toolbarContentHeight() {
            return this.toolbarHeight - 55;
        },
        toolbarStyle() {
            return {
                height: this.toolbarHeight + 'px',
                minHeight: this.toolbarHeight + 'px',
                top: this.toolbarTop + 'px',
            };
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
    },
    methods: {
        ...mapActions({
            toggleToolbarActive: 'toggleToolbarActive',
            toggleHideEditLayout: 'toggleHideEditLayout',
        }),
        activateTool(tool) {
            this.activeTool = tool;
        },
        updateToolbarTop() {
            const responsiveContentbar = document.getElementById('responsive-contentbar');
            if (responsiveContentbar) {
                const contentbarRect = responsiveContentbar.getBoundingClientRect();
                this.toolbarTop = contentbarRect.bottom + 25;
                return;
            }

            const ribbon = document.getElementById('cw-ribbon') ?? document.getElementById('contentbar');
            if (ribbon) {
                const contentbarRect = ribbon.getBoundingClientRect();
                if (ribbon.classList.contains('cw-ribbon-sticky')) {
                    this.toolbarTop = contentbarRect.bottom + 16;
                } else {
                    this.toolbarTop = contentbarRect.bottom + 15;
                }
            }
        },
        onResize() {
            this.windowWidth = window.outerWidth;
            this.windowInnerHeight = window.innerHeight;
        },
    },
    mounted() {
        this.updateToolbarTop();
        this.$nextTick(() => {
            window.addEventListener('scroll', this.updateToolbarTop);
            window.addEventListener('resize', this.onResize);
        });
        this.resetAdderStorage();
    },
    beforeDestroy() {
        window.removeEventListener('scroll', this.updateToolbarTop);
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
