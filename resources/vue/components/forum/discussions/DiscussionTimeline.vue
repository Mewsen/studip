<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import StudipDateTime from '@/vue/components/StudipDateTime.vue';
import { useForumPost } from '@/vue/store/pinia/forum/ForumPost';
import { useForumConfig } from '@/vue/store/pinia/forum/ForumConfig';
import { $gettext } from '@/assets/javascripts/lib/gettext';

const forumConfig = useForumConfig();
const forumPostStore = useForumPost();

defineProps({
    discussion: { type: Object, required: true },
});

const scrollPercentage = ref(0);
const unreadPosition = ref(0);

const posts = computed(() => forumPostStore.posts);
const currentPostIndex = computed(() => forumPostStore.currentPostIndex);
const firstUnreadPostIndex = computed(() => forumPostStore.firstUnreadPostIndex);
const currentPost = computed(() => posts.value[currentPostIndex.value]);

const currentPostDate = computed(() => {
    const post = currentPost.value;
    return post ? new Date(post.mkdate).toLocaleString(String.locale, { month: 'long', year: 'numeric' }) : null;
});

const isNewFrom = computed(() => firstUnreadPostIndex.value > -1 && !forumConfig.allowGuestAccess);

const ariaValueText = computed(() => {
    if (!currentPost.value) return '';
    return $gettext('Beitrag %{current} von %{total}, %{date}')
        .replace('%{current}', currentPostIndex.value + 1)
        .replace('%{total}', posts.value.length)
        .replace('%{date}', currentPostDate.value);
});

const getScrollPercent = () => {
    const root = document.documentElement;
    const scrollable = root.scrollHeight - root.clientHeight;
    return scrollable > 0 ? (window.scrollY / scrollable) * 100 : 0;
};

const getPostPositionPercent = (index) => {
    if (index <= 0) return 0;
    const element = document.querySelector(`[data-index='${index}']`);
    if (!element) return 0;
    const root = document.documentElement;
    const scrollable = root.scrollHeight - root.clientHeight;
    const elementTop = element.getBoundingClientRect().top + window.scrollY;
    return Math.min(100, (elementTop / scrollable) * 100);
};

const updateMetrics = () => {
    scrollPercentage.value = getScrollPercent();
    if (isNewFrom.value) {
        unreadPosition.value = getPostPositionPercent(firstUnreadPostIndex.value);
    }
};

const onSliderInput = (e) => {
    const percent = parseFloat(e.target.value);
    const root = document.documentElement;
    const scrollable = root.scrollHeight - root.clientHeight;
    window.scrollTo(0, (percent / 100) * scrollable);
};

const jumpToPost = (index) => {
    const element =
        index === 0 ? document.getElementById('discussion_start') : document.querySelector(`[data-index='${index}']`);
    element?.scrollIntoView({ behavior: 'smooth' });
};

const onKeyDown = (e) => {
    if (e.key === 'ArrowUp') {
        e.preventDefault();
        jumpToPost(Math.max(0, currentPostIndex.value - 1));
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        jumpToPost(Math.min(posts.value.length - 1, currentPostIndex.value + 1));
    }
};

onMounted(() => {
    window.addEventListener('scroll', updateMetrics);
    window.addEventListener('resize', updateMetrics);
    updateMetrics();
});

onUnmounted(() => {
    window.removeEventListener('scroll', updateMetrics);
    window.removeEventListener('resize', updateMetrics);
});
</script>

<template>
    <div class="discussion-timeline">
        <button class="timeline-anchor" @click="jumpToPost(0)">
            <StudipDateTime :iso="discussion.mkdate" :relative="true" />
        </button>

        <div class="slider-container">
            <span class="sr-only" id="timeline-label">
                {{ $gettext('Beitragsnavigation der Diskussion') }}
            </span>
            <span class="sr-only" id="timeline-instructions">
                {{
                    $gettext(
                        'Nutzen Sie die Pfeiltasten Links und Rechts für eine feine Navigation. Die Tasten Hoch und Runter springen direkt zwischen den einzelnen Beiträgen.',
                    )
                }}
            </span>
            <div class="slider-track">
                <div v-if="isNewFrom" class="unread-marker" :style="{ top: unreadPosition + '%', bottom: 0 }"></div>
            </div>

            <div
                v-if="isNewFrom && currentPostIndex < firstUnreadPostIndex"
                class="new-posts-label"
                :style="{ top: unreadPosition + '%' }"
            >
                <button type="button" @click="jumpToPost(firstUnreadPostIndex)">
                    {{ $gettext('Neu ab hier') }}
                </button>
            </div>

            <input
                type="range"
                min="0"
                max="100"
                step="1"
                :value="scrollPercentage"
                @input="onSliderInput"
                @keydown="onKeyDown"
                class="timeline-slider"
                aria-labelledby="timeline-label"
                aria-describedby="timeline-instructions"
                :aria-valuetext="ariaValueText"
            />

            <div
                class="floating-info"
                :style="{
                    top: scrollPercentage + '%',
                    transform: `translateY(-${scrollPercentage}%)`,
                }"
            >
                <span class="marker"></span>
                <div class="label">
                    {{ currentPostIndex + 1 }}/{{ posts.length }}<br />
                    <time>{{ currentPostDate }}</time>
                </div>
            </div>
        </div>

        <button class="timeline-anchor" @click="jumpToPost(posts.length - 1)">
            <StudipDateTime :iso="posts[posts.length - 1]?.mkdate || discussion.mkdate" :relative="true" />
        </button>
    </div>
</template>

<style scoped lang="scss">
.discussion-timeline {
    position: sticky;
    top: 50px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 1rem;
    width: 220px;

    .timeline-anchor {
        background: none;
        border: none;
        color: var(--color--highlight);
        font-weight: bold;
        cursor: pointer;
        padding: 0;
        text-align: left;
    }

    .slider-container {
        position: relative;
        height: 300px;
        width: 100%;
        padding-left: 10px;

        .slider-track {
            position: absolute;
            left: 10px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--dark-gray-color-20);

            .unread-marker {
                position: absolute;
                background: var(--red);
                width: 100%;
                left: 0;
            }
        }

        .new-posts-label {
            position: absolute;
            left: 25px;
            z-index: 5;
            transform: translateY(-50%);
            white-space: nowrap;

            button {
                background: var(--color--global-background);
                border: none;
                color: var(--color--highlight);
                cursor: pointer;
                font-weight: 700;
                margin-top: 40px;
                padding: 0;
            }
        }

        .timeline-slider {
            position: absolute;
            top: 0;
            left: 0;
            width: 300px;
            height: 40px;
            margin: 0;
            cursor: pointer;
            opacity: 0;
            z-index: 10;
            transform-origin: 0 0;
            transform: rotate(90deg) translate(0, -40px);
            appearance: none;

            &::-webkit-slider-thumb {
                appearance: none;
                width: 50px;
                height: 40px;
            }

            &:focus-visible + .floating-info .marker {
                box-shadow:
                    0 0 0 2px white,
                    0 0 0 4px var(--color--highlight);
            }
        }

        .floating-info {
            position: absolute;
            left: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            pointer-events: none;
            z-index: 2;

            .marker {
                width: 6px;
                height: 40px;
                background: var(--color--highlight);
                border-radius: 3px;
                margin-left: -2px;
                flex-shrink: 0;
                transition: box-shadow 0.2s ease;
            }

            .label {
                font-weight: 700;
                color: var(--color--highlight);
                line-height: 1.1;
                background: var(--color--global-background);
                padding: 2px 0;
            }
        }
    }
}
</style>
