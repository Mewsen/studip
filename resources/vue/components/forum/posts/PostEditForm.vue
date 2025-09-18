<script setup>
import {onMounted, onUnmounted, ref} from "vue";
import {$gettext} from "@/assets/javascripts/lib/gettext";
import {useForumConfig} from "../../../store/pinia/forum/ForumConfig";
import {useForumPost} from "../../../store/pinia/forum/ForumPost";
import StudipIcon from "@/vue/components/StudipIcon.vue";
import StudipSwitch from "@/vue/components/StudipSwitch.vue";
import StudipWysiwyg from "@/vue/components/StudipWysiwyg.vue";
import {deserializeJSONAPIResponse} from "../../../../assets/javascripts/lib/jsonapiUtils";

const forumDiscussionPost = useForumPost();
const forumConfig = useForumConfig();
const emit = defineEmits(['canceled', 'updated']);
const props = defineProps({
    auth_user: {
        type: Object,
        required: true,
    },
    post: {
        type: Object,
        required: true,
    }
});

const anonymous = ref(props.post.anonymous);
const content = ref(props.post.content);
const isLoading = ref(false);

const getPostJSONAPIObject = () => ({
    data: {
        id: props.post.id,
        type: 'forum-postings',
        attributes: {
            content: content.value,
            anonymous: anonymous.value
        }
    }
})


const updatePost = async () => {
    try {
        isLoading.value = true;

        const response = await STUDIP.jsonapi.withPromises().PATCH(
            `forum-postings/${props.post.id}?include=opengraph-urls,posting`,
            { data: getPostJSONAPIObject }
        );

        const post = await deserializeJSONAPIResponse(response);

        const updatedPost = {
            ...props.post,
            ...post
        };

        forumDiscussionPost.updatePost(updatedPost);
        content.value = "";
        emit("updated", updatedPost);

        STUDIP.Report.success($gettext("Die Änderungen wurde gespeichert."));
    } catch (error) {
        STUDIP.Report.error(error);
    } finally {
        isLoading.value = false;
    }
}

onMounted(() => {
    if (window.location.hash) {
        window.history.pushState('', document.title, window.location.href.split('#')[0]);
    }
})

onUnmounted(() => {
    if (window.location.hash) {
        window.history.pushState('', document.title, window.location.href.split('#')[0]);
    }
})
</script>

<template>
    <form @submit.prevent="updatePost" class="default post-form forum-quote">
        <StudipWysiwyg required="required" v-model="content" />
        <div v-if="forumConfig.anonymousPost" class="mt-10">
            <StudipSwitch name="anonymous" v-model="anonymous" :label="$gettext('Anonym')" />
        </div>
        <div class="post-form__footer">
            <button
                type="submit"
                :disabled="isLoading || !content"
                class="button button--icon-label"
                :value="$gettext('Speichern')"
                :title="$gettext('Speichern')"
            >
                <StudipIcon shape="reply" :size="20" aria-hidden="true" />
                {{ $gettext('Speichern') }}
            </button>
            <button
                type="button"
                class="button button--icon-label"
                :title="$gettext('Abbrechen')"
                @click="$emit('canceled')"
            >
                <StudipIcon shape="decline" :size="20" aria-hidden="true"/>
                {{ $gettext('Abbrechen') }}
            </button>
        </div>
    </form>
</template>
