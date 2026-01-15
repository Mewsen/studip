<script setup>
import {computed, onMounted, ref} from 'vue';
import ForumApp from '@/vue/components/forum/ForumApp.vue';
import {useForumConfig} from '@/vue/store/pinia/forum/ForumConfig';
import StudipIcon from '@/vue/components/StudipIcon.vue';
import StudipDateTime from '@/vue/components/StudipDateTime.vue';
import TopicsIndex from '@/vue/components/forum/topics/TopicsIndex.vue';
import CreateTopic from '@/vue/components/forum/topics/CreateTopic.vue';
import {$gettext} from '@/assets/javascripts/lib/gettext';
import {deserializeJSONAPIResponse} from '@/assets/javascripts/lib/jsonapiUtils';
import StudipPagination from '@/vue/components/StudipPagination.vue';

const forumConfig = useForumConfig();

const props = defineProps({
    category: {
        type: Object,
        required: true
    },
    metadata: {
        type: Object,
        required: true
    }
});

const topics = ref([]);
const isLoading = ref(false);
const pagination = ref({});

const toggleLayoutMessage = computed(() => {
    if (forumConfig.tileLayout) {
        return $gettext('Kachelansicht aktiviert');
    }

    return $gettext('Tabellarische Ansicht aktiviert');
});
const fetchTopics = async (_, offset = 0) => {
    try {
        isLoading.value = true;

        const response = await STUDIP.jsonapi.withPromises().GET(
            `forum-categories/${props.category.category_id}/topics`,
            { data: { page: { offset } } }
        );

        pagination.value = {
            ...response.meta.page,
            currentPage: response.meta.page.offset / response.meta.page.limit,
            links: response.links
        };

        topics.value = await deserializeJSONAPIResponse(response);
    } catch (error) {
        STUDIP.Report.error(error);
    } finally {
        isLoading.value = false;
    }
}

onMounted(async () => {
    await fetchTopics();
})
</script>

<template>
    <ForumApp class="use-utility-classes forum">
        <header class="header">
            <div v-if="category.color" class="flag" :style="{ backgroundColor: category.color}"></div>
            <div class="header__content header__content--with-actions items-start">
                <div>
                    <h2>
                        {{ category.name }}
                    </h2>
                    <div class="mt-10 inline-flex gap-20 items-center">
                        <span class="inline-flex gap-5 items-center" :title="$gettext('Anzahl der Teilnehmenden an der Diskussion')" role="group">
                            <StudipIcon shape="community2" :size="15" role="info" aria-hidden="true" />
                            <span class="sr-only">{{ $gettext('Anzahl der Teilnehmenden an der Diskussion') }}:</span>
                            <small>{{ metadata.users_count }}</small>
                        </span>
                        <span class="inline-flex gap-5 items-center" :title="$gettext('Anzahl der Beiträge')" role="group">
                            <StudipIcon shape="reply" :size="15" role="info" aria-hidden="true" />
                            <span class="sr-only">{{ $gettext('Anzahl der Beiträge') }}:</span>
                            <small>{{ metadata.postings_count }}</small>
                        </span>
                        <span class="inline-flex gap-5 items-center" :title="$gettext('Letzte Aktivität')" role="group">
                            <StudipIcon shape="activity" :size="15" role="info" aria-hidden="true" />
                            <span class="sr-only">{{ $gettext('Letzte Aktivität') }}:</span>
                            <StudipDateTime v-if="metadata.recent_activity" :iso="metadata.recent_activity" :relative="true" />
                            <template v-else>{{ $gettext('Keine Aktivität') }}</template>
                        </span>
                    </div>
                </div>

                <div class="actions">
                    <CreateTopic :category_id="category.category_id" />
                    <button
                        v-if="forumConfig.tileLayout"
                        @click="forumConfig.toggleForumLayout()"
                        type="button"
                        :title="$gettext('Tabellarische Ansicht')"
                        class="button button--icon-only">
                        <StudipIcon shape="view-list" :size="20" />
                    </button>
                    <button
                        v-else
                        @click="forumConfig.toggleForumLayout()"
                        type="button"
                        :title="$gettext('Kachelansicht')"
                        class="button button--icon-only">
                        <StudipIcon shape="view-wall" :size="20" />
                    </button>
                    <div aria-live="polite" class="sr-only" role="status">{{ toggleLayoutMessage }}</div>
                </div>
            </div>
        </header>
        <div class="py-10">
            <TopicsIndex :topics="topics" :isLoading="isLoading" :categoryId="category.category_id">
                <template #pagination>
                    <StudipPagination
                        v-if="pagination.total > pagination.limit"
                        :currentPage="pagination.currentPage"
                        :totalItems="pagination.total"
                        :itemsPerPage="pagination.limit"
                        @pageUpdated="fetchTopics" />
                </template>
            </TopicsIndex>
        </div>
    </ForumApp>
</template>
