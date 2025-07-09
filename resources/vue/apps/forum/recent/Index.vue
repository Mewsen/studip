<script setup>
import {onMounted, ref} from "vue";
import ForumApp from "@/vue/components/forum/ForumApp.vue";
import DiscussionIndex from "@/vue/components/forum/discussions/DiscussionIndex.vue";
import {deserializeJSONAPIResponse} from "../../../../assets/javascripts/lib/jsonapiUtils";
import StudipPagination from "../../../components/StudipPagination.vue";

const props = defineProps({
    last_visit: {
        type: Number,
        required: true
    }
});

const discussions = ref([]);
const pagination = ref({});
const isLoading = ref(false);

const fetchDiscussions = async (_, offset = 0) => {
    try {
        isLoading.value = true;
        const response = await STUDIP.jsonapi.withPromises().GET(
            `courses/${STUDIP.URLHelper.parameters.cid}/forum-discussions`,
            {
                data: {
                    include: 'category,discussion-type,members,tags',
                    filter: {
                        'last-visit': props.last_visit
                    },
                    page: { offset }
                }
            }
        );

        pagination.value = {
            ...response.meta.page,
            currentPage: response.meta.page.offset / response.meta.page.limit,
            links: response.links
        };

        discussions.value = await deserializeJSONAPIResponse(response)
    } catch (error) {
        STUDIP.Report.error(error.statusText);
    } finally {
        isLoading.value = false;
    }
}

onMounted(async () => {
    await fetchDiscussions();
});
</script>

<template>
    <ForumApp class="use-utility-classes">
        <DiscussionIndex :discussions="discussions" :withActions="false" :isLoading="isLoading">
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
    </ForumApp>
</template>
