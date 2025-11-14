<template>
    <li :class="{ 'talk-bubble-own-post': ownMessage }" class="talk-bubble-wrapper">
        <div v-if="!ownMessage" class="talk-bubble-avatar">
            <user-avatar-dropdown
                size="30px"
                :user="{
                    id: messageAuthor.id,
                    avatar_url: messageAvatar,
                    username: messageAuthor.username,
                    name: messageAuthor['formatted-name'],
                }"
            />
        </div>
        <div class="talk-bubble" :class="{ editing }">
            <div class="talk-bubble-content">
                <header v-if="!ownMessage" class="talk-bubble-header">
                    <a :href="userProfile">{{ messageAuthor['formatted-name'] }}</a>
                </header>
                <div class="talk-bubble-talktext">
                    <template v-if="!editing">
                        <div ref="html" v-html="message.attributes['content-html']" class="html"></div>
                        <div class="talk-bubble-footer">
                            <span class="talk-bubble-talktext-time"
                                ><studip-date-time :timestamp="messageMkdate" :relative="true"></studip-date-time
                            ></span>
                            <a
                                href="#"
                                v-if="message['is-writable']"
                                @click.prevent.stop="editComment"
                                class="edit_comment"
                                :title="$gettext('Bearbeiten')"
                            >
                                <studip-icon shape="edit" :size="14" />
                            </a>
                            <a
                                href="#"
                                @click.prevent="answerComment"
                                class="answer_comment"
                                :title="$gettext('Hierauf antworten')"
                            >
                                <studip-icon shape="reply" :size="14" />
                            </a>
                        </div>
                    </template>
                    <div v-else class="talk-bubble-edit">
                        <textarea
                            v-model="localText"
                            ref="textarea"
                            @input="setTextareaSize"
                            @focus="setTextareaSize"
                            @keydown.enter.exact.prevent="saveComment"
                            @keyup.escape.exact="doneEditing"
                        ></textarea>
                        <button @click="saveComment" :title="$gettext('Speichern')">
                            <studip-icon shape="accept" />
                        </button>
                        <button @click="doneEditing" :title="$gettext('Abbrechen')">
                            <studip-icon shape="decline" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </li>
</template>

<script setup>
import { computed } from 'vue';
import UserAvatarDropdown from '@/vue/components/avatar/UserAvatarDropdown.vue';

const props = defineProps({
    message: {
        type: Object,
        required: true,
    },
    editing: {
        type: Boolean,
        default: false,
    },
});

const messageAuthor = computed(() => {
    return props.message.relationships.author.data;
});

const messageAvatar = computed(() => {
    return messageAuthor.value?.avatar?.medium ?? 'https://studip81.local.test/chat/public/assets/images/avatars/user/nobody_medium.webp';
});

const messageMkdate = computed(() => {
    return new Date(props.message.mkdate) / 1000;
});

const ownMessage = computed(() => {
    return false; // TODO: Implement own message check
});
const userProfile = computed(() => {
    const username = messageAuthor.value?.username;
    return window.STUDIP.URLHelper.getURL('dispatch.php/profile', { username }, true);
});
</script>
