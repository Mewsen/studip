<script setup>
import {computed, ref, useTemplateRef} from "vue";
import PostEditForm from "./PostEditForm.vue";
import PostCreateForm from "./PostCreateForm.vue";
import PostContent from "@/vue/components/forum/posts/PostContent.vue";
import PostReactions from "./PostReactions.vue";
import {useForumPost} from "../../../store/pinia/forum/ForumPost";
import {getDiscussionURL} from "@/vue/components/forum/helpers/urls";
import StudipDateTime from "@/vue/components/StudipDateTime.vue";
import StudipIcon from "@/vue/components/StudipIcon.vue";
import {$gettext} from "@/assets/javascripts/lib/gettext";
import LinksPreview from "@/vue/components/LinksPreview.vue";
import UserAvatarDropdown from "../UserAvatarDropdown.vue";
import {userProfileURL} from "../helpers/urls";

const forumDiscussionPost = useForumPost();
const props = defineProps({
    discussion: {
        type: Object,
        required: true,
    },
    post: {
        type: Object,
        required: true,
    },
    auth_user: {
        type: Object,
        required: true
    },
    is_unread: {
        type: Boolean,
        default: false
    }
});

const postContent = useTemplateRef('postContent');
const userAvatarContainer = useTemplateRef('userAvatarContainer');

const selectedText = ref('');
const editPost = ref('');
const postCreateForm = ref(false);

const isUnread = computed(() => (!props.post.author && props.is_unread) || (props.is_unread && props.post.author.id !== STUDIP.USER_ID))

const copyToClipboard = () => {
    if (selectedText.value) {
        navigator.clipboard.writeText(selectedText.value);
        postContent.value.removeSelection();
        STUDIP.Report.info($gettext('Der markierte Text wurde in die Zwischenablage kopiert.'));
    }
}

const deletePost = async (post) => {
    STUDIP.Dialog.confirm(
        $gettext('Wollen Sie diese Beitrag löschen?'),
        async () => {
            try {
                await STUDIP.jsonapi.withPromises().DELETE(`forum-postings/${post.id}`);
                forumDiscussionPost.removePost(post.id);
                STUDIP.Report.success($gettext('Der Beitrag wurde gelöscht.'));
            } catch (error) {
                STUDIP.Report.error(error.statusText);
            }
        },
        STUDIP.Dialog.close());
}

const addPost = () => {
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    postCreateForm.value = false;
}

const addReply = post => {
    postCreateForm.value = true;
    selectedText.value = post.content;
}

const forwardPost = post => {
    let messageBoyd = `
        ${$gettext('Die Sender:in dieser Nachricht möchte Sie auf den folgenden Beitrag aufmerksam machen: ')}
        <br />
        <br />
        ${$gettext('Link zum Beitrag: ')}
        <a href="${getDiscussionURL(props.discussion.discussion_id) + '#post_' + post.id}">
            ${props.discussion.title}
        </a>
    `;

    STUDIP.Dialog.fromURL(STUDIP.URLHelper.getURL('dispatch.php/messages/write'), {
        data: {
            default_subject: 'WG: ' + props.discussion.title,
            default_body: STUDIP.wysiwyg.markAsHtml(messageBoyd)
        },
        method: 'post'
    });
}

const removePostHighlight = id => {
    const element = document.getElementById(id)
    if (!element) {
        console.error("Element not found!")
        return
    }
    element.classList.remove('--highlight')
}
</script>

