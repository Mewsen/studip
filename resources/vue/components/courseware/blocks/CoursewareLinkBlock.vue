<template>
    <div class="cw-block cw-block-link">
        <courseware-default-block
            :block="block"
            :canEdit="canEdit"
            :isTeacher="isTeacher"
            :preview="true"
            @showEdit="initCurrentData"
            @storeEdit="storeBlock"
            @closeEdit="initCurrentData"
        >
            <template #content>
                <div v-if="currentType === 'external'">
                    <a :href="currentUrl" target="_blank">
                        <div class="cw-link external">
                            <span class="cw-link-title">{{ currentTitle }}</span>
                        </div>
                    </a>
                </div>
                <div v-if="currentType === 'internal'">
                    <router-link :to="{ name: 'CoursewareStructuralElement', params: { id: currentTarget } }">
                        <div class="cw-link internal">
                            <span class="cw-link-title">
                                {{ currentTitle }}
                            </span>
                        </div>
                    </router-link>
                </div>
                <div v-if="currentType === 'unit' && inCourseContext">
                    <a v-if="currentUnitData" :href="currentUnitData.url">
                        <studip-ident-image
                            v-model="identimage"
                            :baseColor="currentUnitData.color?.hex ?? '#fff'"
                            :pattern="currentUnitData.title ?? ''"
                        />
                        <div class="cw-link unit">
                            <div class="cw-unit-link" :style="previewImageStyle"></div>
                            <div class="cw-link-title unit">
                                <header>{{ currentUnitData.title }}</header>
                                <p>{{ currentUnitData.description }}</p>
                            </div>
                        </div>
                    </a>
                    <courseware-companion-box
                        v-else
                        mood="pointing"
                        :msgCompanion="$gettext('Bitte wählen Sie ein Lernmaterial als Ziel aus.')"
                    />
                </div>
            </template>
            <template v-if="canEdit" #edit>
                <form class="default" @submit.prevent="">
                    <label>
                        {{ $gettext('Art des Links') }}
                        <select v-model="currentType">
                            <option value="external">{{ $gettext('Extern') }}</option>
                            <option value="internal">{{ $gettext('Intern') }}</option>
                            <option v-if="inCourseContext" value="unit">
                                {{ $gettext('Lernmaterial in der Veranstaltung') }}
                            </option>
                        </select>
                    </label>
                    <label v-show="currentType !== 'unit'">
                        {{ $gettext('Titel') }}
                        <input type="text" v-model="currentTitle" />
                    </label>
                    <label v-show="currentType === 'external'">
                        {{ $gettext('URL') }}
                        <input type="text" v-model="currentUrl" @change="fixUrl" />
                    </label>
                    <label v-show="currentType === 'internal'">
                        {{ $gettext('Seite') }}
                        <select v-model="currentTarget">
                            <option v-for="(el, index) in filteredStructuralElements" :key="index" :value="el.id">
                                {{ el.attributes.title }}
                            </option>
                        </select>
                    </label>
                    <label v-show="currentType === 'unit' && inCourseContext">
                        {{ $gettext('Lernmaterial') }}
                        <select v-model="currentUnitTarget">
                            <option v-for="(unit, index) in units" :key="index" :value="unit.id">
                                {{ unit.title }}
                            </option>
                        </select>
                    </label>
                </form>
            </template>
            <template #info>
                <p>{{ $gettext('Informationen zum Link-Block') }}</p>
            </template>
        </courseware-default-block>
    </div>
</template>

