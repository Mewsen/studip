<template>
    <div class="cw-panel-last-visit">
        <h2>{{ $gettext('Kürzlich betrachtet') }}</h2>
        <div class="cw-panel-last-visit-unit">
            <div class="cw-panel-last-visit-unit-description-img" :src="imageURL" :style="image"></div>
            <template v-if="!imageIsSet">
                <studip-ident-image
                    class="cw-panel-last-visit-unit-description-img"
                    v-model="identImage"
                    :baseColor="bgColorHex"
                    :pattern="structuralElement.attributes.title"
                />
            </template>
            <div class="cw-panel-last-visit-unit-description-text" :style="descriptionStyle">
                <h3>{{ structuralElement.attributes.title }}</h3>
                <p>
                    {{ structuralElement.attributes.payload.description }}
                </p>
                <a class="button" :href="link">{{ $gettext('Lesen Sie weiter') }}</a>
            </div>
        </div>
    </div>
</template>

<script>
import StudipIdentImage from './../../StudipIdentImage.vue';
import colorMixin from '@/vue/mixins/courseware/colors.js';
import {  mapGetters } from 'vuex';

export default {
    name: 'CoursewarePanelLastVisit',
    mixins: [colorMixin],
    components: {
        StudipIdentImage,
    },
    props: {
        structuralElement: Object,
    },
    data() {
        return {
            identImage: '',
            identBgImage: '',
        };
    },
    computed: {
        ...mapGetters({
            context: 'context',
        }),
        imageURL() {
            return this.structuralElement.relationships?.image?.meta?.['download-url'];
        },
        imageIsSet() {
            return this.imageURL !== undefined;
        },
        image() {
            let style = {};
            const backgroundURL = this.imageIsSet ? this.imageURL : this.identImage;
            style.backgroundImage = 'url(' + backgroundURL + ')';

            return style;
        },
        bgImage() {
            let style = {};
            const backgroundURL = this.imageIsSet ? this.imageURL : this.identBgImage;

            style.backgroundImage = 'url(' + backgroundURL + ')';
            style.height = this.withTOC ? '300px' : '480px';

            return style;
        },
        bgColorHex() {
            const elementColor = this.structuralElement?.attributes?.payload?.color ?? 'studip-blue';
            const color = this.mixinColors.find((c) => {
                return c.class === elementColor;
            });
            return color.hex;
        },
        descriptionStyle() {
            let style = {};
            style.backgroundColor = this.bgColorHex + '20';

            return style;
        },
        inCourseContext() {
            return this.context.type === 'courses';
        },
        unitId() {
            return this.structuralElement.relationships.unit.data.id;
        },
        link() {
            if (this.inCourseContext) {
                return STUDIP.URLHelper.getURL(`dispatch.php/course/courseware/courseware/${this.unitId}?cid=a07535cf2f8a72df33c12ddfa4b53dde#/structural_element/${this.structuralElement.id}`);
            }
            
            return STUDIP.URLHelper.getURL(`dispatch.php/centents/courseware/courseware/${this.unitId}#/structural_element/${this.structuralElement.id}`);
        }
    },
};
</script>
