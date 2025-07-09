<script setup>
import {getDiscussionURL, getSearchURL} from "../helpers/urls";
import {numberFormatter} from "../../../../assets/javascripts/lib/number_formatter";
import ForumMembers from "../ForumMembers.vue";
import {useSortable} from "../../../composables/useSortable";
import {onMounted, toRef} from "vue";
import StudipIcon from "@/vue/components/StudipIcon.vue";
import StudipDateTime from "@/vue/components/StudipDateTime.vue";
import StudipActionMenu from "@/vue/components/StudipActionMenu.vue";
import {useForumConfig} from "../../../store/pinia/forum/ForumConfig";
import {$gettext} from "@/assets/javascripts/lib/gettext";
import Loader from "../Loader.vue";

const forumConfig = useForumConfig();

const props = defineProps({
    discussions: {
        type: Array,
        required: true
    },
    withActions: {
        type: Boolean,
        default: true
    },
    redirect: {
        type: String,
        default: 'topic'
    },
    isLoading: {
        type: Boolean,
        default: false
    }
});

const discussionsRef = toRef(props, 'discussions');

const {
    sortedData,
    sortBy,
    getSortClass,
    getAriaSortString,
    getAriaSortLabel
} = useSortable(discussionsRef);

const getActionMenusItems = () => {
    if (forumConfig.isModerator) {
        return [
            { label: $gettext('Bearbeiten'),  icon: 'edit', emit: 'edit'},
            { label: $gettext('Löschen'),  icon: 'trash', emit: 'delete'}
        ];
    }

    return [];
}

const editDiscussion = id => STUDIP.Dialog.fromURL(
    STUDIP.URLHelper.getURL(`dispatch.php/course/forum/discussions/edit/${id}`),
    {
        width: '900'
    }
);

const deleteDiscussion = id => STUDIP.Dialog.confirm(
    $gettext('Wollen Sie diese Diskussion löschen? Damit werden auch alle Beiträge gelöscht!'),
    () => {
        window.location = STUDIP.URLHelper.getURL(`dispatch.php/course/forum/discussions/delete/${id}`);
    },
    STUDIP.Dialog.close()
);

onMounted(() => {
    sortBy('meta.recent_activity', 'desc');
});
</script>

