import {defineStore} from "pinia";
import {ref} from "vue";
export const useForumPost = defineStore(
    'forum_discussion_post',
    () => {

        const posts = ref([]);
        const currentPostIndex = ref(0);
        const firstUnreadPostIndex = ref(-1);

        function initPosts(newPosts) {
            posts.value = newPosts;
        }

        function addPost(post) {
            posts.value.push(...[].concat(post));
        }

        function updatePost(post) {
            const postIndex = posts.value.findIndex(({ id }) => id === post.id);

            posts.value[postIndex] = post;
        }

        function removePost(postId) {
            posts.value = posts.value.filter(({ id }) => id !== postId);
        }

        function addPostReaction(reaction, postId) {
            const postIndex = posts.value.findIndex(({ id }) => id === postId);

            posts.value[postIndex].reactions.push(reaction);
        }

        function removePostReaction(reactionId, postId) {
            const postIndex = posts.value.findIndex(({ id }) => id === postId);

            const postReactions = posts.value[postIndex].reactions;

            if (postReactions) {
                posts.value[postIndex].reactions = postReactions.filter(({ id }) => id !== reactionId);
            }
        }

        function updateCurrentPostIndex(index) {
            currentPostIndex.value = index;
        }

        function updateFirstUnreadPostIndex(index) {
            firstUnreadPostIndex.value = index;
        }

        return {
            posts,
            currentPostIndex,
            firstUnreadPostIndex,
            initPosts,
            addPost,
            updatePost,
            removePost,
            addPostReaction,
            removePostReaction,
            updateCurrentPostIndex,
            updateFirstUnreadPostIndex
        }
    }
)
