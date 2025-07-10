<script setup>
import {onMounted, ref} from "vue";
import ForumApp from "@/vue/components/forum/ForumApp.vue";
import { default as CreateDiscussion } from "@/vue/components/forum/discussions/Create.vue";
import DiscussionIndex from "@/vue/components/forum/discussions/DiscussionIndex.vue";
import {getCategoryURL} from "@/vue/components/forum/helpers/urls";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipIcon from "../../../components/StudipIcon.vue";
import StudipDateTime from "../../../components/StudipDateTime.vue";
import SubscriptionDropdown from "@/vue/components/forum/SubscriptionDropdown.vue";
import {deserializeJSONAPIResponse} from "../../../../assets/javascripts/lib/jsonapiUtils";
import StudipPagination from "../../../components/StudipPagination.vue";

const props = defineProps({
    topic: {
        type: Object,
        required: true,
    },
    category: {
        type: Object,
        required: true,
    },
    metadata: {
        type: Object,
        required: true,
    },
    user_subscription: {
        type: Object,
        required: true
    },
});

const discussions = ref([]);
const isLoading = ref(false);
const pagination = ref({});

const fetchDiscussions = async (_, offset = 0) => {
    try {
        isLoading.value = true;

        const response = await STUDIP.jsonapi.withPromises().GET(
            `forum-topics/${props.topic.topic_id}/discussions`,
            {
                data: { include: 'category,discussion-type,members,tags', page: { offset } }
            }
        );

        pagination.value = {
            ...response.meta.page,
            currentPage: response.meta.page.offset / response.meta.page.limit,
            links: response.links
        };

        discussions.value = await deserializeJSONAPIResponse(response);
    } catch (error) {
        STUDIP.Report.error(error.statusText);
    } finally {
        isLoading.value = false;
    }
}

onMounted(async () => {
    await fetchDiscussions();
})
</script>

<template>
    <ForumApp class="use-utility-classes forum">
        <header class="header">
            <div v-if="category.color" class="flag" :style="{ backgroundColor: category.color}"></div>
            <div class="header__content header__content--with-actions items-start">
                <div>
                    <ul class="breadcrumb">
                        <li v-if="category.category_id">
                            <a :href="getCategoryURL(category.category_id)" :title="$gettext('Zur Kategorie')">
                                {{ category.name }}
                            </a>
                        </li>
                        <li>{{ topic.name }}</li>
                    </ul>

                    <div class="mt-10 inline-flex gap-20 items-center">
                        <span class="inline-flex gap-5 items-center" :title="$gettext('Anzahl der Teilnehmenden am Thema')" :aria-label="$gettext('Anzahl der Teilnehmenden am Thema')" role="group">
                            <StudipIcon shape="community2" role="info" :size="15" aria-hidden="true" />
                            <small>{{ metadata.users_count }}</small>
                        </span>
                        <span class="inline-flex gap-5 items-center" :title="$gettext('Anzahl der Beiträge')" :aria-label="$gettext('Anzahl der Beiträge')" role="group">
                            <StudipIcon shape="reply" role="info" :size="15" aria-hidden="true"/>
                            <small>{{ metadata.postings_count }}</small>
                        </span>
                        <span class="inline-flex gap-5 items-center" :title="$gettext('Letzte Aktivität')" :aria-label="$gettext('Letzte Aktivität')" role="group">
                            <StudipIcon shape="activity" role="info" :size="15" aria-hidden="true" />
                            <StudipDateTime v-if="metadata.recent_activity" :iso="metadata.recent_activity" :relative="true" />
                            <template v-else>{{ $gettext('Keine Aktivität') }}</template>
                        </span>
                    </div>
                </div>

                <div class="actions">
                    <CreateDiscussion :topic_id="topic.topic_id" />
                    <SubscriptionDropdown
                        :title="$gettext('Thema abonnieren')"
                        :subject="{
                            id: topic.topic_id,
                            type: 'forum-topics'
                        }"
                        :user_subscription="user_subscription"
                    />
                </div>
            </div>
        </header>
        <div class="py-10">
            <DiscussionIndex :discussions="discussions" :isLoading="isLoading">
                <template #pagination>
                    <tfoot v-if="pagination && pagination.total > pagination.limit">
                        <tr>
                            <td colspan="7">
                                <StudipPagination
                                    :currentPage="pagination.currentPage"
                                    :totalItems="pagination.total"
                                    :itemsPerPage="pagination.limit"
                                    @pageUpdated="fetchDiscussions" />
                            </td>
                        </tr>
                    </tfoot>
                </template>
            </DiscussionIndex>
        </div>
    </ForumApp>
</template>
