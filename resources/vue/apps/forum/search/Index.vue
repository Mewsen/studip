<script setup>
import ForumApp from "@/vue/components/forum/ForumApp.vue";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import {computed, onMounted, reactive, ref} from "vue";
import SelectTopicInput from "@/vue/components/forum/topics/SelectTopicInput.vue";
import SelectTagsInput from "@/vue/components/forum/SelectTagsInput.vue";
import SelectDiscussionType from "@/vue/components/forum/discussions/SelectDiscussionType.vue";
import {getTopicURL} from "@/vue/components/forum/helpers/urls";
import SelectUserInput from "@/vue/components/forum/SelectUserInput.vue";
import DiscussionIndex from "@/vue/components/forum/discussions/DiscussionIndex.vue";
import StudipIcon from "../../../components/StudipIcon.vue";
import StudipSelect from "../../../components/StudipSelect.vue";
import {highlightText, removeHighlight} from "@/vue/components/forum/helpers";

const discussionStatuses = [
    {
        value: 1,
        label: $gettext('Alle')
    },
    {
        value: 2,
        label: $gettext('Geöffnet')
    },
    {
        value: 3,
        label: $gettext('Geschlossen')
    }
];

const CSRF = STUDIP.CSRF_TOKEN;

const props = defineProps({
    search: {
        type: Object,
        required: true
    },
    discussions: {
        type: Array,
        required: true
    },
    topics: {
        type: Array,
        required: true
    },
    discussion_types: {
        type: Array,
        required: true
    },
    tags: {
        type: Array,
        required: true
    },
    course_members: {
        type: Array,
        required: true
    }
});

const isFilterVisible = ref(true);

const searchForm = reactive({
    ...props.search,
    begin: toDateString(props.search.begin),
    end: toDateString(props.search.end),
    discussion_status: discussionStatuses.find(status => status.value === props.search.discussion_status),
    topics: props.topics.filter(({ topic_id }) => props.search.topic_ids.includes(topic_id)),
    tags: props.tags.filter(({ id }) => props.search.tag_ids.includes(id.toString())),
    types: props.discussion_types.filter(({ type_id }) => props.search.discussion_type_ids.includes(type_id.toString())),
    authors: props.course_members.filter(({ user_id }) => props.search.user_ids.includes(user_id))
});

const availableTags = computed(() => {
    if (searchForm.tags && searchForm.tags.length > 0) {
        const selectedTagsId = searchForm.tags.map(({ id }) => id);
        return props.tags.filter(({ id }) => selectedTagsId.indexOf(id) < 0);
    }

    return props.tags;
});

const availableTopics = computed(() => {
    if (searchForm.topics && searchForm.topics.length > 0) {
        const selectedTopicsId = searchForm.topics.map(({ topic_id }) => topic_id);
        return props.topics.filter(({ topic_id }) => selectedTopicsId.indexOf(topic_id) < 0);
    }

    return props.topics;
});

const availableTypes = computed(() => {
    if (searchForm.types && searchForm.types.length > 0) {
        const selectedTypesId = searchForm.types.map(({ type_id }) => type_id);
        return props.discussion_types.filter(({ type_id }) => selectedTypesId.indexOf(type_id) < 0);
    }

    return props.discussion_types;
});

const actionURL = STUDIP.URLHelper.getURL(`dispatch.php/course/forum/search`);

const resetSearchForm = () => {
    Object.assign(searchForm, {
        keyword: '',
        discussion_status: null,
        begin: null,
        end: null,
        topics: [],
        tags: [],
        types: [],
        authors: []
    });
}

function toUnixTimestamp(date) {
    return (new Date(date)).getTime() / 1000;
}

function toDateString(unixTimestamp) {
    if (!unixTimestamp) {
        return '';
    }

    return (new Date(parseInt(unixTimestamp) * 1000)).toISOString().split('T')[0];
}

onMounted(() => {
    if(searchForm.keyword.length > 1 && props.discussions.length) {
        highlightText(searchForm.keyword, '.title');

        // remove highlights
        document.getElementById("forum-search").addEventListener("click", function() {
            removeHighlight('.title mark');
        });
    }
})
</script>

