<template>
    <div>
        <div v-if="structureLoadingState === 'done'">
            <courseware-search-results v-show="showSearchResults" />
            <courseware-structural-element
                v-show="!showSearchResults"
                :canVisit="canVisit"
                :structural-element="selected"
                :ordered-structural-elements="orderedStructuralElements"
                @select-element="selectStructuralElement"
            ></courseware-structural-element>
            <Teleport to="#courseware-search-widget" name="sidebar-search">
                <courseware-search-widget v-if="selected !== null"></courseware-search-widget>
            </Teleport>
        </div>
        <studip-progress-indicator
            v-if="structureLoadingState === 'loading'"
            class="loading-indicator-content"
            :description="$gettext('Lade Lernmaterial...')"
        />
        <courseware-companion-box
            v-if="structureLoadingState === 'error'"
            mood="sad"
            :msgCompanion="loadingErrorMessage"
        />
        <courseware-companion-overlay />
    </div>
</template>

<script>
import CoursewareStructuralElement from '@/vue/components/courseware/structural-element/CoursewareStructuralElement.vue';
import CoursewareSearchResults from '@/vue/components/courseware/structural-element/CoursewareSearchResults.vue';
import CoursewareCompanionBox from '@/vue/components/courseware/layouts/CoursewareCompanionBox.vue';
import CoursewareCompanionOverlay from '@/vue/components/courseware/layouts/CoursewareCompanionOverlay.vue';
import CoursewareSearchWidget from '@/vue/components/courseware/widgets/CoursewareSearchWidget.vue';

import StudipProgressIndicator from '@/vue/components/StudipProgressIndicator.vue';

import { mapActions, mapGetters } from 'vuex';

export default {
    components: {
        CoursewareStructuralElement,
        CoursewareSearchResults,
        CoursewareCompanionBox,
        StudipProgressIndicator,
        CoursewareSearchWidget,
        CoursewareCompanionOverlay,
    },
    data: () => ({
        canVisit: null,
        selected: null,
        structureLoadingState: 'idle',
        loadingErrorStatus: null,
    }),
    computed: {
        ...mapGetters({
            context: 'context',
            courseware: 'courseware',
            orderedStructuralElements: 'courseware-structure/ordered',
            relatedStructuralElement: 'courseware-structural-elements/related',
            showSearchResults: 'showSearchResults',
            structuralElementLastMeta: 'courseware-structural-elements/lastMeta',
            structuralElements: 'courseware-structural-elements/all',
            structuralElementById: 'courseware-structural-elements/byId',
            userId: 'userId',
            userIsTeacher: 'userIsTeacher',
        }),
        loadingErrorMessage() {
            switch (this.loadingErrorStatus) {
                case 404:
                    return this.$gettext('Die Seite konnte nicht gefunden werden.');
                case 403:
                    return this.$gettext('Diese Seite steht Ihnen leider nicht zur Verfügung.');
                default:
                    return this.$gettext('Beim Laden der Seite ist ein Fehler aufgetreten.');
            }
        },
    },
    methods: {
        ...mapActions({
            buildStructure: 'courseware-structure/build',
            coursewareBlockAdder: 'coursewareBlockAdder',
            invalidateStructureCache: 'courseware-structure/invalidateCache',
            loadCoursewareStructure: 'courseware-structure/load',
            loadStructuralElement: 'loadStructuralElement',
        }),
        async selectStructuralElement(id) {
            if (!id) {
                return;
            }

            try {
                await this.loadStructuralElement(id);
            } catch (error) {
                this.loadingErrorStatus = error.status;
                this.structureLoadingState = 'error';
                return;
            }

            this.$nextTick(() => {
                this.canVisit = this.structuralElementLastMeta['can-visit'];
                this.selected = this.structuralElementById({ id });
            });
        },
    },
    async mounted() {
        this.structureLoadingState = 'loading';
        try {
            await this.loadCoursewareStructure();
        } catch (error) {
            this.loadingErrorStatus = error.status;
            this.structureLoadingState = 'error';
            return;
        }

        this.structureLoadingState = 'done';
        console.debug("mounted", this.$route.params);
        const selectedId = this.$route.params?.id;
        await this.selectStructuralElement(selectedId);
    },
    watch: {
        $route(to) {
            // reset block adder on navigate
            this.coursewareBlockAdder({});

            const selectedId = to.params?.id;
            this.selectStructuralElement(selectedId);
            window.scrollTo({ top: 0 });
        },
        structuralElements: {
            async handler() {
                // compute order of structural elements once more
                await this.buildStructure();

                // throw away stale cache
                this.invalidateStructureCache();
            },
            deep: true,
        },
    },

    async beforeCreate() {
        STUDIP.loadChunk('courseware');

        const httpClient = this.$store.getters.httpClient;
        httpClient.get('studip/properties').then((response) => {
            response.data.data.forEach((prop) => {
                this.$store.dispatch('studip-properties/storeRecord', prop);
            });
        });

        this.$store.dispatch('setUrlHelper', STUDIP.URLHelper);
        this.$store.dispatch('setUserId', STUDIP.USER_ID);
        this.$store.dispatch('users/loadById', { id: STUDIP.USER_ID });

        const { type, unit } = this.$store.getters["context"];
        this.$store.dispatch('courseware-units/loadById', { id: unit });
        if (type === 'courses') {
            this.$store.dispatch('loadProgresses');
            await this.$store.dispatch('courseware-units/loadById', {
                id: unit,
                options: { include: 'structural-element' },
            });
        }

        this.$store.dispatch('courseware-templates/loadAll');
        this.$store.dispatch('loadUserClipboards', STUDIP.USER_ID);

        STUDIP.JSUpdater.register(
            'coursewareclipboard',
            () => { this.$store.dispatch('loadUserClipboards', STUDIP.USER_ID)},
            () => { return { 'counter' : this.$store.getters['courseware-clipboards/all'].length };},
            5000
        );
    },
};
</script>
