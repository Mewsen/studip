<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
import StudipDateTime from '@/vue/components/StudipDateTime.vue';
import {useForumPost} from '@/vue/store/pinia/forum/ForumPost';
import {useForumConfig} from '@/vue/store/pinia/forum/ForumConfig';
import {$gettext} from '@/assets/javascripts/lib/gettext';

const forumConfig = useForumConfig();
const forumPostStore = useForumPost();
defineProps({
    discussion: {
        type: Object,
        required: true,
    }
});

const scrollerTop = ref(0);
const isDragging = ref(false);
const unreadScrollPosition = ref(-1);

const posts = computed(() => forumPostStore.posts);
const currentPostIndex = computed(() => forumPostStore.currentPostIndex);
const firstUnreadPostIndex = computed(() => forumPostStore.firstUnreadPostIndex);
const currentPostDate = computed(() => {
    if (currentPostIndex.value < posts.value.length) {
        const date = new Date(posts.value[currentPostIndex.value].mkdate);
        return date.toLocaleString(String.locale, { month: 'long', year: 'numeric' });
    }

    return null;
});
const isNewFrom = computed(() => firstUnreadPostIndex.value > -1 && !forumConfig.allowGuestAccess);
const postProgressText = computed(() => {
    return $gettext('Aktuell: Beitrag %{current} von %{total}', {
        current: currentPostIndex.value + 1,
        total: posts.value.length
    })
});

const findPostAtScroll = y => {
    const postElements = document.querySelectorAll('.post');
    for (const postElement of postElements) {
        const postScrollPosition = postElement.getBoundingClientRect().top + window.scrollY;

        if (postScrollPosition > y) {
            return postElement;
        }
    }

    return null;
}

const jumpToPost = (targetPost, index = 0) => {
    if (!targetPost) {
        targetPost = document.querySelector(`[data-index='${index}']`);
    }

    if (parseInt(targetPost?.dataset.index) === 0) {
        forumPostStore.updateCurrentPostIndex(0);
        document.getElementById('discussion_start').scrollIntoView({ behavior: 'instant' });
        return;
    }

    targetPost?.scrollIntoView({ behavior: 'instant' });
    targetPost?.focus();
}

const jumpTo = e => {
    const contentContainer = document.documentElement;
    const trackRect = e.currentTarget.getBoundingClientRect();
    const clickY = e.clientY - trackRect.top;
    const percent = Math.min(Math.max(clickY / trackRect.height, 0), 1);

    const scrollPosition = percent * (contentContainer.scrollHeight - contentContainer.clientHeight);
    const targetPost = findPostAtScroll(scrollPosition);

    if (targetPost) {
        jumpToPost(targetPost);
        updateScroller(scrollPosition);
    } else {
        contentContainer.scrollTop = scrollPosition;
    }
}

const startDrag = e => {
    isDragging.value = true;
    let scrollPosition = -1;
    let targetPost = null;

    const contentContainer = document.documentElement;
    const rectScrollArea = document.getElementById('scroll-area').getBoundingClientRect();
    const scrollerRect = document.getElementById('scroller').getBoundingClientRect();

    const offsetY = e.clientY - (scrollerRect.top + scrollerRect.height / 2);

    const onDrag = e2 => {
        const y = e2.clientY - rectScrollArea.top - offsetY;
        const percent = Math.min(Math.max(y / rectScrollArea.height, 0), 1);

        scrollerTop.value = percent * 100;
        scrollPosition = percent * (contentContainer.scrollHeight - contentContainer.clientHeight);
        targetPost = findPostAtScroll(scrollPosition);
        forumPostStore.updateCurrentPostIndex(parseInt(targetPost?.dataset.index ?? 0))
        updateScroller(scrollPosition);
    };

    const onDrop = () => {
        if (scrollPosition < 0) {
            resetDrag();
            return;
        }

        if (targetPost) {
            jumpToPost(targetPost);
        } else {
            contentContainer.scrollTop = scrollPosition;
        }

        resetDrag();
    };

    const resetDrag = () => {
        isDragging.value = false;
        document.removeEventListener('mousemove', onDrag);
        document.removeEventListener('mouseup', onDrop);
    };

    document.addEventListener('mousemove', onDrag);
    document.addEventListener('mouseup', onDrop);
}

const updateScroller = (scrollPosition = -1, ignoreOffset = 0) => {
    const contentContainer = document.documentElement;
    scrollPosition = scrollPosition > -1 ? scrollPosition : contentContainer.scrollTop;
    const range = Math.max(1, contentContainer.scrollHeight - contentContainer.clientHeight - ignoreOffset);
    scrollerTop.value = Math.min(100, Math.max(0, scrollPosition - ignoreOffset) / range * 100);

    if (scrollerTop.value === 0) {
        forumPostStore.updateCurrentPostIndex(0);
    }
}

