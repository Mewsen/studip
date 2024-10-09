<template>
    <focus-trap v-model="trap" :clickOutsideDeactivates="false" :fallbackFocus ="() => fallbackFocusElement">
        <div
            class="cw-ribbon-tools"
            :class="{ unfold: toolsActive, 'cw-ribbon-tools-consume': consumeMode }"
        >
            <div class="cw-ribbon-tool-content">
                <div class="cw-ribbon-tool-content-nav">
                    <courseware-tabs
                        class="cw-ribbon-tool-content-tablist"
                        ref="tabs"
                    >
                        <courseware-tab
                            :name="$gettext('Inhaltsverzeichnis')"
                            alias="contents"
                            ref="contents"
                            :index="0"
                        >
                            <courseware-tools-contents
                                id="cw-ribbon-tool-contents"
                            />
                        </courseware-tab>
                        <courseware-tab
                            :name="$gettext('Lernmaterialien')"
                            :selected="showUnits"
                            alias="units"
                            ref="units"
                            :index="1"
                        >
                            <CoursewareToolsUnits />
                        </courseware-tab>
                    </courseware-tabs>
                    <button
                        :title="$gettext('schließen')"
                        class="cw-tools-hide-button"
                        ref="closeTools"
                        @click="$emit('deactivate')">
                    </button>
                </div>
            </div>
        </div>
    </focus-trap>
</template>
<script>
import CoursewareTabs from '../layouts/CoursewareTabs.vue';
import CoursewareTab from '../layouts/CoursewareTab.vue';
import CoursewareToolsContents from './CoursewareToolsContents.vue';
import CoursewareToolsUnits from './CoursewareToolsUnits.vue';
import { FocusTrap } from 'focus-trap-vue';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-ribbon-toolbar',
    components: {
        CoursewareTabs,
        CoursewareTab,
        CoursewareToolsContents,
        CoursewareToolsUnits,
        FocusTrap,
    },
    props: {
        toolsActive: Boolean,
        canEdit: Boolean,
        disableSettings: {
            type: Boolean,
            default: false,
        },
        disableAdder: {
            type: Boolean,
            default: false,
        },
        stickyRibbon: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            showContents: true,
            showUnits: false,
            trap: false,
        };
    },
    computed: {
        ...mapGetters({
            userIsTeacher: 'userIsTeacher',
            consumeMode: 'consumeMode',
            containerAdder: 'containerAdder',
            adderStorage: 'blockAdder',
            viewMode: 'viewMode',
            context: 'context',
            userById: 'users/byId',
            userId: 'userId',
            selectedToolbarItem: 'selectedToolbarItem',
            currentElementisLink: 'currentElementisLink',
        }),
        isTeacher() {
            return this.userIsTeacher;
        },
        fallbackFocusElement(){
            return this.$refs.contents;
        }
    },
    methods: {
        ...mapActions({
            coursewareContainerAdder: 'coursewareContainerAdder',
        }),
        scrollToCurrent() {
            setTimeout(() => {
                let contents = this.$refs.contents.$el; 
                let current = contents.querySelector('.cw-tree-item-link-current');
                if (current) {
                    contents.scroll({ top: current.offsetTop - 4, behavior: 'smooth' });
                }
            }, 360);
        },
    },
    mounted () {
        this.scrollToCurrent();
    },
    watch: {
        toolsActive(newValue) {
            if (newValue) {
                setTimeout(() => {
                    this.trap = true;
                }, 300);
            }
        },
    },
};
</script>
