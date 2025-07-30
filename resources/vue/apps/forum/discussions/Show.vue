<script setup>
import {onMounted, computed, ref} from "vue";
import ForumApp from "@/vue/components/forum/ForumApp.vue";
import {useForumPost} from "../../../store/pinia/forum/ForumPost";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import Post from "@/vue/components/forum/posts/Post.vue";
import PostCreateForm from "@/vue/components/forum/posts/PostCreateForm.vue";
import Loader from "@/vue/components/forum/Loader.vue";
import {useForumConfig} from "../../../store/pinia/forum/ForumConfig";
import StudipIcon from "../../../components/StudipIcon.vue";
import StudipDateTime from "../../../components/StudipDateTime.vue";
import SubscriptionDropdown from "@/vue/components/forum/SubscriptionDropdown.vue";
import {highlightText, removeHighlight} from "@/vue/components/forum/helpers";
import {getSearchURL, getTopicURL, getDiscussionIndexURL} from "@/vue/components/forum/helpers/urls";
import {deserializeJSONAPIResponse} from "../../../../assets/javascripts/lib/jsonapiUtils";
import DiscussionFooter from "../../../components/forum/discussions/DiscussionFooter.vue";

const forumConfig = useForumConfig();
const forumPostStore = useForumPost();
const props = defineProps({
    discussion: {
        type: Object,
        required: true,
    },
    category: {
        type: Object,
        required: true,
    },
    auth_user: {
        type: Object,
        required: true,
    },
    read_index: {
        type: Number,
        required: true,
        default: 0
    },
    redirect: {
        type: String,
        default: 'topic'
    },
    search_keyword: {
        type: String,
        default: ''
    }
});

const isLoading = ref(false);
const postCreateForm = ref(false)

const editDiscussion = id => STUDIP.Dialog.fromURL(
    STUDIP.URLHelper.getURL(`dispatch.php/course/forum/discussions/edit/${id}`),
    {
        width: '900'
    }
);

const posts = computed(() => forumPostStore.posts);

const addPost = () => {
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    postCreateForm.value = false;
}

const goBackURL = computed(() => {
    switch (props.redirect) {
        case 'search':
            return getSearchURL();
        case 'discussions':
            return getDiscussionIndexURL();
        default:
            return getTopicURL(props.discussion.topic_id);
    }
});

const canEditDiscussion = computed(() => {
    return forumConfig.isModerator || props.discussion.user_id === STUDIP.USER_ID
})

const fetchPostings = async () => {
    let allPostings = [];
    let offset = 0;
    let total = null;

    try {
        let hasMore = true;

        while (hasMore) {
            const response = await STUDIP.jsonapi.withPromises().GET(
                `forum-discussions/${props.discussion.discussion_id}/postings`,
                {
                    data: {
                        include: 'author,opengraph-urls,posting,reactions,reactions.user&fields[users]=id,username,formatted-name',
                        page: { offset }
                    }
                }
            );

            const deserializedPostings = await deserializeJSONAPIResponse(response);
            allPostings.push(...deserializedPostings);

            if (total === null) {
                total = response.meta.page.total;
            }

            offset += response.meta.page.limit;
            hasMore = offset < total;
        }

        forumPostStore.initPosts(allPostings);
    } catch (error) {
        STUDIP.Report.error(error.statusText);
    }
};


onMounted(async () => {
    isLoading.value = true;

    await fetchPostings();

    const urlHash = window.location.hash.split("#")[1];
    if (urlHash) {
        if (urlHash === 'new-post') {
            postCreateForm.value = true;
        }
        document.getElementById(urlHash)?.scrollIntoView();
    } else if (props.read_index < posts.value.length) {
        document.querySelectorAll(".post")[props.read_index].scrollIntoView();
    }

    isLoading.value = false;

    if (props.search_keyword !== "") {
        highlightText(props.search_keyword, '.post-content');

        document.querySelector('.post-content mark')?.scrollIntoView();

        // remove highlights
        document.getElementById("discussion_start").addEventListener("click", function() {
            removeHighlight('.post-content mark');
        })
    }
})
</script>

