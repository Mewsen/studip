<script setup>
import {onMounted} from "vue";
import {useForumConfig} from "../../store/pinia/forum/ForumConfig";

const forumConfig = useForumConfig();

onMounted(async () => {
    try {
        const response = await STUDIP.jsonapi.withPromises().GET(`courses/${STUDIP.URLHelper.parameters.cid}/forum-configs`);

        forumConfig.$patch({
            isModerator: response.meta['is-moderator'],
            isAdmin: response.meta['is-admin'],
            anonymousPost: response.meta['anonymous-post'],
            tileLayout: response.meta['tile-layout'],
        });
    } catch (error) {
        STUDIP.Report.error(error.statusText);
    }
})
</script>

<template>
    <div class="forum">
        <div class="forum__container use-utility-classes">
            <div>
                <slot />
            </div>
            <div class="forum__sidebar">
                <slot name="sidebar" />
            </div>
        </div>
    </div>
</template>
