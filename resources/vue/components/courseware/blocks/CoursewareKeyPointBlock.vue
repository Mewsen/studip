<template>
    <div class="cw-block cw-block-keypoint">
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
                <div class="cw-keypoint-content">
                    <div class="cw-keypoint-icon-wrapper" :style="{ borderColor: currentHexColor }">
                        <StudipIcon v-if="currentIcon" :size="48" :shape="currentIcon" role="info" class="cw-keypoint-icon" :style="{ color: currentHexColor }"/>
                    </div>
                    <p class="cw-keypoint-sentence" :style="{ backgroundColor: getRgbaFromHex(currentHexColor, 0.2) }">{{ currentText }}</p>
                </div>
            </template>
            <template v-if="canEdit" #edit>
                <form class="default" @submit.prevent="">
                    <label class="col-4">
                        {{ $gettext('Merksatz') }}
                        <input
                            type="text"
                            name="cw-keypoint-content"
                            class="cw-keypoint-set-content"
                            v-model="currentText"
                            spellcheck="true"
                        />
                    </label>
                    <br />
                    <div class="col-2">
                        <label for="current-color">
                            {{ $gettext('Farbe') }}
                        </label>
                        <StudipSelect
                            id="current-color"
                            :options="colors"
                            label="name"
                            :clearable="false"
                            :reduce="(option) => option.class"
                            v-model="currentColor"
                        >
                            <template #no-options>
                                {{ $gettext('Es steht keine Auswahl zur Verfügung.') }}
                            </template>
                            <template #selected-option="option">
                                <span class="vs__option-color" :style="{ 'background-color': option.hex }"></span
                                ><span>{{ option.name }}</span>
                            </template>
                            <template #option="option">
                                <span class="vs__option-color" :style="{ 'background-color': option.hex }"></span
                                ><span>{{ option.name }}</span>
                            </template>
                        </StudipSelect>
                    </div>
                    <div class="col-2">
                        <label for="current-icon">
                            {{ $gettext('Icon') }}
                        </label>
                        <StudipSelect
                            id="current-icon"
                            :options="icons"
                            :clearable="false"
                            v-model="currentIcon">
                            <template #no-options>
                                {{ $gettext('Es steht keine Auswahl zur Verfügung.') }}
                            </template>
                            <template #selected-option="option">
                                <studip-icon :shape="option.label" />
                                <span class="vs__option-with-icon">{{ option.label }}</span>
                            </template>
                            <template #option="option">
                                <studip-icon :shape="option.label" />
                                <span class="vs__option-with-icon">{{ option.label }}</span>
                            </template>
                        </StudipSelect>
                    </div>
                </form>
            </template>
            <template #info>
                <p>{{ $gettext('Informationen zum Merksatz-Block') }}</p>
            </template>
        </courseware-default-block>
    </div>
</template>

<script>
import BlockComponents from './block-components.js';
import blockMixin from '@/vue/mixins/courseware/block.js';
import colorMixin from '@/vue/mixins/courseware/colors.js';
import contentIconsMixin from '@/vue/mixins/courseware/content-icons.js';
import { mapActions } from 'vuex';

export default {
    name: 'courseware-key-point-block',
    mixins: [blockMixin, colorMixin, contentIconsMixin],
    components: Object.assign(BlockComponents, {}),
    props: {
        block: Object,
        canEdit: Boolean,
        isTeacher: Boolean,
    },
    data() {
        return {
            currentText: '',
            currentColor: '',
            currentIcon: '',
        };
    },
    computed: {
        file() {
            return `icons/${this.color}/${this.icon}.svg`;
        },
        icons() {
            return this.contentIcons;
        },
        colors() {
             let colors = this.mixinColors.filter(
                (color) => (color.icon && color.class !== 'white' && color.class !== 'studip-lightblue')
                || color.class === 'royal-purple' || color.class === 'apple-green' || color.class === 'pumpkin' || color.class === 'verdigris' || color.class === 'mulberry'
            );

            return colors.map((color) => {
                if (!color.icon) {
                     color.icon = color.class;
                }
                return color;
            });
        },
        text() {
            return this.block?.attributes?.payload?.text;
        },
        color() {
            return this.block?.attributes?.payload?.color;
        },
        icon() {
            return this.block?.attributes?.payload?.icon;
        },
        currentRole() {
            switch (this.currentColor) {
                case 'black':
                    return 'info';

                case 'grey':
                    return 'inactive';

                case 'green':
                    return 'status-green';

                case 'red':
                    return 'status-red';

                case 'white':
                    return 'info_alt';

                case 'yellow':
                    return 'status-yellow';

                case 'blue':
                default:
                    return 'clickable';
            }
        },
        currentHexColor() {
            return this.colors.find((color) => color.class === this.currentColor)?.hex ?? '#000000';
        },
    },
    methods: {
        ...mapActions({
            updateBlock: 'updateBlockInContainer',
        }),
        initCurrentData() {
            this.currentText = this.text;
            this.currentColor = this.color;
            this.currentIcon = this.icon;
        },
        storeBlock() {
            let attributes = {};
            attributes.payload = {};
            attributes.payload.text = this.currentText;
            attributes.payload.color = this.currentColor;
            attributes.payload.icon = this.currentIcon;

            this.updateBlock({
                attributes: attributes,
                blockId: this.block.id,
                containerId: this.block.relationships.container.data.id,
            });
        },
    },
    mounted() {
        this.initCurrentData();
    },
};
</script>
<style scoped lang="scss">
@import '../../../../assets/stylesheets/scss/courseware/blocks/keypoint';
</style>
