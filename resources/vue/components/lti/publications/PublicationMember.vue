<script setup>
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipDateTime from "../../StudipDateTime.vue";
import UserAvatarDropdown from "@/vue/components/avatar/UserAvatarDropdown.vue";
import {userProfileURL} from "../helpers/urls";
import {onMounted, ref} from "vue";
import {useSortable} from "../../../composables/useSortable";

const props = defineProps({
    members: {
        type: Array,
        required: true
    }
});

const membersRef = ref(props.members);

const {
    sortedData: sortedMembers,
    sortBy,
    getSortClass,
    getAriaSortString,
    getAriaSortLabel
} = useSortable(membersRef);

onMounted(() => {
    sortBy('mkdate', 'desc');
});
</script>

<template>
    <table class="default forum-table --posts-reactors">
        <thead>
            <tr class="sortable">
                <th scope="col" style="width: 50px;"></th>
                <th
                    :class="getSortClass('user.name')"
                    :aria-sort="getAriaSortString('user.name')"
                    :aria-label="getAriaSortLabel('user.name', $gettext('Name'))"
                >
                    <button
                        type="button"
                        class="as-link"
                        @click="sortBy('user.name')"
                        :title="$gettext('Nach Name sortieren')">
                        {{ $gettext('Name') }}
                    </button>
                </th>
                <th
                    :class="getSortClass('mkdate')"
                    :aria-sort="getAriaSortString('mkdate')"
                    :aria-label="getAriaSortLabel('mkdate', $gettext('Einschreibedatum'))"
                >
                    <button
                        type="button"
                        class="as-link"
                        @click="sortBy('mkdate')"
                        :title="$gettext('Nach Einschreibedatum sortieren')">
                        {{ $gettext('Einschreibedatum') }}
                    </button>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(member, index) in sortedMembers" :key="index">
                <td>
                    <UserAvatarDropdown v-if="member.user.id" size="30px" :user="member.user" />
                </td>
                <td>
                    <a
                        v-if="member.user.id"
                        :href="userProfileURL(member.user.username)"
                        :title="$gettext('Zum Profil')"
                        :aria-label="$gettext('Zum Profil von %{name}', { name: member.user.name })"
                        class="author-name"
                    >
                        {{ member.user.name }}
                    </a>
                    <p v-else class="author-name">
                        {{ $gettext('Unbekannt') }}
                    </p>
                </td>
                <td>
                    <StudipDateTime :iso="member.mkdate" :relative="true" />
                </td>
            </tr>
        </tbody>
    </table>
</template>
