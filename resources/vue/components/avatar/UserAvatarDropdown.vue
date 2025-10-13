<script setup>
import {$gettext} from "@/assets/javascripts/lib/gettext";
import Dropdown from "@/vue/components/Dropdown.vue";
import UserAvatar from "@/vue/components/avatar/UserAvatar.vue";

defineProps({
    user: {
        type: Object,
        required: true
    },
    size: {
        type: String,
        default: '25px',
    },
    label: {
        type: String,
        default: ''
    }
});

const isOpen = defineModel({ default: false });
</script>
<template>
    <Dropdown class="user-avatar-dropdown" v-model="isOpen">
        <template #trigger>
            <button
                class="user-avatar-dropdown__preview"
                @click="isOpen = !isOpen"
                v-bind="$attrs"
                :class="{
                    'active': isOpen
                }"
                :title="label ?? user.name"
                :aria-label="label ?? $gettext('vCard')"
                :aria-pressed="isOpen"
            >
                <img class="user-profile" :src="user.avatar_url" :style="{ width: size, height: size }" :alt="user.name" />
            </button>
        </template>

        <template #content>
            <UserAvatar :user="user" v-model="isOpen" />
        </template>
    </Dropdown>
</template>
