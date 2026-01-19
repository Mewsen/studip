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
        default: null
    }
});

const isOpen = defineModel({ default: false });
</script>
<template>
    <Dropdown class="user-avatar-dropdown" v-model="isOpen">
        <template #trigger>
            <button
                type="button"
                class="user-avatar-dropdown__preview button-base"
                @click="isOpen = !isOpen"
                v-bind="$attrs"
                :class="{
                    'active': isOpen
                }"
                :title="label ?? $gettext('Avatar-Menü öffnen')"
                :aria-label="$gettext('Avatar-Menü für „%{ context }“ öffnen', { context: user.name })"
                aria-haspopup="menu"
                :aria-expanded="isOpen"
            >
                <img class="user-profile" :src="user.avatar_url" :style="{ width: size, height: size }" :alt="user.name" />
            </button>
        </template>

        <template #content>
            <UserAvatar :user="user" v-model="isOpen" :id="'user-avatar-' + user.id" />
        </template>
    </Dropdown>
</template>