<template>
    <ForumApp id="forum-search">
        <form :action="actionURL" method="post" class="default search-container use-utility-classes">
            <input type="hidden" :name="CSRF.name" :value="CSRF.value">
            <h1>{{ $gettext('Suche') }}</h1>
            <div class="search-controls">
                <div class="search-input-container">
                    <input name="keyword" type="text" :value="searchForm.keyword" :placeholder="$gettext('Diskussionen oder Beiträge')"/>
                </div>
                <button
                    type="submit"
                    class="button m-0 --with-icon"
                    :title="$gettext('Suchen')"
                >
                    <StudipIcon shape="search" :size="20" class="icon-default" aria-hidden="true" />
                    <StudipIcon shape="search" :size="20" class="icon-hover" role="info_alt" aria-hidden="true" />
                    {{ $gettext('Suchen') }}
                </button>
                <button @click="resetSearchForm" type="button" class="icon-button" :title="$gettext('Zurücksetzen')">
                    <StudipIcon shape="decline" :size="20" />
                </button>
            </div>

            <div class="filter-summary-container">
                <template v-for="topic in searchForm.topics" :key="topic.topic_id">
                    <div class="badge">
                        <a :href="getTopicURL(topic.topic_id)" :title="$gettext('Zum Thema')" target="_blank" class="flex gap-5 items-center">
                            <span :style="{ backgroundColor: topic.color ?? '#EDEDED', height: '14px', width: '14px'}"></span>
                            {{ topic.name }}
                        </a>
                        <button @click="searchForm.topics = searchForm.topics.filter(t => t.topic_id !== topic.topic_id)" class="action">
                            <StudipIcon shape="decline" :size="15" />
                        </button>
                    </div>
                </template>
                <template v-for="type in searchForm.types" :key="type.type_id">
                    <div class="badge" :title="type.name">
                        <StudipIcon :shape="type.icon" :size="15" />
                        <span>{{ type.name }}</span>
                        <button @click="searchForm.types = searchForm.types.filter(t => t.type_id !== type.type_id)" class="action">
                            <StudipIcon shape="decline" :size="15" />
                        </button>
                    </div>
                </template>
                <template v-for="tag in searchForm.tags" :key="tag">
                    <div class="badge" :title="tag.name">
                        <span>{{ '#'+tag.name }}</span>
                        <button @click="searchForm.tags = searchForm.tags.filter(t => t.name !== tag.name)" class="action">
                            <StudipIcon shape="decline" :size="15" />
                        </button>
                    </div>
                </template>

                <template v-for="user in searchForm.authors" :key="user.user_id">
                    <div class="badge">
                        <a :href="user.profile_url" target="_blank" :title="$gettext('Zum Nutzer Profile')" class="flex gap-5 items-center">
                            <img width="15px" height="15px" :src="user.avatar_url" :alt="user.name" />
                            {{ user.name }}
                        </a>
                        <button @click="searchForm.authors = searchForm.authors.filter(u => u.name !== user.name)" class="action">
                            <StudipIcon shape="decline" :size="15" />
                        </button>
                    </div>
                </template>
            </div>

            <hr />

            <div>
                <button
                    @click="isFilterVisible = !isFilterVisible"
                    type="button" class="toggle-filter-button"
                    :title="isFilterVisible ? $gettext('Erweiterte Filter zuklappen') : $gettext('Erweiterte Filter aufklappen')"
                    :aria-label="isFilterVisible ? $gettext('Erweiterte Filter zuklappen') : $gettext('Erweiterte Filter aufklappen')"
                    :aria-expanded="isFilterVisible.toString()"
                >
                    {{ $gettext('Erweiterte Filter') }}
                    <StudipIcon v-if="isFilterVisible" shape="arr_1up" :size="20" />
                    <StudipIcon v-else shape="arr_1down"  :size="20" />
                </button>
                <div v-if="isFilterVisible" class="filter-controls">
                    <label>
                        <span class="sr-only">{{ $gettext('Thema') }}</span>
                        <template v-for="topic in searchForm.topics" :key="topic.topic_id">
                            <input type="hidden" name="topic_ids[]" :value="topic.topic_id">
                        </template>
                        <SelectTopicInput id="" :options="availableTopics" v-model="searchForm.topics" multiple />
                    </label>
                    <label>
                        <span class="sr-only">{{ $gettext('Diskussionstyp') }}</span>
                        <template v-for="type in searchForm.types" :key="type.type_id">
                            <input type="hidden" name="discussion_type_ids[]" :value="type.type_id">
                        </template>
                        <SelectDiscussionType :options="availableTypes" v-model="searchForm.types" multiple />
                    </label>
                    <label>
                        <span class="sr-only">{{ $gettext('Schlagworte') }}</span>
                        <template v-for="tag in searchForm.tags" :key="tag.id">
                            <input type="hidden" name="tag_ids[]" :value="tag.id">
                        </template>
                        <SelectTagsInput :options="availableTags" v-model="searchForm.tags" multiple />
                    </label>
                    <label>
                        <span class="sr-only">{{ $gettext('Status der Diskussion') }}</span>
                        <input v-if="searchForm.discussion_status" type="hidden" name="discussion_status" :value="searchForm.discussion_status.value">
                        <StudipSelect
                            :options="discussionStatuses"
                            :placeholder="$gettext('Status der Diskussion')"
                            v-model="searchForm.discussion_status"
                        >
                            <template #no-options>
                                <div>
                                    {{ $gettext('Es gibt keine Diskussionsstatus.') }}
                                </div>
                            </template>
                        </StudipSelect>
                    </label>
                    <div class="date-inputs-container">
                        <input type="date" v-model="searchForm.begin" :placeholder="$gettext('Von')" :aria-label="$gettext('Von')" autocomplete="off" />
                        <input type="date" v-model="searchForm.end" :placeholder="$gettext('Bis')" :aria-label="$gettext('Bis')" autocomplete="off" />

                        <input type="hidden" name="begin" :value="toUnixTimestamp(searchForm.begin)" />
                        <input type="hidden" name="end" :value="toUnixTimestamp(searchForm.end)" />
                    </div>
                    <label>
                        <span class="sr-only">{{ $gettext('Autor/-in') }}</span>
                        <template v-for="user in searchForm.authors" :key="user.user_id">
                            <input type="hidden" name="user_ids[]" :value="user.user_id">
                        </template>
                        <SelectUserInput
                            :options="course_members"
                            multiple
                            v-model="searchForm.authors"
                        />
                    </label>
                </div>
            </div>
        </form>

        <div class="search-result-container">
            <h2>{{ $gettext('Suchergebnisse') }}</h2>
            <DiscussionIndex :discussions="discussions" :withActions="false" redirect="search" />
        </div>
    </ForumApp>
</template>
