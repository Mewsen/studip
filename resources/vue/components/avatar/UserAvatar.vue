<script setup>
import {$gettext} from "@/assets/javascripts/lib/gettext";
import StudipIcon from "@/vue/components/StudipIcon.vue";

const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

const isOpen = defineModel({ default: false });
const AUTH_ID = STUDIP.USER_ID;
const vCardDownloadURL = STUDIP.URLHelper.getURL('dispatch.php/contact/vcard', {'user[]': props.user.username});
const userProfileURL = STUDIP.URLHelper.getURL('dispatch.php/profile', {username: props.user.username});

const writeMessage = () => {
    STUDIP.Dialog.fromURL(
        STUDIP.URLHelper.getURL('dispatch.php/messages/write'),
        {
            method: 'get',
            data: {
                username: props.user.username,
                rec_uname: props.user.username
            }
        }
    );

    isOpen.value = false;
}

const openBlubberChat = () => {
    STUDIP.Dialog.fromURL(
        STUDIP.URLHelper.getURL(`dispatch.php/blubber/write_to/${props.user.id}`),
        {
            method: 'get'
        }
    );

    isOpen.value = false;
}
</script>
<template>
    <div class="user-avatar">
        <div class="user-avatar__header">
            <img class="user-profile" :src="user.avatar_url" :alt="user.name" />
            <div class="user-info">
                <p class="user-name">{{ user.name }}</p>
                <p v-if="user.motto">{{ user.motto }}</p>
            </div>
        </div>
        <hr />
        <ul class="user-avatar__actions">
            <li>
                <button
                    type="button"
                    v-if="user.id !== AUTH_ID"
                    @click="openBlubberChat"
                    class="action-item button-base"
                    :title="$gettext('Blubber diesen Nutzer an')"
                    :aria-label="$gettext('Blubber diesen Nutzer an')"
                >
                    <StudipIcon shape="blubber" :size="18" aria-hidden="true" />
                    {{ $gettext('Chat starten (blubbern)') }}
                </button>
            </li>
            <li>
                <a
                    class="action-item"
                    :href="userProfileURL"
                    :title="$gettext('Zum Profil von %{name}', { name: user.name })"
                    :aria-label="$gettext('Zum Profil von %{name}', { name: user.name })"
                >
                    <StudipIcon shape="role" :size="18" aria-hidden="true" />
                    {{ $gettext('Profil anzeigen') }}
                </a>
            </li>
            <li>
                <button
                    type="button"
                    v-if="user.id !== AUTH_ID"
                    class="action-item button-base"
                    :title="$gettext('Nachricht schreiben')"
                    :aria-label="$gettext('Nachricht schreiben')"
                    @click="writeMessage()"
                >
                    <StudipIcon shape="mail2" :size="18" aria-hidden="true" />
                    {{ $gettext('Nachricht schreiben') }}
                </button>
            </li>
            <li>
                <a
                    class="action-item"
                    :href="vCardDownloadURL"
                    :title="$gettext('vCard herunterladen')"
                    :aria-label="$gettext('vCard herunterladen')"
                >
                    <StudipIcon shape="vcard" :size="18" aria-hidden="true" />
                    {{ $gettext('Vcard speichern') }}
                </a>
            </li>
        </ul>
    </div>
</template>