<template>
    <ForumApp id="discussion_start">
        <header class="header">
            <div v-if="category.color" class="flag" :style="{ backgroundColor: category.color}"></div>
            <div class="header__content header__content--with-actions items-start">
                <div class="flex items-start gap-10">
                    <a :href="goBackURL" :title="$gettext('Zum Thema')" class="go-back-link">
                        <StudipIcon shape="arr_1left" :size="20" />
                    </a>
                    <div>
                        <ul class="breadcrumb">
                            <li>
                                <a :href="getTopicURL(discussion.topic_id)" :title="$gettext('Zum Thema')">
                                    {{ discussion.topic.name }}
                                </a>
                            </li>
                            <li>
                                <div class="inline-flex items-start gap-5">
                                    <StudipIcon class="mt-1" v-if="discussion.sticky" role="info" shape="pin" :size="20" />
                                    {{ discussion.title }}
                                </div>
                            </li>
                        </ul>

                        <ul class="mt-10 tags-container">
                            <li v-if="discussion.type.name" class="tags-container__tag">
                                <StudipIcon role="info" :shape="discussion.type.icon" :size="15" :title="discussion.type.name"/>
                            </li>
                            <template v-for="tag in discussion.tags" :key="tag.id">
                                <li class="tags-container__tag">
                                    <a :href="getSearchURL(`tag_ids[]=${tag.id}`)" :title="$gettext('Zum Schlagwort')" :aria-label="$gettext('Zum Schlagwort')">{{ '#'+tag.name }}</a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <div class="actions">
                    <div
                        v-if="discussion.closed_at"
                        :title="$gettext('Diskussion ist geschlossen')"
                        class="discussion-closed">
                        <em>
                            {{ $gettext('Geschlossen:') }}
                            <StudipDateTime :iso="discussion.closed_at" :relative="true" />
                        </em>
                        <StudipIcon shape="lock-locked2" :size="20" role="inactive" />
                    </div>
                    <template v-if="!forumConfig.allowGuestAccess">
                        <button v-if="canEditDiscussion" @click="editDiscussion(discussion.discussion_id)" type="button" :title="$gettext('Diskussion bearbeiten')" class="button button--icon-only">
                            <StudipIcon shape="edit" :size="20" />
                        </button>
                        <SubscriptionDropdown
                            v-if="!discussion.closed_at"
                            :subject="{
                                id: discussion.discussion_id,
                                type: 'forum-discussions'
                            }"
                            :user_subscription="auth_user.subscription"
                        />
                    </template>
                </div>
            </div>
        </header>
        <div class="discussion">
            <template v-if="posts[0]">
                <Post :post="posts[0]" :auth_user="auth_user" :discussion="discussion" :is_unread="read_index === 0" />
            </template>
            <div v-else class="discussion__body">
                <Loader v-if="isLoading" />
                <p v-else class="text-center">
                    {{ $gettext('Es sind noch keine Beiträge vorhanden.') }}
                </p>
            </div>
            <hr />
            <DiscussionFooter
                :discussion="discussion"
                :posts_count="posts.length"
                :recent_activity="posts[posts.length - 1] ? posts[posts.length - 1].mkdate : null"
                v-model:postCreateForm="postCreateForm"
            />
            <hr />
        </div>
        <div class="posts-container">
            <template v-for="(post, index) in posts.slice(1)" :key="post.id">
                <Post
                    :post="post"
                    :auth_user="auth_user"
                    :discussion="discussion"
                    :is_unread="read_index < index + 2"
                />
                <hr v-if="index < posts.slice(1).length - 1" class="divider"/>
            </template>
        </div>

        <div v-if="posts.length > 3" class="discussion">
            <DiscussionFooter
                :discussion="discussion"
                :posts_count="posts.length"
                :recent_activity="posts[posts.length - 1].mkdate"
                v-model:postCreateForm="postCreateForm"
            />
        </div>

        <div id="new-post" class="post-form-container">
            <PostCreateForm
                v-if="postCreateForm && !discussion.closed_at"
                :discussion_id="discussion.discussion_id"
                :auth_user="auth_user"
                @canceled="postCreateForm = false"
                @created="addPost"
            />
        </div>
    </ForumApp>
</template>

<style>
#content-wrapper {
    overflow-x: unset !important;
}

#sidebar #sidebar-actions {
    position: sticky;
    top: 50px;
}
html {
    scroll-behavior: smooth;
}
</style>
