<script setup>
import {onMounted, onUnmounted, ref, useTemplateRef} from 'vue';
import {$gettext} from '@/assets/javascripts/lib/gettext';
import {useForumConfig} from '@/vue/store/pinia/forum/ForumConfig';
import {useForumPost} from '@/vue/store/pinia/forum/ForumPost';
import StudipIcon from '@/vue/components/StudipIcon.vue';
import StudipSwitch from '@/vue/components/StudipSwitch.vue';
import StudipWysiwyg from '@/vue/components/StudipWysiwyg.vue';
import {deserializeJSONAPIResponse} from '@/assets/javascripts/lib/jsonapiUtils';

const forumDiscussionPost = useForumPost();
const forumConfig = useForumConfig();
const emit = defineEmits(['canceled', 'updated']);
const props = defineProps({
    authUser: {
        type: Object,
        required: true
    },
    post: {
        type: Object,
        required: true
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

onMounted( () => {
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
        <label :for="`post-content-${post.id}`">
            <span class="sr-only">{{ $gettext('Inhalt') }}</span>
        </label>
        <StudipWysiwyg :id="`post-content-${post.id}`" required="required" v-model="content" :autofocus="true" />
        <div v-if="forumConfig.anonymousPost" class="mt-10">
            <StudipSwitch name="anonymous" v-model="anonymous" :label="$gettext('Anonym')" />
        </div>
        <div class="post-form__footer">
            <button
                type="submit"
                class="button button--icon-label"
                :disabled="isLoading || !content"
                :value="$gettext('Speichern')"
            >
                <StudipIcon shape="accept" :size="20" aria-hidden="true" />
                {{ $gettext('Speichern') }}
            </button>
            <button
                type="button"
                class="button button--icon-label"
                @click="$emit('canceled')"
            >
                <StudipIcon shape="decline" :size="20" aria-hidden="true"/>
                {{ $gettext('Abbrechen') }}
            </button>
        </div>
    </form>
</template>
