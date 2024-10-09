<template>
    <li>
        <div class="cw-tree-unit-link">
            <a :href="url">
                <div class="cw-tree-units-header">
                    <studip-ident-image
                        v-model="identimage"
                        :baseColor="color.hex ?? '#fff'"
                        :pattern="rootElement.title ?? '-'"
                    />
                    <div class="cw-tree-units-header-image" :style="style"></div>
                    <div class="cw-tree-units-header-details">
                        <header>
                            {{ title }}
                        </header>
                        <p>{{ description }}</p>
                    </div>
                </div>
            </a>
            <button v-if="canEditRoot" class="button trash" :title="$gettext('Link entfernen')" @click.prevent="removeUnitLink">
            </button>
        </div>
    </li>
</template>

<script>
import StudipIdentImage from './../../StudipIdentImage.vue';
import colorMixin from '@/vue/mixins/courseware/colors.js';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'CoursewareTreeUnit',
    mixins: [colorMixin],
    components: {
        StudipIdentImage,
    },
    props: {
        unit: {
            type: Object,
            required: true,
        },
        canEditRoot: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            identimage: '',
        };
    },
    computed: {
        ...mapGetters({
            context: 'context',
            structuralElementById: 'courseware-structural-elements/byId',
        }),
        rootElement() {
            return this.structuralElementById({
                id: this.unit.relationships['structural-element'].data.id,
            });
        },
        title() {
            return this.rootElement.attributes.title;
        },
        description() {
            return this.rootElement.attributes.payload.description;
        },
        url() {
            return STUDIP.URLHelper.getURL('dispatch.php/course/courseware/courseware/' + this.unit.id, {
                cid: this.context.id,
            });
        },
        color() {
            return this.mixinColors.find((color) => color.class === this.rootElement.attributes.payload.color);
        },
        style() {
            const imageUrl = this.rootElement.relationships?.image?.meta?.['download-url'];
            if (imageUrl) {
                return { 'background-image': 'url(' + imageUrl + ')' };
            }

            return { 'background-image': 'url(' + this.identimage + ')' };
        },
    },
    methods: {
        removeUnitLink() {
            this.$emit('removeUnitLink', this.unit.id);
        },
    },
};
</script>
