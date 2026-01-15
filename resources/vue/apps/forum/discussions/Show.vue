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
    authUser: {
        type: Object,
        required: true,
    },
    readIndex: {
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
const postCreateForm = ref(false);

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
    return forumConfig.isModerator || props.discussion.user_id === STUDIP.USER_ID;
})

const fetchPostings = async () => {
    let offset = 0;
    let total = null;

    try {
        let hasMore = true;
        isLoading.value = true;

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

            const deserializedPosts = await deserializeJSONAPIResponse(response);
            forumPostStore.addPost(deserializedPosts);

            if (total === null) {
                total = response.meta.page.total;
            }

            offset += response.meta.page.limit;
            hasMore = offset < total;
        }

    } catch (error) {
        STUDIP.Report.error(error);
    } finally {
        isLoading.value = false;
    }
};


onMounted(async () => {
    await fetchPostings();

    const urlHash = window.location.hash.split("#")[1];
    if (urlHash) {
        if (urlHash === 'new-post') {
            postCreateForm.value = true;
        }
        jumpTo(document.getElementById(urlHash))
    } else if (props.readIndex < posts.value.length) {
        if (props.readIndex === 0) {
            jumpTo(document.getElementById('discussion_start'));
        } else {
            jumpTo(document.querySelector(`[data-index='${props.readIndex}']`));
        }
    }

    if (props.search_keyword !== "") {
        highlightText(props.search_keyword, '.post-content');

        document.querySelector('.post-content mark')?.scrollIntoView();

        // remove highlights
        document.getElementById("discussion_start").addEventListener("click", function() {
            removeHighlight('.post-content mark');
        });
    }
});
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
                            :context="discussion.title"
                            :subject="{
                                id: discussion.discussion_id,
                                type: 'forum-discussions'
                            }"
                            :userSubscription="authUser.subscription"
                        />
                    </template>
                </div>
            </div>
        </header>
        <div class="discussion">
            <template v-if="posts[0]">
                <Post :post="posts[0]" :authUser="authUser" :discussion="discussion" :readIndex="readIndex" />
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
                :posts="posts"
                :readIndex="readIndex"
                v-model:postCreateForm="postCreateForm"
            />
            <hr />
        </div>
        <div class="posts-container">
            <template v-for="(post, index) in posts.slice(1)" :key="post.id">
                <Post
                    :post="post"
                    :authUser="authUser"
                    :discussion="discussion"
                    :index="index + 1"
                    :readIndex="readIndex"
                />
                <hr v-if="index < posts.slice(1).length - 1" class="divider"/>
            </template>
        </div>

        <div v-if="posts.length > 3" class="discussion">
            <DiscussionFooter
                :discussion="discussion"
                :posts="posts"
                v-model:postCreateForm="postCreateForm"
            />
        </div>

        <div id="new-post" class="post-form-container">
            <PostCreateForm
                v-if="postCreateForm && !discussion.closed_at"
                :discussionId="discussion.discussion_id"
                :authUser="authUser"
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
