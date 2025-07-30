<script setup>
import CreateTopic from "@/vue/components/forum/topics/CreateTopic.vue";
import {useForumConfig} from "../../../store/pinia/forum/ForumConfig";
import StudipIcon from "../../../components/StudipIcon.vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import ForumApp from "@/vue/components/forum/ForumApp.vue";
import TopicsIndex from "@/vue/components/forum/topics/TopicsIndex.vue";
import {computed, onMounted, ref} from "vue";
import {deserializeJSONAPIResponse} from "../../../../assets/javascripts/lib/jsonapiUtils";
import StudipPagination from "../../../components/StudipPagination.vue";
import {topicTransformer} from "../../../components/forum/helpers/transformers";
import EmptyForum from "../../../components/forum/EmptyForum.vue";

const forumConfig = useForumConfig();

const toggleLayoutMessage = computed(() => {
    if (forumConfig.tileLayout) {
        return $gettext('Kachelansicht aktiviert');
    }

    return $gettext('Tabellarische Ansicht aktiviert');
});

const topics = ref([]);
const isLoading = ref(false);
const pagination = ref({});
const fetchTopics = async (_, offset = 0) => {
    try {
        isLoading.value = true;

        const response = await STUDIP.jsonapi.withPromises().GET(
            `courses/${STUDIP.URLHelper.parameters.cid}/forum-topics`,
            { data: { include: 'category', page: { offset } } }
        );

        pagination.value = {
            ...response.meta.page,
            currentPage: response.meta.page.offset / response.meta.page.limit,
            links: response.links
        };

        const data = await deserializeJSONAPIResponse(response);
        topics.value = data.map(topicTransformer);
    } catch (error) {
        STUDIP.Report.error(error.statusText);
    } finally {
        isLoading.value = false;
    }
}

onMounted(async () => {
    await fetchTopics();
});
</script>

<template>
    <ForumApp>
        <EmptyForum v-if="topics.length === 0 && !isLoading" />
        <template v-else>
            <header class="header">
                <div class="header__content header__content--with-actions">
                    <h2>
                        {{ $gettext('Themen') }}
                    </h2>
                    <div class="actions">
                        <CreateTopic />
                        <button
                            v-if="forumConfig.tileLayout"
                            @click="forumConfig.toggleForumLayout();"
                            :title="$gettext('Tabellarische Ansicht')"
                            type="button"
                            class="button button--icon-only">
                            <StudipIcon shape="view-list" :size="20" />
                        </button>
                        <button
                            v-else
                            @click="forumConfig.toggleForumLayout();"
                            :title="$gettext('Kachelansicht')"
                            type="button"
                            class="button button--icon-only">
                            <StudipIcon shape="view-wall" :size="20" />
                        </button>
                        <div aria-live="polite" class="sr-only" role="status">{{ toggleLayoutMessage }}</div>
                    </div>
                </div>
            </header>
            <div class="py-10">
                <TopicsIndex :topics="topics" :isLoading="isLoading" :showEmptyForumLayout="true">
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
        </template>
    </ForumApp>
</template>
