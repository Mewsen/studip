<script setup>
import {computed} from "vue";
import StudipDateTime from "@/vue/components/StudipDateTime.vue";

const props = defineProps({
    posts: {
        type: Array,
        required: true,
    },
    read_index: {
        type: Number,
        required: true,
        default: 0
    },
    discussion: {
        type: Object,
        required: true,
    }
});

const readPosts = computed(() => props.posts.slice(0, props.read_index));

const unreadPosts = computed(() => props.posts.slice(props.read_index));

const readPostsPercentage = computed(() => {
    if (props.posts.length === 0) {
        return 100;
    }

    return parseFloat((props.posts.length - unreadPosts.value.length) * 100 / props.posts.length);
});
</script>

<template>
    <table class="discussion-timeline-table" cellspacing="0">
        <tbody>
            <tr>
                <td></td>
                <td>
                    <a href="#discussion_start">
                        <StudipDateTime :iso="discussion.mkdate" :relative="true" />
                        <p>1/{{ posts.length }}</p>
                    </a>
                </td>
            </tr>
            <tr v-if="readPostsPercentage > 0" :style="{height: readPostsPercentage+'%' }">
                <td></td>
                <td></td>
            </tr>
            <template v-if="unreadPosts.length > 0">
                <tr>
                    <td class="bg-new-activity"></td>
                    <td>
                        <a :href="'#post_'+unreadPosts[0].id">
                            <StudipDateTime :iso="unreadPosts[0].mkdate" :relative="true" />
                            <p>{{ readPosts.length + 1 }}/{{ posts.length }} - {{ $gettext('neu ab hier') }}</p>
                        </a>
                    </td>
                </tr>
                <tr :style="{height: (100 - readPostsPercentage)+'%' }">
                    <td class="bg-new-activity"></td>
                    <td></td>
                </tr>
            </template>
            <tr v-if="posts.length > 0">
                <td class="bg-new-activity"></td>
                <td>
                    <a :href="'#post_'+posts[posts.length -1].id">
                        <StudipDateTime :iso="posts[posts.length -1].mkdate" :relative="true" />
                        <p>{{ posts.length }}/{{ posts.length }}</p>
                    </a>
                </td>
            </tr>
            <tr v-else>
                <td></td>
                <td>
                    <StudipDateTime :iso="discussion.mkdate" :relative="true" />
                    <p>{{ $gettext('Keine Beitrag bis hier') }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</template>
<style>
html {
    scroll-behavior: smooth;
}
</style>
