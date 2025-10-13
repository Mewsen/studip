<script setup>
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
    },
    withName: {
        type: Boolean,
        default: false
    }
});

const isOpen = defineModel({ default: false });
</script>
<template>
    <Dropdown class="user-avatar-dropdown" v-model="isOpen">
        <template #trigger>
            <button
                class="user-avatar-dropdown__preview button-base"
                @click="isOpen = !isOpen"
                v-bind="$attrs"
                :class="{
                    'active': isOpen
                }"
                :title="label ?? user.name"
                :aria-label="label ?? user.name"
                :aria-pressed="isOpen"
            >
                <img class="user-profile" :src="user.avatar_url" :style="{ width: size, height: size }" :alt="user.name" />
                <span v-if="withName">
                    {{ user.name }}
                </span>
            </button>
        </template>

        <template #content>
            <UserAvatar :user="user" v-model="isOpen" />
        </template>
    </Dropdown>
</template>
