<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipDateTime from "../../StudipDateTime.vue";
import UserAvatarDropdown from "@/vue/components/avatar/UserAvatarDropdown.vue";
import {REACTION_ICONS} from "./reactions";
import {userProfileURL} from "../helpers/urls";
import {computed, onMounted} from "vue";
import {useSortable} from "../../../composables/useSortable";

const props = defineProps({
    reactions: {
        type: Array,
        required: true
    },
    emoji: {
        type: String,
        default: 'all'
    }
});

const computedReactions = computed(() => {
    if (props.emoji === 'all') {
        return props.reactions;
    }

    return props.reactions.filter(({ emoji }) => emoji === props.emoji);
});

const {
    sortedData: sortedReactions,
    sortBy,
    getSortClass,
    getAriaSortString,
    getAriaSortLabel
} = useSortable(computedReactions);

onMounted(() => {
    sortBy('mkdate', 'desc');
});
</script>

<template>
    <table class="default forum-table --posts-reactors">
        <colgroup>
            <col style="width: 50px;">
            <col>
            <col>
        </colgroup>
        <thead>
            <tr class="sortable">
                <th></th>
                <th
                    :class="getSortClass('user.formatted_name')"
                    :aria-sort="getAriaSortString('user.formatted_name')"
                    :aria-label="getAriaSortLabel('user.formatted_name', $gettext('Name'))"
                >
                    <a
                        href="#"
                        @click.prevent="sortBy('user.formatted_name')"
                        :title="$gettext('Nach Name sortieren')">
                        {{ $gettext('Name') }}
                    </a>
                </th>
                <th
                    :class="getSortClass('mkdate')"
                    :aria-sort="getAriaSortString('mkdate')"
                    :aria-label="getAriaSortLabel('mkdate', $gettext('Datum'))"
                >
                    <a
                        href="#"
                        @click.prevent="sortBy('mkdate')"
                        :title="$gettext('Nach Datum sortieren')">
                        {{ $gettext('Datum') }}
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(reaction, index) in sortedReactions" :key="index">
                <td>
                    <div class="user-reaction">
                        <UserAvatarDropdown
                            size="30px"
                            v-if="reaction.user.id"
                            :user="{
                                id: reaction.user.id,
                                username: reaction.user.username,
                                name: reaction.user.formatted_name,
                                avatar_url: reaction.user.meta.avatar.medium
                            }"
                        />
                        <span class="emoji-icon" v-html="REACTION_ICONS[reaction.emoji].icon"></span>
                        <span class="sr-only">{{ emoji }}</span>
                    </div>
                </td>
                <td>
                    <a
                        v-if="reaction.user.id"
                        :href="userProfileURL(reaction.user.username)"
                        :title="$gettext('Zum Profil')"
                        :aria-label="$gettext('Zum Profil von %{name}', { name: reaction.user.formatted_name })"
                        class="author-name"
                    >
                        {{ reaction.user.formatted_name }}
                    </a>
                    <p v-else class="author-name">
                        {{ $gettext('Unbekannt') }}
                    </p>
                </td>
                <td>
                    <StudipDateTime :iso="reaction.mkdate" :relative="true" />
                </td>
            </tr>
        </tbody>
    </table>
</template>