<template>
    <table class="default forum-table --discussions-index">
        <colgroup>
            <col>
            <col style="width: 15%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col style="width: 10%;">
            <col style="width: 5%">
            <col v-if="withActions" style="width: 10%">
        </colgroup>
        <thead>
            <tr class="sortable">
                <th
                    :class="getSortClass('title')"
                    :aria-sort="getAriaSortString('title')"
                    :aria-label="getAriaSortLabel('title', $gettext('Diskussionstitel'))"
                >
                    <a
                        href="#"
                        @click.prevent="sortBy('title')"
                        :title="$gettext('Nach Diskussionstitel sortieren')">
                        {{ $gettext('Diskussion') }}
                    </a>
                </th>
                <th
                    :class="getSortClass('members')"
                    :aria-sort="getAriaSortString('members')"
                    :aria-label="getAriaSortLabel('members', $gettext('Anzahl der Teilnehmenden'))"
                >
                    <a
                        href="#"
                        @click.prevent="sortBy('members')"
                        :title="$gettext('Nach Anzahl der Teilnehmenden sortieren')">
                        {{ $gettext('Teilnehmende') }}
                    </a>
                </th>
                <th
                    :class="getSortClass('meta.postings_count')"
                    :aria-sort="getAriaSortString('meta.postings_count')"
                    :aria-label="getAriaSortLabel('meta.postings_count', $gettext('Anzahl der Beiträge'))"
                >
                    <a
                        href="#"
                        @click.prevent="sortBy('meta.postings_count')"
                        :title="$gettext('Nach Anzahl der Beiträge sortieren')">
                        {{ $gettext('Beiträge') }}
                    </a>
                </th>
                <th
                    :class="getSortClass('view_count')"
                    :aria-sort="getAriaSortString('view_count')"
                    :aria-label="getAriaSortLabel('view_count', $gettext('Anzahl der Aufrufe'))"
                >
                    <a
                        href="#"
                        @click.prevent="sortBy('view_count')"
                        :title="$gettext('Nach Anzahl der Aufrufe sortieren')">
                        {{ $gettext('Aufrufe') }}
                    </a>
                </th>
                <th
                    :class="getSortClass('meta.recent_activity')"
                    :aria-sort="getAriaSortString('meta.recent_activity')"
                    :aria-label="getAriaSortLabel('meta.recent_activity', $gettext('Aktivitäten'))"
                >
                    <a
                        href="#"
                        @click.prevent="sortBy('meta.recent_activity')"
                        :title="$gettext('Nach Aktivitäten sortieren')">
                        {{ $gettext('Aktivitäten') }}
                    </a>
                </th>
                <th></th>
                <th v-if="withActions"></th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="isLoading" >
                <td colspan="7">
                    <Loader />
                </td>
            </tr>
            <template v-else>
                <tr v-for="discussion in sortedData" :key="discussion.id">
                    <td>
                        <div class="discussion-overview">
                            <div class="title-with-actions">
                                <div class="title-with-actions__content">
                                    <StudipIcon class="icon" v-if="discussion.sticky" shape="pin" role="info" :size="20" />
                                    <a
                                        class="title-with-actions__link"
                                        :href="getDiscussionURL(discussion.id, {redirect})"
                                        :title="$gettext('Zur Diskussion')">
                                        <h3 class="title-with-actions_title line-clamp-2 m-0">{{ discussion.title }}</h3>
                                        <span
                                            v-if="discussion.meta.postings_count > discussion.meta.user_read_index"
                                            class="unread-items-badge"
                                            role="status"
                                            aria-live="polite"
                                            :aria-label="$gettext('Sie haben %{count} ungelesene Beiträge', {count: discussion.meta.postings_count - discussion.meta.user_read_index})"
                                            :title="$gettext('Sie haben %{count} ungelesene Beiträge', {count: discussion.meta.postings_count - discussion.meta.user_read_index})"
                                        >
                                            {{ discussion.meta.postings_count - discussion.meta.user_read_index }}
                                        </span>
                                    </a>
                                </div>
                                <div class="title-with-actions__actions-xs">
                                    <StudipActionMenu
                                        v-if="withActions"
                                        :items="getActionMenusItems(discussion)"
                                        @edit="editDiscussion(discussion.id)"
                                        @delete="deleteDiscussion(discussion.id)"
                                    />
                                </div>
                            </div>
                            <div class="inline-flex gap-10 items-start mb-10 mt-10">
                                <div v-if="discussion.category" class="discussion-category">
                                    <span v-if="discussion.category.color" :style="{ width: '12px', height: '12px', backgroundColor: discussion.category.color }"></span>
                                    <p class="m-0">
                                        {{ discussion.category.name }}
                                    </p>
                                </div>
                                <ul class="tags-container">
                                    <li v-if="discussion.discussion_type?.name" class="tags-container__tag">
                                        <StudipIcon :shape="discussion.discussion_type.icon" :size="12" :title="discussion.discussion_type.name" role="info" />
                                    </li>
                                    <template v-for="tag in discussion.tags" :key="tag.id">
                                        <li class="tags-container__tag">
                                            <a :href="getSearchURL(`tag_ids[]=${tag.id}`)" :title="$gettext('Zum Schlagwort')" :aria-label="$gettext('Zum Schlagwort')">
                                                {{ '#'+tag.name }}
                                            </a>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <!--mobile display: start-->
                        <div class="details-xs mt-10">
                            <dl>
                                <dt>{{ $gettext('Aufrufe') }}</dt>
                                <dd class="inline-flex gap-5 items-center">
                                    <StudipIcon shape="block-eyecatcher" :size="15" role="info" />
                                    {{ numberFormatter(discussion.view_count, 1) }}
                                </dd>

                                <dt>{{ $gettext('Anzahl der Beitrage') }}</dt>
                                <dd class="inline-flex gap-5 items-center">
                                    <StudipIcon shape="forum" :size="15" role="info" />
                                    {{ discussion.meta.postings_count }}
                                </dd>

                                <dt>{{ $gettext('Aktivitäten') }}</dt>
                                <dd class="inline-flex gap-5 items-center">
                                    <StudipIcon shape="activity" :size="15" role="info" />
                                    <StudipDateTime :iso="discussion.meta.recent_activity" :relative="true" />
                                </dd>

                                <dt>{{ $gettext('Ist geschlossen') }}</dt>
                                <dd
                                    v-if="discussion.closed_at"
                                    class="inline-flex gap-5 items-center"
                                >
                                    <StudipIcon
                                        :title="$gettext('Diskussion ist geschlossen')"
                                        shape="lock-locked2"
                                        :size="15"
                                        role="inactive" />
                                </dd>
                            </dl>

                            <dl>
                                <dt>{{ $gettext('Teilnehmende') }}</dt>
                                <dd class="nowrap">
                                    <ForumMembers :members="discussion.members" :limit="5" />
                                </dd>
                            </dl>
                        </div>
                        <!--mobile display: end-->
                    </td>
                    <td class="nowrap">
                        <ForumMembers :members="discussion.members" :limit="5" />
                    </td>
                    <td>
                        {{ discussion.meta.postings_count }}
                    </td>
                    <td>
                        {{ numberFormatter(discussion.view_count, 1) }}
                    </td>
                    <td class="nowrap">
                        <StudipDateTime v-if="discussion.meta.recent_activity" :iso="discussion.meta.recent_activity" :relative="true" />
                        <template v-else>{{ $gettext('Keine Aktivität') }}</template>
                    </td>
                    <td class="text-center">
                        <StudipIcon
                            v-if="discussion.closed_at"
                            :title="$gettext('Diskussion ist geschlossen')"
                            shape="lock-locked2"
                            :size="20"
                            role="inactive" />
                    </td>
                    <td v-if="withActions" class="actions">
                        <StudipActionMenu
                            :items="getActionMenusItems(discussion)"
                            @edit="editDiscussion(discussion.id)"
                            @delete="deleteDiscussion(discussion.id)"
                        />
                    </td>
                </tr>
                <tr v-if="sortedData.length === 0">
                    <td v-if="!forumConfig.isLoading" colspan="7">
                        {{ $gettext('Es sind noch keine Diskussionen vorhanden.') }}
                    </td>
                </tr>
            </template>
        </tbody>
        <slot name="pagination" />
    </table>
</template>
