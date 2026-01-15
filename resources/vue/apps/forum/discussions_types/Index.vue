<script setup>
import {onMounted, ref} from 'vue';
import {$gettext} from '@/assets/javascripts/lib/gettext';
import StudipIcon from '@/vue/components/StudipIcon.vue';
import {useSortable} from '@/vue/composables/useSortable';
import {getDiscussionTypeEditURL} from '@/vue/components/forum/helpers/urls';
import StudipActionMenu from '@/vue/components/StudipActionMenu.vue';
import {deserializeJSONAPIResponse} from '@/assets/javascripts/lib/jsonapiUtils';
import StudipPagination from '@/vue/components/StudipPagination.vue';
import Loader from '@/vue/components/forum/Loader.vue';

const discussionTypes = ref([]);
const pagination = ref({});
const isLoading = ref(false);

const actionMenusItems = [
    { label: $gettext('Diskussionstyp bearbeiten'),  icon: 'edit', emit: 'edit'},
    { label: $gettext('Diskussionstyp löschen'),  icon: 'trash', emit: 'delete'}
];

const fetchDiscussionTypes = async (_, offset = 0) => {
    try {
        isLoading.value = true;

        const response = await STUDIP.jsonapi.withPromises().GET(
            'forum-discussion-types',
            {
                data: { page: { offset } }
            }
        );

        pagination.value = {
            ...response.meta.page,
            currentPage: response.meta.page.offset / response.meta.page.limit,
            links: response.links
        };

        discussionTypes.value = await deserializeJSONAPIResponse(response);
    } catch (error) {
        STUDIP.Report.error(error);
    } finally {
        isLoading.value = false;
    }
}

const addType = () => {
    STUDIP.Dialog.fromURL(
        getDiscussionTypeEditURL(),
        {
            width: '700',
            height: '650'
        }
    );
}

const editType = id => STUDIP.Dialog.fromURL(
    getDiscussionTypeEditURL(id),
    {
        width: '700',
        height: '650'
    }
);

const deleteType = type => STUDIP.Dialog.confirm(
    $gettext('Wollen Sie „%{ name }“ löschen?', { name: type.name }),
    async () => {
        try {
            await STUDIP.jsonapi.withPromises().DELETE(`forum-discussion-types/${type.id}`);
            discussionTypes.value = discussionTypes.value.filter(({ id }) => id !== type.id);

            STUDIP.Report.success($gettext('Der Diskussionstyp wurde gelöscht.'));
        } catch (error) {
            STUDIP.Report.error(error);
        }
    },
    STUDIP.Dialog.close()
);

const {
    sortedData,
    sortBy,
    getSortClass,
    getAriaSortString,
    getAriaSortLabel
} = useSortable(discussionTypes);

onMounted(() => {
    fetchDiscussionTypes();
});
</script>

<template>
    <div class="forum">
        <table class="default">
            <caption>
                {{ $gettext('Diskussionstypen') }}
                <span class="actions">
                    <button
                        type="button"
                        class="button button--icon-only"
                        @click="addType"
                        :title="$gettext('Neuen Diskussionstyp anlegen')"
                    >
                        <StudipIcon shape="add" aria-hidden="true" />
                    </button>
                </span>
            </caption>

            <colgroup>
                <col style="width: 10%">
                <col>
                <col style="width: 24px">
            </colgroup>

            <thead>
                <tr class="sortable">
                    <th>{{ $gettext('Icon') }}</th>
                    <th
                        :class="getSortClass('name')"
                        :aria-sort="getAriaSortString('name')"
                        :aria-label="getAriaSortLabel('name', $gettext('Name'))"
                    >
                        <button
                            type="button"
                            class="as-link"
                            @click="sortBy('name')"
                            :title="$gettext('Nach Name sortieren')">
                            {{ $gettext('Name') }}
                        </button>
                    </th>

                    <th>{{ $gettext('Aktionen') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="type in sortedData" :key="type.id">
                    <td>
                        <StudipIcon :shape="type.icon" role="info" :size="24" aria-hidden="true" />
                    </td>
                    <td>
                        <button
                            type="button"
                            class="as-link"
                            @click="editType(type.id)"
                            :title="$gettext('Diskussionstyp bearbeiten')"
                        >
                            {{ type.name }}
                        </button>
                    </td>

                    <td class="actions">
                        <StudipActionMenu
                            :context="type.name"
                            :items="actionMenusItems"
                            @edit="editType(type.id)"
                            @delete="deleteType(type)"
                        />
                    </td>
                </tr>

                <tr v-if="isLoading" >
                    <td colspan="3">
                        <Loader />
                    </td>
                </tr>

                <tr v-if="sortedData.length === 0">
                    <td colspan="3" class="text-center">
                        {{ $gettext('Es sind noch keine Diskussionstypen vorhanden.') }}
                    </td>
                </tr>
            </tbody>
        </table>
        <StudipPagination
            v-if="pagination.total > pagination.limit"
            :currentPage="pagination.currentPage"
            :totalItems="pagination.total"
            :itemsPerPage="pagination.limit"
            @pageUpdated="fetchDiscussionTypes" />
    </div>
</template>
