<template>
    <a v-if="element" class="cw-tools-units-item-header" :href="url">
        <studip-ident-image v-model="identimage" :baseColor="headerColor.hex" :pattern="element.attributes.title" />
        <div class="cw-tools-units-item-header-image" :style="headerImageStyle"></div>
        <div class="cw-tools-units-item-header-details">
            <header>{{ element.attributes.title }}</header>
            <p>{{ element.attributes.payload.description }}</p>
        </div>
    </a>
</template>

<script>
import StudipIdentImage from '../../StudipIdentImage.vue';
import colorMixin from '@/vue/mixins/courseware/colors.js';
import { mapGetters } from 'vuex';

export default {
    name: 'CoursewareToolsUnitsItem',
    mixins: [colorMixin],
    components: {
        StudipIdentImage,
    },
    props: {
        unit: Object,
        element: Object,
    },
    data() {
        return {
            identimage: '',
        };
    },
    computed: {
        ...mapGetters({
            context: 'context',
        }),
        headerImageUrl() {
            return this.element.relationships?.image?.meta?.['download-url'];
        },
        headerImageStyle() {
            if (this.headerImageUrl) {
                return { 'background-image': 'url(' + this.headerImageUrl + ')' };
            }
            return { 'background-image': 'url(' + this.identimage + ')' };
        },
        headerColor() {
            const rootColor = this.element?.attributes?.payload?.color ?? 'studip-blue';
            return this.mixinColors.find((color) => color.class === rootColor);
        },
        inCourseContext() {
            return this.context.type === 'courses';
        },
        url() {
            if (this.inCourseContext) {
                return STUDIP.URLHelper.getURL('dispatch.php/course/courseware/courseware/' + this.unit.id, {
                    cid: this.context.id,
                });
            } else {
                return STUDIP.URLHelper.getURL('dispatch.php/contents/courseware/courseware/' + this.unit.id);
            }
        },
    }
};
</script>
<style lang="scss">
.cw-tools-units-item-header {
    display: flex;
    flex-direction: row;
    height: 100px;
    margin-top: 8px;
    .cw-tools-units-item-header-image {
        height: 100px;
        width: 150px;
        min-width: 150px;
        background-size: 100% auto;
        background-repeat: no-repeat;
        background-position: center;
        background-color: var(--content-color-20);
    }

    .cw-tools-units-item-header-details {
        margin: 0 8px;
        display: -webkit-box;
        overflow: hidden;
        height: 100px;
        -webkit-line-clamp: 5;
        -webkit-box-orient: vertical;
        header {
            margin: 0 0 6px 0;
            font-size: 16px;
            line-height: 16px;
        }
        p {
            margin: 0;
            color: var(--black);
        }
    }
}
</style>