<template>
    <div :id="'post_'+post.id" class="post" @click="removePostHighlight('post_'+post.id)">
        <div v-if="isUnread" class="post__unread">
        </div>
        <div class="post__body">
            <div class="post__author">
                <div class="post__author-avatar" ref="userAvatarContainer">
                    <UserAvatarDropdown
                        v-if="post.author?.id"
                        :user="post.author"
                        size="50px"
                        @update:modelValue="state => {
                            if (state) userAvatarContainer.style.setProperty('z-index', 100);
                            else userAvatarContainer.style.setProperty('z-index', 1);
                        }"
                    />
                </div>
                <div class="post__author-name-container --xs">
                    <p v-if="!post.author" class="author-name">
                        {{ $gettext('Unbekannt') }}
                    </p>
                    <p v-else-if="!post.author.id" class="author-name">
                        {{ $gettext('Anonym') }}
                    </p>
                    <a
                        v-else
                        :href="userProfileURL(post.author.username)"
                        :title="$gettext('Zum Profil')"
                        :aria-label="$gettext('Zum Profil von %{name}', { name: post.author.name })"
                        class="author-name"
                    >
                        {{ post.author.name }}
                    </a>
                    <em v-if="post.chdate > post.mkdate">
                        {{ $gettext('Bearbeitet: ') }}
                        <StudipDateTime :iso="post.chdate" :relative="true" />
                    </em>
                    <StudipDateTime v-else :iso="post.mkdate" :relative="true" />
                </div>
            </div>
            <div class="post__content">
                <div class="post__author-name-container --xl">
                    <p v-if="!post.author" class="author-name">
                        {{ $gettext('Unbekannt') }}
                    </p>
                    <p v-else-if="!post.author.id" class="author-name">
                        {{ $gettext('Anonym') }}
                    </p>
                    <a
                        v-else
                        :href="userProfileURL(post.author.username)"
                        :title="$gettext('Zum Profil')"
                        :aria-label="$gettext('Zum Profil von %{name}', { name: post.author.name })"
                        class="author-name"
                    >
                        {{ post.author.name }}
                    </a>
                    <span v-if="post.chdate > post.mkdate">
                        {{ $gettext('Bearbeitet: ') }}
                        <StudipDateTime :iso="post.chdate" :relative="true" />
                    </span>
                    <StudipDateTime v-else :iso="post.mkdate" :relative="true" />
                </div>
                <template v-if="editPost === post.id">
                    <PostEditForm :post="post" :auth_user="auth_user" class="mt-10" @canceled="editPost = ''" @updated="editPost = ''"/>
                </template>
                <template v-else>
                    <div class="post__text">
                        <PostContent ref="postContent" v-model="selectedText" :content="post.content" class="forum-quote">
                            <template #actions>
                                <a
                                    :href="`#create_form_${post.id}`"
                                    class="ballon-action__button"
                                    v-if="!postCreateForm && !discussion.closed_at"
                                    @click="postCreateForm = true; postContent.removeSelection()"
                                    :title="$gettext('Auswahl zitieren und antworten')"
                                    :aria-label="$gettext('Auswahl zitieren und antworten')"
                                >
                                    <StudipIcon shape="quote" :size="20" />
                                </a>
                                <button
                                    type="button"
                                    class="ballon-action__button"
                                    @click="copyToClipboard"
                                    :title="$gettext('Kopieren')"
                                    :aria-label="$gettext('Kopieren')"
                                >
                                    <StudipIcon shape="clipboard" :size="20" />
                                </button>
                            </template>
                        </PostContent>
                    </div>

                    <div v-if="post.meta.opengraph_urls.length" class="opengraph-urls">
                        <LinksPreview :links="post.meta.opengraph_urls" />
                    </div>

                    <PostReactions :posting_id="post.id" :reactions="post.reactions" />
                </template>

                <div class="post__footer">
                    <div></div>
                    <div class="inline-flex items-center gap-40">
                        <div v-if="!discussion.closed_at" class="inline-flex items-center gap-10">
                            <template v-if="post.author?.id === auth_user.id">
                                <a
                                    :href="`#post_${post.id}`"
                                    @click="editPost = post.id"
                                    type="button"
                                    class="button button--icon-only"
                                    :class="{
                                        'disabled': editPost === post.id
                                    }"
                                    :title="$gettext('Beitrag bearbeiten')"
                                    :aria-label="$gettext('Beitrag bearbeiten')"
                                >
                                    <StudipIcon shape="edit" :size="20" aria-hidden="true" />
                                </a>
                                <button @click="deletePost(post)" type="button" class="button button--icon-only" :title="$gettext('Beitrag löschen')" :aria-label="$gettext('Beitrag löschen')">
                                    <StudipIcon shape="trash" :size="20" aria-hidden="true" />
                                </button>
                            </template>
                            <button type="button" @click="forwardPost(post)" class="button button--icon-only" :title="$gettext('Beitrage weiterleiten')" :aria-label="$gettext('Beitrage weiterleiten')">
                                <StudipIcon shape="export" :size="20" aria-hidden="true" />
                            </button>
                            <button :disabled="postCreateForm" @click="addReply(post)" type="button" class="button button--icon-only" :title="$gettext('Zitieren und antworten')" :aria-label="$gettext('Zitieren und Antworten')">
                                <StudipIcon shape="quote" :size="20" aria-hidden="true" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="postCreateForm && !discussion.closed_at" :id="`create_form_${post.id}`" class="post-form-container" style="scroll-margin-top: 200px;">
        <PostCreateForm
            :parent_id="post.id"
            :discussion_id="props.discussion.discussion_id"
            :auth_user="auth_user"
            v-model:quote="selectedText"
            @canceled="postCreateForm = false; selectedText = ''"
            @created="addPost"
        />
    </div>
</template>
