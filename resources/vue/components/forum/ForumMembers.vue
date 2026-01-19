<script setup>
import {computed, ref} from 'vue';
import {$gettext} from '@/assets/javascripts/lib/gettext';
import Dropdown from '@/vue/components/Dropdown.vue';
import StudipIcon from '@/vue/components/StudipIcon.vue';
import UserAvatar from '@/vue/components/UserAvatar.vue';
import UserAvatarDropdown from '@/vue/components/forum/UserAvatarDropdown.vue';

const props = defineProps({
    members: {
        type: Array,
        required: true,
    },
    limit: {
        type: Number,
        default: 4
    },
    size: {
        type: String,
        default: '25px',
    }
});

const showAllMembers = ref(false);
const activeUserAvatar = ref('');

const moderators = computed(() => props.members.filter(({ role }) => role === 'moderator'));
const authors = computed(() => props.members.filter(({ role }) => role === 'author'));

const remainedMembersCount = computed(() => props.members.length - props.members.slice(0, props.limit).length);
const isModerator = role => role === 'moderator';
</script>

<template>
    <ul class="forum-members">
        <li v-for="(user, index) in [...moderators, ...authors].slice(0, limit)" :key="index">
            <UserAvatarDropdown
                :user="user"
                :size="size"
                :label="isModerator(user.role) ? `${$gettext('Moderator')}: ${user.name}` : `${$gettext('Autor:in')}: ${user.name}`"
                :class="{
                    'moderator': isModerator(user.role)
                }"
            />
        </li>
        <li v-if="remainedMembersCount" class="remained-users" aria-live="polite">
            <Dropdown v-model="showAllMembers">
                <template #trigger>
                    <button
                        type="button"
                        class="remained-users__button"
                        @click="showAllMembers = !showAllMembers"
                        :title="$gettext('Alle Teilnehmende anzeigen')"
                        :aria-label="$gettext('Alle Teilnehmende anzeigen')"
                        aria-haspopup="menu"
                        :aria-expanded="showAllMembers"
                    >
                        <span class="remained-users__count" :style="{ width: size, height: size }">
                            +{{ remainedMembersCount }}
                        </span>
                    </button>
                </template>

                <template #content>
                    <div class="forum-users-dropdown">
                        <div class="user-group user-group--moderators">
                            <p class="user-group__title">{{ $gettext('Moderatoren') }}</p>
                            <ul class="user-group__list">
                                <li v-for="(user, index) in moderators" :key="index">
                                    <div
                                        v-if="activeUserAvatar !== user.id"
                                        class="user-item"
                                    >
                                        <div class="user-item__user">
                                            <img :src="user.avatar_url" :alt="user.name" />
                                            <p>{{ user.name }}</p>
                                        </div>
                                        <button
                                            type="button"
                                            @click="activeUserAvatar = user.id"
                                            :title="$gettext('Aufklappen')"
                                            :aria-label="$gettext('Aufklappen')"
                                            :aria-expanded="activeUserAvatar === user.id"
                                            :aria-controls="'user-avatar-' + user.id"
                                            class="show-avatar button-base">
                                            <StudipIcon shape="arr_1down" :size="15" aria-hidden="true" />
                                        </button>
                                    </div>
                                    <button
                                        v-else
                                        type="button"
                                        @click="activeUserAvatar = ''"
                                        :title="$gettext('Zuklappen')"
                                        :aria-label="$gettext('Zuklappen')"
                                        class="hide-avatar button-base">
                                        <StudipIcon shape="arr_1up" :size="15" aria-hidden="true" />
                                    </button>

                                    <UserAvatar v-if="activeUserAvatar === user.id" :id="'user-avatar-' + user.id" :user="user" />
                                </li>
                            </ul>
                        </div>
                        <template v-if="authors.length > 0">
                            <hr />
                            <div class="user-group">
                                <p class="user-group__title">{{ $gettext('Autor:in') }}</p>
                                <ul class="user-group__list">
                                    <li v-for="(user, index) in authors" :key="index">
                                        <div
                                            v-if="activeUserAvatar !== user.id"
                                            class="user-item"
                                        >
                                            <div class="user-item__user">
                                                <img :src="user.avatar_url" :alt="user.name" />
                                                <p>{{ user.name }}</p>
                                            </div>
                                            <button
                                                @click="activeUserAvatar = user.id"
                                                :title="$gettext('Aufklappen')"
                                                :aria-label="$gettext('Aufklappen')"
                                                class="show-avatar button-base">
                                                <StudipIcon shape="arr_1down" :size="15" aria-hidden="true" />
                                            </button>
                                        </div>
                                        <button
                                            v-else
                                            @click="activeUserAvatar = ''"
                                            :title="$gettext('Zuklappen')"
                                            :aria-label="$gettext('Zuklappen')"
                                            :aria-expanded="activeUserAvatar === user.id"
                                            :aria-controls="'user-avatar-' + user.id"
                                            class="hide-avatar button-base">
                                            <StudipIcon shape="arr_1up" :size="15" aria-hidden="true" />
                                        </button>
                                        <UserAvatar v-if="activeUserAvatar === user.id" :id="'user-avatar-' + user.id" :user="user" />
                                    </li>
                                </ul>
                            </div>
                        </template>
                    </div>
                </template>
            </Dropdown>
        </li>
    </ul>
</template>
