<template>
    <div
        v-if="commentUser"
        :class="{ 'cw-talk-bubble-own-post': ownComment }"
        class="cw-talk-bubble"
    >
        <div class="cw-talk-bubble-user" v-if="!ownComment">
            <div class="cw-talk-bubble-avatar">
                <img :src="userAvatar" />
            </div>
            <span>{{ userFormattedName }}</span>
        </div>
        <div v-show="!editActive" class="cw-talk-bubble-talktext">
            <p>
                <span>{{ content }}</span>
            </p>
            <p class="cw-talk-bubble-talktext-time">
                <iso-date :date="chdate" class="time"/>
                <button
                    v-if="ownComment"
                    :title="$gettext('Bearbeiten')"
                    class="cw-talk-bubble-button edit-button"
                    @click="editComment"
                >
                </button>
                <button
                    v-if="ownComment || userIsTeacher"
                    :title="$gettext('Löschen')"
                    class="cw-talk-bubble-button delete-button"
                    @click="showDeleteDialog = true"
                >
                </button>
            </p>
        </div>
        <div
            v-show="editActive"
            class="cw-talk-bubble-talktext cw-talk-bubble-talktext-edit"
        >
            <textarea
                v-model="currentContent"
                ref="commentedit"
                @keydown.enter="updateComment"
                @keydown.esc="resetComment"
            />
            <button
                class="button accept"
                :title="$gettext('Speichern')"
                @click="updateComment"
            >
            </button>
            <button
                class="button cancel"
                :title="$gettext('Abbrechen')"
                @click="resetComment"
            >
            </button>
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
    </div>
    <div v-else class="cw-talk-bubble">
        <studip-progress-indicator
            class="cw-loading-indicator-blubber-comment"
            :description="$gettext('Lade Beitrag...')"
        />
    </div>
</template>

<script>
import IsoDate from './IsoDate.vue';
import StudipDialog from '../StudipDialog.vue';
import StudipProgressIndicator from '../StudipProgressIndicator.vue';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-blubber-comment',
    components: {
        IsoDate,
        StudipDialog,
        StudipProgressIndicator,
    },
    props: {
        commentId: String,
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
            if(comment) {
                return comment;
            }
            return null;
        },
        content() {
            return this.comment?.attributes?.content;
        },
        chdate() {
            return this.comment?.attributes?.chdate;
        },
        userFormattedName() {
            if (this.commentUser) {
                return this.commentUser.attributes['formatted-name']
            }
            return '';
        },
        userAvatar() {
            if (this.commentUser) {
                return this.commentUser.meta.avatar.small;
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
            if(commentUserId) {
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
        editComment() {
            this.editActive = true;
            this.$nextTick(() => {
                this.adjustHeight();
            });
        },
        async updateComment() {
            if(this.currentContent === '') {
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
            this.$emit('editing', this.editActive);
        }
    }
}
</script>