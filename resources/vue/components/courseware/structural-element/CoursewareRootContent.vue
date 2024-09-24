<template>
    <div class="cw-root-content-hint" v-if="hideRoot">
        <courseware-companion-box
            :msgCompanion="
                $gettext(
                    'In diesem Lernmaterial wird die Startseite ausgeblendet. Dies können Sie in den Einstellungen des Lernmaterials ändern. Wenn Sie die Einstellung beibehalten wollen, legen Sie bitte eine Seite an.'
                )
            "
        >
            <template v-slot:companionActions>
                <button v-if="canEdit" class="button" @click="addPage">
                    {{ $gettext('Eine Seite hinzufügen') }}
                </button>
            </template>
        </courseware-companion-box>
    </div>
    <div v-else class="cw-root-content-wrapper">
        <div class="cw-root-content" :class="['cw-root-content-' + rootLayout]">
            <div class="cw-root-content-img" :style="bgImage">
                <section class="cw-root-content-description" :style="bgColor">
                    <div class="cw-root-content-description-img" :src="imageURL" :style="image"></div>
                    <template v-if="!imageIsSet">
                        <studip-ident-image
                            class="cw-root-content-description-img"
                            v-model="identImage"
                            :baseColor="bgColorHex"
                            :pattern="structuralElement.attributes.title"
                        />
                        <studip-ident-image
                            v-model="identBgImage"
                            class="cw-root-content-description-background-img"
                            :width="4380"
                            :height="withTOC ? 1200 : 1920"
                            :baseColor="bgColorHex"
                            :pattern="structuralElement.attributes.title"
                        />
                    </template>
                    <div class="cw-root-content-description-text">
                        <h1>{{ structuralElement.attributes.title }}</h1>
                        <p>
                            {{ structuralElement.attributes.payload.description }}
                        </p>
                    </div>
                </section>
            </div>
        </div>
        <div v-if="withTOC" class="cw-root-content-toc">
            <ul class="cw-tiles">
                <li v-for="child in childElements" :key="child.id">
                    <router-link :to="'/structural_element/' + child.id" :title="child.attributes.title">
                        <courseware-tile
                            tag="div"
                            :color="child.attributes.payload.color"
                            :title="child.attributes.title || '–'"
                            :imageUrl="hasImage(child) ? child.relationships?.image?.meta?.['download-url'] : ''"
                        >
                            <template #description>
                                {{ child.attributes.payload.description }}
                            </template>
                            <template #footer>
                                <p class="cw-root-content-toc-tile-footer">
                                {{
                                    $gettextInterpolate(
                                        $ngettext('%{pages} Seite', '%{pages} Seiten', countChildChildren(child)),
                                        { pages: countChildChildren(child) }
                                    )
                                }}
                                </p>
                            </template>
                        </courseware-tile>
                    </router-link>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
import CoursewareCompanionBox from './../layouts/CoursewareCompanionBox.vue';
import CoursewareTile from './../layouts/CoursewareTile.vue';
import StudipIdentImage from './../../StudipIdentImage.vue';
import colorMixin from '@/vue/mixins/courseware/colors.js';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-root-content',
    mixins: [colorMixin],
    components: {
        CoursewareCompanionBox,
        CoursewareTile,
        StudipIdentImage,
    },
    props: {
        structuralElement: Object,
        canEdit: Boolean,
    },
    data() {
        return {
            identImage: '',
            identBgImage: '',
        };
    },
    computed: {
        ...mapGetters({
            rootLayout: 'rootLayout',
            childrenById: 'courseware-structure/children',
            structuralElementById: 'courseware-structural-elements/byId',
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
        bgColor() {
            return { 'background-color': this.bgColorHex };
        },
        withTOC() {
            return this.rootLayout === 'toc';
        },
        hideRoot() {
            return this.rootLayout === 'none';
        },
        childElements() {
            return this.childrenById(this.structuralElement.id).map((id) => this.structuralElementById({ id }));
        },
    },
    methods: {
        ...mapActions({
            showElementAddDialog: 'showElementAddDialog',
        }),
        getChildStyle(child) {
            let url = child.relationships?.image?.meta?.['download-url'];

            if (url) {
                return { 'background-image': 'url(' + url + ')' };
            } else {
                return {};
            }
        },
        countChildChildren(child) {
            return this.childrenById(child.id).length + 1;
        },
        hasImage(child) {
            return child.relationships?.image?.data !== null;
        },
        getColor(child) {
            return this.mixinColors.find((color) => color.class === child.attributes.payload.color);
        },
        addPage() {
            this.showElementAddDialog(true);
        },
    },
};
</script>
<style scoped lang="scss">
.cw-root-content {
    max-width: 1095px;
    margin-bottom: 1em;
    overflow: hidden;
    .cw-root-content-img {
        background-position: center;
        background-size: cover;
        background-repeat: no-repeat;
    }
    .cw-root-content-description {
        display: flex;
        position: relative;
        flex-direction: column;
        margin: 0 1em;
        padding: 1em 4px 1em 1em;
        top: 1em;
        gap: 10px;

        .cw-root-content-description-img {
            min-width: 135px;
            height: 90px;
            background-color: #fff;
            background-size: cover;
            background-position: center;
            margin-right: 1em;
        }
        .cw-root-content-description-text {
            max-height: calc(480px - 18em);
            overflow-y: auto;
            &::-webkit-scrollbar {
                width: 2px;
            }

            &::-webkit-scrollbar-track {
                box-shadow: inset 0 0 6px rgba(255, 255, 255, 0.3);
            }

            &::-webkit-scrollbar-thumb {
                background-color: rgba(0, 0, 0, 0.4);
            }
            h1,
            p {
                color: #fff;
                margin-right: 2em;
            }
        }
    }
}
.cw-root-content-toc {
    max-width: 1095px;
    margin-bottom: 1em;
    .cw-root-content-description {
        height: calc(100% - 4em);
        .cw-root-content-description-text {
            max-height: calc(300px - 6em);
        }
    }
    .cw-root-content-toc-tile-footer {
        line-height: 4em;
    }
}
.cw-root-content-hint {
    max-width: 1095px;
}

.size-small {
    .cw-root-content-description {
        flex-direction: row;
        padding: 1em 4px 1em 1em;

        .cw-root-content-description-img {
            min-width: 135px;
            height: 90px;
        }
    }

    .cw-root-content-default {
        .cw-root-content-description {
            margin: 0 4em;
            top: 8em;
        }
    }
    .cw-root-content-toc {
        .cw-root-content-description {
            height: calc(100% - 6em);
            margin: 0 4em;
            top: 1.5em;
        }
    }
}

.size-large {
    .cw-root-content-description {
        flex-direction: row;
        padding: 2em 4px 2em 2em;

        .cw-root-content-description-img {
            min-width: 270px;
            height: 180px;
        }
    }

    .cw-root-content-default {
        .cw-root-content-description {
            margin: 0 8em;
            top: 8em;
        }
    }
    .cw-root-content-toc {
        .cw-root-content-description {
            height: calc(100% - 7em);
            margin: 0 8em;
            top: 1.5em;
        }
    }
}
</style>
