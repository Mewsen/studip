<script setup>
import {debounce} from 'lodash';
import Draggable from 'vuedraggable';
import {computed, nextTick, onMounted, ref} from 'vue';
import ForumApp from '@/vue/components/forum/ForumApp.vue';
import { default as CreateCategory } from '@/vue/components/forum/categories/Create.vue';
import CategoryItem from '@/vue/components/forum/categories/CategoryItem.vue';
import {useForumConfig} from '@/vue/store/pinia/forum/ForumConfig';
import {$gettext} from '@/assets/javascripts/lib/gettext';
import StudipIcon from '@/vue/components/StudipIcon.vue';
import {deserializeJSONAPIResponse} from '@/assets/javascripts/lib/jsonapiUtils';
import StudipPagination from '@/vue/components/StudipPagination.vue';
import {useSortable} from '@/vue/composables/useSortable';
import ShowCategory from "@/vue/components/forum/categories/ShowCategory.vue";
import StudipDialog from "@/vue/components/StudipDialog.vue";

const forumConfig = useForumConfig();
const currentCategory = ref(null);
const categories = ref([]);
const pagination = ref({});

const {
    sortedData: sortedCategories,
    sortBy,
    getSortClass,
    getAriaSortString,
    getAriaSortLabel
} = useSortable(categories);

const toggleLayoutMessage = computed(() => {
    if (forumConfig.tileLayout) {
        return $gettext('Kachelansicht aktiviert');
    }

    return $gettext('Tabellarische Ansicht aktiviert');
});

const fetchCategories = async (_, offset = 0) => {
    try {
        const response = await STUDIP.jsonapi.withPromises().GET(
            `courses/${STUDIP.URLHelper.parameters.cid}/forum-categories`,
            {
                data: { page: { offset } }
            }
        );

        pagination.value = {
            ...response.meta.page,
            currentPage: response.meta.page.offset / response.meta.page.limit,
            links: response.links
        };

        categories.value = await deserializeJSONAPIResponse(response);
    } catch (error) {
        STUDIP.Report.error(error);
    }
}

const updateCategoriesOrder = async () => {
    try {
        const categoryIds = sortedCategories.value.map(({ id }) => id);

        const data = {
            attributes: {
                'category-ids': categoryIds
            },
            relationships: {
                range: {
                    data: {
                        type: 'courses',
                        id: STUDIP.URLHelper.parameters.cid
                    }
                }
            }
        };

        await STUDIP.jsonapi.withPromises().PATCH(
            `forum-categories/sort`,
            { data: { data } }
        );
    } catch (error) {
        STUDIP.Report.error(error);
    }
}

onMounted(async () => {
    await fetchCategories();
});

const updateOrderDebounced = debounce(updateCategoriesOrder, 2000);
const assistiveLive = ref('');

const swapCategory = (categoryId, step) => {
    const index = sortedCategories.value.findIndex(({ id }) => id === categoryId);
    const newIndex = index + step;

    if (newIndex < 0 || newIndex >= sortedCategories.value.length) {
        return;
    }

    const temp = sortedCategories.value[newIndex];
    sortedCategories.value[newIndex] = sortedCategories.value[index];
    sortedCategories.value[index] = temp;

    nextTick(() => {
        document.getElementById(`sort-handle-${categoryId}`)?.focus();
        assistiveLive.value = $gettext(
            'Aktuelle Position in der Liste: %{index} von %{length}.',
            { index: newIndex + 1, length: sortedCategories.value.length }
        );

        updateOrderDebounced();
    });
}

const showCategoryDialog = category => currentCategory.value = category;
</script>

