<template>
    <div class="cw-shared-items">
        <h2 v-if="sharedElements.length > 0">{{ $gettext('Geteilte Lernmaterialien') }}</h2>
        <ul class="cw-tiles">
            <CoursewareTile
                v-for="element in sharedElements"
                :key="element.id"
                tag="li"
                :color="element.attributes.payload.color"
                :title="element.attributes.title"
                :descriptionLink="getSharedElementUrl(element.id)"
                :descriptionTitle="$gettext('Lernmaterial öffnen')"
                :displayProgress="false"
                :progress="0"
                :imageUrl="element.relationships.image?.meta?.['download-url']"
            >
                <template #image-overlay>
                    {{ getOwnerName(element) }}
                </template>
                <template #description>
                    {{ element.attributes.payload.description }}
                </template>
            </CoursewareTile>
        </ul>
    </div>
</template>
<script>
import { mapGetters } from 'vuex';
import CoursewareTile from '../layouts/CoursewareTile.vue';

export default {
    name: 'courseware-shared-items',
    components: {
        CoursewareTile,
    },
    computed: {
        ...mapGetters({
            sharedElements: 'courseware-structural-elements-shared/all',
            userById: 'users/byId',
        }),
    },
    methods: {
        getChildStyle(child) {
            let url = child.relationships?.image?.meta?.['download-url'];

            if (url) {
                return { 'background-image': 'url(' + url + ')' };
            } else {
                return {};
            }
        },
        getSharedElementUrl(elementId) {
            return (
                STUDIP.URLHelper.base_url + 'dispatch.php/contents/courseware/shared_content_courseware/' + elementId
            );
        },
        getOwnerName(element) {
            const ownerId = element.relationships.owner.data.id;
            const owner = this.userById({ id: ownerId });

            return owner.attributes['formatted-name'];
        },
    },
};
</script>
