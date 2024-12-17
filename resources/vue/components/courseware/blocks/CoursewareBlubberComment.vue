<template>
    <li
        v-if="commentUser"
        :class="{ 'talk-bubble-own-post': ownComment }"
        class="talk-bubble-wrapper"
    >
        <div v-if="!ownComment" class="talk-bubble-avatar">
            <a :href="userProfileURL" :title="userFormattedName">
                <img :src="userAvatar" />
            </a>
        </div>
        <div class="talk-bubble" :class="{ editing: editActive }">
            <div class="talk-bubble-content">
                <header v-if="!ownComment" class="talk-bubble-header">
                    <a :href="userProfileURL">{{ userFormattedName }}</a>
                </header>
                <div class="talk-bubble-talktext">
                    <template v-if="!editActive">
                        <div v-html="comment.attributes['content-html']" class="html"></div>
                        <div class="talk-bubble-footer">
                            <span class="talk-bubble-talktext-time"><studip-date-time :timestamp="chdate"
                                    :relative="true"></studip-date-time></span>
                            <a href="#" v-if="ownComment" @click.prevent.stop="editComment" class="edit_comment"
                                :title="$gettext('Bearbeiten')">
                                <studip-icon shape="edit" :size="14" />
                            </a>
                            <a href="#" @click.prevent="answerComment" class="answer_comment"
                                :title="$gettext('Hierauf antworten')">
                                <studip-icon shape="reply" :size="14" />
                            </a>
                            <a href="#" v-if="ownComment || userIsTeacher" @click.prevent="showDeleteDialog = true" class="answer_comment"
                                :title="$gettext('Löschen')">
                                <studip-icon shape="trash" :size="14" />
                            </a>

                        </div>
                    </template>
                    <div v-else class="talk-bubble-edit">
                    <textarea
                        v-model="currentContent"
                        ref="commentedit"
                        @keydown.enter.exact.prevent="updateComment"
                        @keyup.escape.exact="resetComment"
                    ></textarea>
                        <button @click="updateComment" :title="$gettext('Speichern')">
                            <studip-icon shape="accept" />
                        </button>
                        <button @click="resetComment" :title="$gettext('Abbrechen')">
                            <studip-icon shape="decline" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <studip-dialog
            v-if="showDeleteDialog"
            :title="$gettext('Beitrag löschen')"
            :question="$gettext('Möchten Sie diesen Beitrag wirklich löschen')"
            height="180"
            width="360"
            @confirm="deleteComment"
            @close="showDeleteDialog = false"
        ></studip-dialog>
    </li>
    <li v-else class="cw-talk-bubble">
        <studip-progress-indicator
            class="cw-loading-indicator-blubber-comment"
            :description="$gettext('Lade Beitrag…')"
        />
    </li>
</template>

<script>
import StudipDialog from '../../StudipDialog.vue';
import StudipProgressIndicator from '../../StudipProgressIndicator.vue';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-blubber-comment',
    components: {
        StudipDialog,
        StudipProgressIndicator,
    },
    props: {
        commentId: String,
        editing: Boolean,
    },
    data() {
        return {
            editActive: false,
            currentContent: '',
            showDeleteDialog: false
        }
    },
    computed: {
        ...mapGetters({
            blubberCommentsById: 'blubber-comments/byId',
            userId: 'userId',
            usersById: 'users/byId',
            userIsTeacher: 'userIsTeacher',
        }),
        comment() {
            let comment = this.blubberCommentsById({ id: this.commentId });
            if (comment) {
                return comment;
            }
            return null;
        },
        content() {
            return this.comment?.attributes?.content;
        },
        chdate() {
            return new Date(this.comment?.attributes?.chdate) / 1000;
        },
        userFormattedName() {
            if (this.commentUser) {
                return this.commentUser.attributes['formatted-name']
            }
            return '';
        },
        userAvatar() {
            if (this.commentUser) {
                return this.commentUser.meta.avatar.medium;
            }
            return '';
        },
        userProfileURL() {
            if (this.commentUser) {
                return STUDIP.URLHelper.base_url + 'dispatch.php/profile?username=' + this.commentUser.attributes.username;
            }
            return '';
        },
        ownComment() {
            if (this.commentUser && this.commentUser.id === this.userId) {
                return true;
            }
            return false;
        },
        commentUser() {
            let commentUserId = this.comment?.relationships?.author?.data?.id;
            if (commentUserId) {
                return this.usersById({ id: commentUserId });
            }
            return null;
        }
    },
    methods: {
        ...mapActions({
            loadUsers: 'users/loadById',
            updateBlubberComment: 'updateBlubberComment',
            companionWarning: 'companionWarning',
            deleteBlubberComment: 'deleteBlubberComment'
        }),
        initCurrent() {
            this.currentContent = this.content;
        },
        adjustHeight() {
            let textarea = this.$refs.commentedit;
            textarea.style.height = textarea.scrollHeight + 'px';
        },
        answerComment() {
            const quoteContent = this.content.replace(/\[quote[^\]]*\].*\[\/quote\]/g, '').trim();
            const quote = `[quote=${this.userFormattedName}]${quoteContent} [/quote]\n`;
            this.$emit('answer', quote);
        },
        editComment() {
            this.editActive = true;
            this.$nextTick(() => {
                this.adjustHeight();
                this.$refs.commentedit.focus();
            });
        },
        async updateComment() {
            if (this.currentContent === '') {
                this.companionWarning({
                    info: this.$gettext('Bitte schreiben Sie etwas in das Textfeld.')
                });
            }
            await this.updateBlubberComment({
                content: this.currentContent,
                id: this.comment.id
            });
            this.editActive = false;
        },
        resetComment() {
            this.currentContent = this.content;
            this.editActive = false;
        },
        deleteComment() {
            this.deleteBlubberComment({
                id: this.commentId
            });
            this.$emit('delete');
        }
    },
    mounted() {
        this.initCurrent();
    },
    watch: {
        editActive(edit) {
            this.$emit('editing', this.editActive ? this.commentId : null);
        },
        editing(edit) {
            if (edit) {
                this.editComment();
            }
        }
    }
}
</script>
