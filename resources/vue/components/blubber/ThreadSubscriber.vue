<template>
    <div class="context_info">
        <a
            href="#"
            @click.prevent="onClick"
            class="followunfollow"
            :class="{ unfollowed: !followed }"
            :title="$gettext('Benachrichtigungen für diese Konversation abstellen.')"
        >
            <StudipIcon v-if="!followed" shape="decline" :size="20" class="text-bottom"></StudipIcon>
            <StudipIcon v-else shape="notification2" :size="20" class="text-bottom"></StudipIcon>
            {{ $gettext('Benachrichtigungen aktiviert') }}
        </a>
    </div>
</template>
<script lang="ts">
import { defineComponent } from 'vue';

export default defineComponent({
    props: {
        followed: {
            type: Boolean,
            required: true,
        },
    },
    methods: {
        onClick() {
            this.$emit('subscribe-thread', !this.followed);
        },
    },
});
</script>