<template>
    <ForumApp class="use-utility-classes">
        <header class="header">
            <div class="header__content header__content--with-actions">
                <div>
                    <h2>
                        {{ $gettext('Kategorien') }}
                    </h2>
                </div>

                <div class="actions">
                    <CreateCategory />
                    <button
                        v-if="forumConfig.tileLayout"
                        @click="forumConfig.toggleForumLayout()"
                        type="button"
                        :title="$gettext('Tabellarische Ansicht')" class="button button--icon-only">
                        <StudipIcon shape="view-list" :size="20" />
                    </button>
                    <button
                        v-else
                        @click="forumConfig.toggleForumLayout()"
                        type="button"
                        :title="$gettext('Kachelansicht')" class="button button--icon-only">
                        <StudipIcon shape="view-wall" :size="20" />
                    </button>
                    <div aria-live="polite" class="sr-only" role="status">{{ toggleLayoutMessage }}</div>
                </div>
            </div>
        </header>
        <div class="py-10">
            <span aria-live="assertive" class="sr-only">{{ assistiveLive }}</span>
            <div v-if="forumConfig.tileLayout">
                <draggable
                    v-if="sortedCategories.length"
                    v-model="sortedCategories"
                    item-key="category_id"
                    :animation="200"
                    @end="updateCategoriesOrder"
                    :disabled="!forumConfig.isModerator"
                    class="topic-cards-container"
                    :class="{
                        '--fill-free-space': sortedCategories.length > 1
                    }"
                    handle=".drag-handle"
                    tag="ul">
                    <template #item="{element}">
                        <li>
                            <CategoryItem
                                :category="element"
                                @swapCategory="swapCategory"
                                @showCategory="showCategoryDialog(element)"
                            />
                        </li>
                    </template>
                    <template v-if="forumConfig.isModerator" #footer>
                        <li key="footer">
                            <div class="topic-card topic-card--new-topic">
                                <CreateCategory
                                    class="--with-label"
                                    :label="$gettext('Neue Kategorie anlegen')"
                                />
                            </div>
                        </li>
                    </template>
                </draggable>
                <div v-else-if="forumConfig.isModerator" class="topic-cards-container">
                    <div class="topic-card topic-card--new-topic">
                        <CreateCategory
                            class="--with-label"
                            :label="$gettext('Neue Kategorie anlegen')"
                        />
                    </div>
                </div>
            </div>
            <table v-else class="default forum-table forum-table--topics-index">
                <colgroup>
                    <col>
                    <col style="width: 15%;">
                    <col style="width: 15%;">
                    <col style="width: 15%;">
                    <col style="width: 15%;">
                    <col style="width: 5%">
                </colgroup>
                <thead>
                    <tr class="sortable">
                        <th
                            scope="col"
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
                        <th
                            scope="col"
                            :class="getSortClass('meta.discussions_count')"
                            :aria-sort="getAriaSortString('meta.discussions_count')"
                            :aria-label="getAriaSortLabel('meta.discussions_count', $gettext('Anzahl der Diskussionen'))"
                        >
                            <button
                                type="button"
                                class="as-link"
                                @click="sortBy('meta.discussions_count')"
                                :title="$gettext('Nach Anzahl der Diskussionen sortieren')">
                                {{ $gettext('Diskussionen') }}
                            </button>
                        </th>
                        <th
                            scope="col"
                            :class="getSortClass('meta.users_count')"
                            :aria-sort="getAriaSortString('meta.users_count')"
                            :aria-label="getAriaSortLabel('meta.users_count', $gettext('Anzahl der Teilnehmenden'))"
                        >
                            <button
                                type="button"
                                class="as-link"
                                @click="sortBy('meta.users_count')"
                                :title="$gettext('Nach Anzahl der Teilnehmenden sortieren')">
                                {{ $gettext('Teilnehmende') }}
                            </button>
                        </th>
                        <th
                            scope="col"
                            :class="getSortClass('meta.topics_count')"
                            :aria-sort="getAriaSortString('meta.topics_count')"
                            :aria-label="getAriaSortLabel('meta.topics_count', $gettext('Anzahl der Themen'))"
                        >
                            <button
                                type="button"
                                class="as-link"
                                @click="sortBy('meta.topics_count')"
                                :title="$gettext('Nach Anzahl der Themen sortieren')">
                                {{ $gettext('Themen') }}
                            </button>
                        </th>
                        <th
                            scope="col"
                            :class="getSortClass('meta.recent_activity')"
                            :aria-sort="getAriaSortString('meta.recent_activity')"
                            :aria-label="getAriaSortLabel('meta.recent_activity', $gettext('Letzte Aktivität'))"
                        >
                            <button
                                type="button"
                                class="as-link"
                                @click.prevent="sortBy('meta.recent_activity')"
                                :title="$gettext('Nach letzter Aktivität sortieren')">
                                {{ $gettext('Letzte Aktivität') }}
                            </button>
                        </th>
                        <th scope="col">
                            <span class="sr-only">{{ $gettext('Aktionen') }}</span>
                        </th>
                    </tr>
                </thead>
                <Draggable
                    v-if="sortedCategories.length"
                    v-model="sortedCategories"
                    item-key="category_id"
                    :animation="200"
                    @end="updateCategoriesOrder"
                    :disabled="!forumConfig.isModerator"
                    handle=".drag-handle"
                    tag="tbody">
                    <template #item="{element}">
                        <CategoryItem
                            renderType="tr"
                            :category="element"
                            @swapCategory="swapCategory"
                            @showCategory="showCategoryDialog(element)"
                        />
                    </template>
                </Draggable>
                <tbody v-else>
                    <tr>
                        <td colspan="6">
                            {{ $gettext('Keine Kategorien vorhanden.') }}
                        </td>
                    </tr>
                </tbody>
                <tfoot v-if="forumConfig.isModerator">
                    <tr class="new-topic">
                        <td colspan="6">
                            <div class="footer-actions-container">
                                <CreateCategory
                                    class="--with-label"
                                    :label="$gettext('Neue Kategorie anlegen')"
                                />
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <StudipPagination
                v-if="pagination.total > pagination.limit"
                :currentPage="pagination.currentPage"
                :totalItems="pagination.total"
                :itemsPerPage="pagination.limit"
                @pageUpdated="fetchCategories" />
        </div>

        <StudipDialog
            v-if="currentCategory?.id"
            :title="$gettext('Detaillierte Information')"
            :closeText="$gettext('Schließen')"
            height="700"
            width="600"
            @close="currentCategory = null"
        >
            <template #dialogContent>
                <div class="forum">
                    <ShowCategory :category="currentCategory" />
                </div>
            </template>
        </StudipDialog>
    </ForumApp>
</template>
