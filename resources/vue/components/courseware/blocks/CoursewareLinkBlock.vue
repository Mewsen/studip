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
                    <template v-if="currentUrl">
                        <div v-if="currentQREnabled" class="cw-link-qr-code">
                            <qrcode-vue
                                v-if="currentUrl && currentQREnabled"
                                :value="currentUrl"
                                :size="currentQRSize ?? 180"
                                :level="currentQRLevel"
                                render-as="svg"
                            />
                            <a :href="currentUrl" target="_blank">
                                {{ currentTitle }}
                            </a>
                        </div>

                        <a v-else :href="currentUrl" target="_blank">
                            <div class="cw-link external">
                                <span class="cw-link-title">{{ currentTitle }}</span>
                            </div>
                        </a>
                    </template>

                    <courseware-companion-box
                        v-if="!currentUrl && isTeacher"
                        mood="pointing"
                        :msgCompanion="$gettext('Bitte wählen Sie eine URL als Ziel aus.')"
                    />
                </div>
                <div v-if="currentType === 'internal'">
                    <router-link
                        v-if="currentTarget"
                        :to="{ name: 'CoursewareStructuralElement', params: { id: currentTarget } }"
                    >
                        <div class="cw-link internal">
                            <span class="cw-link-title">
                                {{ currentTitle }}
                            </span>
                        </div>
                    </router-link>
                    <courseware-companion-box
                        v-if="!currentTarget && isTeacher"
                        mood="pointing"
                        :msgCompanion="$gettext('Bitte wählen Sie eine Seite als Ziel aus.')"
                    />
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
                        v-if="!currentUnitData && isTeacher"
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
                    <label v-if="currentType !== 'unit'">
                        {{ $gettext('Titel') }}
                        <input type="text" v-model="currentTitle" />
                    </label>
                    <template v-if="currentType === 'external'">
                        <label>
                            {{ $gettext('URL') }}
                            <input type="text" v-model="currentUrl" @change="fixUrl" />
                        </label>
                        <label>
                            {{ $gettext('QR-Code') }}
                            <select v-model="currentQREnabled">
                                <option value="false">{{ $gettext('Kein QR-Code') }}</option>
                                <option value="true">{{ $gettext('QR-Code') }}</option>
                            </select>
                        </label>
                        <template v-if="currentQREnabled">
                            <label>
                                {{ $gettext('QR-Code Fehlerkorrekturlevel') }}
                                <select v-model="currentQRLevel">
                                    <option value="L">{{ $gettext('L – Niedrig (ca. 7% Wiederherstellung)') }}</option>
                                    <option value="M">{{ $gettext('M – Mittel (ca. 15% Wiederherstellung)') }}</option>
                                    <option value="Q">{{ $gettext('Q – Quartil (ca. 25% Wiederherstellung)') }}</option>
                                    <option value="H">{{ $gettext('H – Hoch (ca. 30% Wiederherstellung)') }}</option>
                                </select>
                            </label>
                            <label>
                                {{ $gettext('QR-Code Größe') }}
                                <select v-model="currentQRSize">
                                    <option value="180">{{ $gettext('Klein') }}</option>
                                    <option value="240">{{ $gettext('Mittel') }}</option>
                                    <option value="300">{{ $gettext('Groß') }}</option>
                                    <option value="360">{{ $gettext('Sehr groß') }}</option>
                                </select>
                            </label>
                        </template>
                    </template>
                    <label v-if="currentType === 'internal'">
                        {{ $gettext('Seite') }}
                        <select v-if="filteredStructuralElements.length > 0" v-model="currentTarget">
                            <option v-for="(el, index) in filteredStructuralElements" :key="index" :value="el.id">
                                {{ el.attributes.title }}
                            </option>
                        </select>
                        <span v-else>{{
                            $gettext('Es wurde keine weitere Seite in diesem Lernmaterial gefunden.')
                        }}</span>
                    </label>
                    <label v-if="currentType === 'unit' && inCourseContext">
                        {{ $gettext('Lernmaterial') }}
                        <select v-if="units.length > 0" v-model="currentUnitTarget">
                            <option v-for="(unit, index) in units" :key="index" :value="unit.id">
                                {{ unit.title }}
                            </option>
                        </select>
                        <span v-else>{{
                            $gettext('Es wurde kein weiteres Lernmaterial in dieser Veranstaltung gefunden.')
                        }}</span>
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
import QrcodeVue from 'qrcode.vue';
import { mapActions, mapGetters } from 'vuex';
import { $gettext } from '../../../../assets/javascripts/lib/gettext';

export default {
    name: 'courseware-link-block',
    mixins: [blockMixin, colorMixin],
    components: { ...BlockComponents, StudipIdentImage, QrcodeVue },
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
            currentQREnabled: false,
            currentQRLevel: '',
            currentQRSize: '',
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
        qrEnabled() {
            return this.block?.attributes?.payload?.['qr-enabled'];
        },
        qrLevel() {
            return this.block?.attributes?.payload?.['qr-level'];
        },
        qrSize() {
            return this.block?.attributes?.payload?.['qr-size'];
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
            const headerUrl = this.rootElement(this.unitById({ id: this.currentUnitTarget }))?.relationships?.image
                ?.meta?.['download-url'];
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
            this.currentQREnabled = this.qrEnabled;
            this.currentQRLevel = this.qrLevel;
            this.currentQRSize = this.qrSize;
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
                        title: this.currentTitle,
                        'qr-enabled': this.currentQREnabled,
                        'qr-level': this.currentQRLevel,
                        'qr-size': this.currentQRSize,
                    },
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
    },
};
</script>
<style scoped lang="scss">
@import '../../../../assets/stylesheets/scss/courseware/blocks/link';
</style>