<script>
import StudipIdentImage from './../../StudipIdentImage.vue';
import BlockComponents from './block-components.js';
import blockMixin from '@/vue/mixins/courseware/block.js';
import colorMixin from '@/vue/mixins/courseware/colors.js';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-link-block',
    mixins: [blockMixin, colorMixin],
    components: Object.assign(BlockComponents, { StudipIdentImage }),
    props: {
        block: Object,
        canEdit: Boolean,
        isTeacher: Boolean,
    },
    data() {
        return {
            currentType: '',
            currentTarget: '',
            currentUnitTarget: '',
            currentUrl: '',
            currentTitle: '',
            identimage: '',
        };
    },
    computed: {
        ...mapGetters({
            context: 'context',
            courseUnits: 'courseware-units/all',
            unitById: 'courseware-units/byId',
            allStructuralElements: 'courseware-structural-elements/all',
            structuralElementById: 'courseware-structural-elements/byId',
        }),
        type() {
            return this.block?.attributes?.payload?.type;
        },
        target() {
            return this.block?.attributes?.payload?.target;
        },
        unitTarget() {
            return this.block?.attributes?.payload?.['unit-target'];
        },
        url() {
            return this.block?.attributes?.payload?.url;
        },
        title() {
            return this.block?.attributes?.payload?.title;
        },
        units() {
            const allUnits = this.courseUnits;
            const units = allUnits.filter((unit) => unit.id !== this.context.unit);

            let unitData = [];
            for (const unit of units) {
                unitData.push(this.getUnitData(unit));
            }
            return unitData;
        },
        currentUnitData() {
            return this.currentType === 'unit' ? this.getUnitData(this.unitById({ id: this.currentUnitTarget })) : null;
        },
        headerImageUrl() {
            const headerUrl = this.rootElement(this.unitById({ id: this.currentUnitTarget }))?.relationships?.image?.meta?.[
                'download-url'
            ];
            return headerUrl ? headerUrl : null;
        },
        previewImageStyle() {
            if (this.headerImageUrl) {
                return { 'background-image': 'url(' + this.headerImageUrl + ')' };
            }

            return { 'background-image': 'url(' + this.identimage + ')' };
        },
        inCourseContext() {
            return this.context.type === 'courses';
        },
        filteredStructuralElements() {
            return this.allStructuralElements.filter(
                (element) => element.relationships.unit.data.id === this.context.unit
            );
        },
    },
    mounted() {
        this.initCurrentData();
    },
    methods: {
        ...mapActions({
            loadCourseUnits: 'loadCourseUnits',
            updateBlock: 'updateBlockInContainer',
            companionWarning: 'companionWarning',
        }),
        initCurrentData() {
            this.loadCourseUnits(this.context.id);
            this.currentType = this.type;
            this.currentTarget = this.target;
            this.currentUnitTarget = this.unitTarget;
            this.currentUrl = this.url;
            this.fixUrl();
            this.currentTitle = this.title;
        },

        fixUrl() {
            if (
                this.currentUrl.indexOf('http://') !== 0 &&
                this.currentUrl.indexOf('https://') !== 0 &&
                this.currentUrl !== ''
            ) {
                this.currentUrl = 'https://' + this.currentUrl;
            }
        },
        storeBlock() {
            let empty = false;
            let info = '';
            let defaultTitle = '';
                
            switch (this.currentType) {
                case 'external':
                    info = this.$gettext('Bitte wählen Sie eine URL als Ziel aus.');
                    empty = this.currentUrl === '';
                    this.currentTarget = '';
                    this.currentUnitTarget = '';
                    this.currentTitle = this.currentTitle || this.currentUrl;
                    break;
                case 'internal': 
                    info = this.$gettext('Bitte wählen Sie eine Seite als Ziel aus.');
                    empty = this.currentTarget === '';
                    if (!empty) {
                        const element = this.filteredStructuralElements.find((el) => el.id === this.currentTarget);
                        defaultTitle = element.attributes.title;
                    }
                    this.currentUrl = '';
                    this.currentUnitTarget = '';
                    this.currentTitle = this.currentTitle || defaultTitle;
                    break;
                case 'unit':
                    info = this.$gettext('Bitte wählen Sie ein Lernmaterial als Ziel aus.');
                    empty = this.currentUnitTarget === '';
                    this.currentTarget = '';
                    this.currentUrl = '';
                    this.currentTitle = '';
                    break;
            }

            if (empty) {
                this.companionWarning({ info: info });

                return false;
            } else {
                const attributes = {
                    payload: {
                        type: this.currentType,
                        target: this.currentTarget,
                        'unit-target': this.currentUnitTarget,
                        url: this.currentUrl,
                        title: this.currentTitle
                    }
                };

                this.updateBlock({
                    attributes: attributes,
                    blockId: this.block.id,
                    containerId: this.block.relationships.container.data.id,
                });
            }
        },
        getUnitData(unit) {
            if (unit) {
                const url = STUDIP.URLHelper.getURL('dispatch.php/course/courseware/courseware/' + unit.id, {
                    cid: this.context.id,
                });
                const element = this.rootElement(unit);
                const color = this.mixinColors.find((color) => color.class === element.attributes.payload.color);
                return {
                    id: unit.id,
                    url: url,
                    title: element.attributes.title,
                    description: element.attributes.payload.description,
                    color: color,
                };
            }
            return null;
        },
        rootElement(unit) {
            if (unit && this.context.type === 'courses') {
                return this.structuralElementById({
                    id: unit.relationships['structural-element'].data.id,
                });
            }
        },
    }
};
</script>
<style scoped lang="scss">
@import '../../../../assets/stylesheets/scss/courseware/blocks/link.scss';
</style>