const handleScroll = () => {
    if (!isDragging.value) {
        updateScroller(window.scrollY, 200);
    }
};

const updateUnreadScrollPosition = () => {
    if (firstUnreadPostIndex.value === 0) {
        unreadScrollPosition.value = 0;
        return;
    }

    const firstUnreadPost = document.querySelector(`[data-index='${firstUnreadPostIndex.value}']`);
    if (!firstUnreadPost) {
        return;
    }

    const contentContainer = document.documentElement;
    const elementTop = firstUnreadPost.getBoundingClientRect().top + window.scrollY - 200;
    const scrollableHeight = contentContainer.scrollHeight - contentContainer.clientHeight;
    unreadScrollPosition.value = Math.min(Math.max((elementTop / scrollableHeight) * 100, 0), 90);
};

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
    STUDIP.eventBus.on('forum:jumpToPost', updateUnreadScrollPosition);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
    STUDIP.eventBus.off('forum:jumpToPost', updateUnreadScrollPosition);
});
</script>

<template>
    <div class="discussion-timeline">
        <div class="discussion-timeline__start">
            <button
                type="button"
                class="button-base"
                @click="jumpToPost(null, 0)"
                :title="$gettext('Zum ersten Beitrag')"
                :aria-label="$gettext('Zum ersten Beitrag')"
            >
                <StudipDateTime :iso="discussion.mkdate" :relative="true" />
            </button>
        </div>
        <nav class="navigation-area sr-only" :aria-label="$gettext('Beitragsnavigation')">
            <span aria-live="assertive">{{ postProgressText }}</span>
            <button
                v-if="isNewFrom && currentPostIndex !== firstUnreadPostIndex"
                type="button"
                @click="jumpToPost(null, firstUnreadPostIndex)"
            >
                {{ $gettext('Zum ersten ungelesenen Beitrag') }}
            </button>
            <button
                type="button"
                :disabled="currentPostIndex < 1"
                @click="jumpToPost(null, currentPostIndex - 1)"
            >
                {{ $gettext('Zum vorherigen Beitrag') }}
            </button>
            <button
                type="button"
                :disabled="currentPostIndex >= posts.length - 1"
                @click="jumpToPost(null, currentPostIndex + 1)"
            >
                {{ $gettext('Zum nächsten Beitrag') }}
            </button>
        </nav>
        <div
            id="scroll-area"
            class="scroll-area"
            aria-hidden="true"
            @click="jumpTo"
        >
            <div class="scroll-area__track" aria-hidden="true">
                <Transition name="fade">
                    <div
                        v-if="isNewFrom"
                        class="scroll-area__unread"
                        :style="{
                            top: `${unreadScrollPosition}%`
                        }"
                    >
                    </div>
                </Transition>
            </div>
            <Transition name="fade">
                <div
                    v-if="isNewFrom && currentPostIndex !== firstUnreadPostIndex"
                    class="scroll-area__new-from"
                     :style="{
                        top: `${unreadScrollPosition}%`
                    }">
                    <button
                        type="button"
                        class="button-base"
                        @click.stop="jumpToPost(null, firstUnreadPostIndex)"
                        :title="$gettext('Zum ersten ungelesenen Beitrag')"
                        aria-live="polite"
                    >
                        {{ $gettext('Neu ab hier') }}
                    </button>
                </div>
            </Transition>
            <button
                type="button"
                id="scroller"
                class="scroll-area__scroller"
                aria-hidden="true"
                tabindex="-1"
                :style="{
                    top: `${scrollerTop}%`,
                    transform: `translateY(-${scrollerTop}%)`,
                    cursor: posts.length > 1 ? 'ns-resize' : 'not-allowed'
                }"
                @mousedown.prevent="startDrag"
                @click.stop
            >
                <span class="scroll-area__scroll-marker" aria-hidden="true"></span>
                <span class="scroll-area__info">
                    {{ currentPostIndex + 1 }}/{{ posts.length }} <br />
                    <time
                        v-if="currentPostDate" :datetime="currentPostDate"
                        :aria-label="`${$gettext('Beitragsdatum')}: ${currentPostDate}`">
                        {{ currentPostDate }}
                    </time>
                    <Transition name="fade">
                        <span v-if="isNewFrom && currentPostIndex === firstUnreadPostIndex">
                            &mdash; {{ $gettext('Neu ab hier') }}
                        </span>
                    </Transition>
                </span>
            </button>
        </div>
        <div class="discussion-timeline__end">
            <button
                type="button"
                class="button-base"
                @click="jumpToPost(null, posts.length -1)"
                :title="$gettext('Zum letzten Beitrag')"
                :aria-label="$gettext('Zum letzten Beitrag')"
            >
                <StudipDateTime v-if="posts.length > 0" :iso="posts[posts.length -1].mkdate" :relative="true" />
                <StudipDateTime v-else :iso="discussion.mkdate" :relative="true" />
            </button>
        </div>
    </div>
</template>
