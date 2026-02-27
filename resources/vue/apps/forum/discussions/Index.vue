<script setup>
import {onMounted, ref} from 'vue';
import StudipIcon from '@/vue/components/StudipIcon.vue';
import ForumApp from '@/vue/components/forum/ForumApp.vue';
import StudipDateTime from '@/vue/components/StudipDateTime.vue';
import StudipPagination from '@/vue/components/StudipPagination.vue';
import DiscussionIndex from '@/vue/components/forum/discussions/DiscussionIndex.vue';
import { default as CreateDiscussion } from '@/vue/components/forum/discussions/Create.vue';
import {$gettext} from '@/assets/javascripts/lib/gettext';
import {deserializeJSONAPIResponse} from '@/assets/javascripts/lib/jsonapiUtils';

defineProps({
    metadata: {
        type: Object,
        required: true
    }
});

const discussions = ref([]);
const isLoading = ref(false);
const pagination = ref({});

const fetchDiscussions = async (_, offset = 0) => {
    try {
        isLoading.value = true;

        const response = await STUDIP.jsonapi.withPromises().GET(
            `courses/${STUDIP.URLHelper.parameters.cid}/forum-discussions`,
            {
                data: { include: 'category,discussion-type,members,tags,user&fields[users]=id', page: { offset } }
            }
        );

        pagination.value = {
            ...response.meta.page,
            currentPage: response.meta.page.offset / response.meta.page.limit,
            links: response.links
        };

        discussions.value = await deserializeJSONAPIResponse(response);
    } catch (error) {
        STUDIP.Report.error(error);
    } finally {
        isLoading.value = false;
    }
}

onMounted(async () => {
    await fetchDiscussions();
});
</script>

<template>
    <ForumApp class="use-utility-classes forum">
        <header class="header">
            <div class="header__content header__content--with-actions items-start">
                <div>
                    <h2>
                        {{ $gettext('Alle Diskussionen') }}
                    </h2>

                    <div class="mt-10 inline-flex gap-20 items-center">
                        <span
                            role="group"
                            class="inline-flex gap-5 items-center"
                            :title="$gettext('Anzahl der Teilnehmenden')"
                            :aria-label="$gettext('Anzahl der Teilnehmenden')"
                        >
                            <StudipIcon shape="community2" role="info" :size="15" aria-hidden="true" />
                            <span class="sr-only">{{ $gettext('Anzahl der Teilnehmenden') }}:</span>
                            <small>{{ metadata.users_count }}</small>
                        </span>

                        <span
                            role="group"
                            class="inline-flex gap-5 items-center"
                            :title="$gettext('Anzahl der Beiträge')"
                            :aria-label="$gettext('Anzahl der Beiträge')"
                        >
                            <StudipIcon shape="post" role="info" :size="15" aria-hidden="true" />
                            <span class="sr-only">{{ $gettext('Anzahl der Beiträge') }}:</span>
                            <small>{{ metadata.postings_count }}</small>
                        </span>

                        <span
                            role="group"
                            class="inline-flex gap-5 items-center"
                            :title="$gettext('Letzte Aktivität')"
                            :aria-label="$gettext('Letzte Aktivität')"
                        >
                            <StudipIcon shape="activity" role="info" :size="15" aria-hidden="true" />
                            <StudipDateTime v-if="metadata.recent_activity" :iso="metadata.recent_activity" :relative="true" />
                            <template v-else>{{ $gettext('Keine Aktivität') }}</template>
                        </span>
                    </div>
                </div>

                <div class="actions">
                    <CreateDiscussion />
                </div>
            </div>
        </header>
        <div class="py-10">
            <DiscussionIndex :discussions="discussions" :isLoading="isLoading" redirect="discussions">
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
