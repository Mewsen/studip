<template>
    <div class="cw-blubber-thread blubber_thread">
        <ol
            v-show="!loadingThreads && threadComments.length > 0"
            class="cw-blubber-comments comments"
            aria-live="polite"
            ref="commentsRef"
        >
            <courseware-blubber-comment
                v-for="comment in threadComments"
                :key="comment.id"
                :comment-id="comment.id"
                @delete="loadThread(threadId)"
                @editing="editingComment"
            />
        </ol>
        <courseware-companion-box
            v-show="!loadingThreads && threadComments.length === 0"
            class="cw-blubber-thread-empty"
            :msgCompanion="$gettext('Bisher wurde noch nicht diskutiert.')"
            mood="pointing"
        />
        <div v-show="!loadingThreads" class="cw-blubber-thread-add-comment">
            <textarea
                v-model="newComment"
                :placeholder="$gettext('Schreiben Sie eine Nachricht...')"
                spellcheck="true"
                @keydown.enter="createComment"
            ></textarea>
            <button class="button" @click="createComment">
                <translate>Absenden</translate>
            </button>
        </div>
        <studip-progress-indicator
            v-show="loadingThreads"
            class="cw-loading-indicator-blubber-comment"
            :description="$gettext('Lade Beiträge...')"
        />
    </div>
</template>

<script>
import CoursewareBlubberComment from './CoursewareBlubberComment.vue';
import CoursewareCompanionBox from './CoursewareCompanionBox.vue';
import StudipProgressIndicator from '../StudipProgressIndicator.vue';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-blubber-thread',
    components: {
        CoursewareBlubberComment,
        CoursewareCompanionBox,
        StudipProgressIndicator
    },
    props: {
        threadId: String,
    },
    data() {
        return {
            newComment: '',
            loadingThreads: true,
            updater: null,
            editingComments: false,
        }
    },
    computed: {
        ...mapGetters({
            blubberThreadById: 'blubber-threads/byId',
            blubberCommentsById: 'blubber-comments/byId',
        }),
        blubberThread() {
            return this.blubberThreadById({ id: this.threadId });
        },
        threadComments() {
            let comments = this.blubberThread?.relationships?.comments?.data;
            if (comments) {
                return comments;
            }
            return [];
        },
        threadTitle() {
            return this.blubberThread?.attributes?.content;
        }
    },
    methods: {
        ...mapActions({
            loadBlubberThread: 'loadBlubberThread',
            createBlubberComment: 'createBlubberComment',
            companionInfo: 'companionInfo',
        }),
        async createComment() {
            if (this.newComment) {
                await this.createBlubberComment({
                    threadId: this.threadId,
                    content: this.newComment
                });
                this.newComment = '';
            } else {
                this.companionInfo({ info: this.$gettext('Leere Beiträge können nicht erstellt werden.') });
            }
        },
        scrollDown() {
            this.$nextTick( () => {
                let ref = this.$refs["commentsRef"];
                if (ref) {
                    ref.scrollTop = ref.scrollHeight;
                }
            });
        },
        async loadThread(threadId) {
            await this.loadBlubberThread({ threadId: threadId});
            this.$emit('threadContent', this.threadTitle);
        },
        editingComment(event) {
            this.editingComments = event;
        }
    },
    mounted() {
        this.$nextTick(async() => {
            if (this.threadId) {
                await this.loadThread(this.threadId);
                this.loadingThreads = false;
                this.scrollDown();
            }
        });
        this.updater = setInterval(() => {
            if (this.threadId) {
                this.loadThread(this.threadId);
            }
        }, 10000);
    },
    beforeDestroy() {
        clearInterval(this.updater)
    },
    watch: {
        threadId(newId) {
            if (newId) {
                this.loadThread(newId);
            }
        },
        threadComments(newComments, oldComments) {
            if (newComments.length !== oldComments.length && !this.editingComments) {
                this.scrollDown();
            }
        }
    }
}
</script>
