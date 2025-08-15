<script setup>
import ForumApp from "@/vue/components/forum/ForumApp.vue";
import {onMounted, ref} from "vue";
import {getDiscussionURL, getTopicURL} from "@/vue/components/forum/helpers/urls";
import {useSortable} from "../../../composables/useSortable";
import {$gettext} from "../../../../assets/javascripts/lib/gettext";
import StudipIcon from "../../../components/StudipIcon.vue";
import StudipDateTime from "../../../components/StudipDateTime.vue";
import SubscriptionDropdown from "@/vue/components/forum/SubscriptionDropdown.vue";
import {deserializeJSONAPIResponse} from "../../../../assets/javascripts/lib/jsonapiUtils";
import {subscriptionTransformer} from "../../../components/forum/helpers/transformers";
import StudipPagination from "../../../components/StudipPagination.vue";
import Loader from "../../../components/forum/Loader.vue";

const subscriptions = ref([]);
const pagination = ref({});
const isLoading = ref(false);

const removeSubscription = subscription_id => {
    subscriptions.value = subscriptions.value.filter(({ id }) => id !== subscription_id);
}

const getSubjectLabel = type => {
    switch (type) {
        case 'forum-discussions':
            return $gettext('Diskussion');
        case 'forum-topics':
            return $gettext('Thema');
        default:
            return $gettext('Unbekannt');
    }
}

const getSubscriptionDropdownTitle = type => {
    switch (type) {
        case 'forum-discussions':
            return $gettext('Diskussion abonnieren');
        case 'forum-topics':
            return $gettext('Thema abonnieren');
        default:
            return $gettext('Abonnieren');
    }
}

const fetchSubscribedDiscussions = async (_, offset = 0) => {
    try {
        isLoading.value = true;

        const response = await STUDIP.jsonapi.withPromises().GET(
            `courses/${STUDIP.URLHelper.parameters.cid}/forum-subscriptions`,
            {
                data: { include: 'subject', page: { offset } }
            }
        );

        pagination.value = {
            ...response.meta.page,
            currentPage: response.meta.page.offset / response.meta.page.limit,
            links: response.links
        };

        const data = await deserializeJSONAPIResponse(response)

        subscriptions.value = data.map(subscriptionTransformer);
    } catch (error) {
        STUDIP.Report.error(error.statusText);
    } finally {
        isLoading.value = false;
    }
}

const {
    sortedData,
    sortBy,
    getSortClass,
    getAriaSortString,
    getAriaSortLabel
} = useSortable(subscriptions);

onMounted(async () => {
    await fetchSubscribedDiscussions();
});
</script>

<template>
    <ForumApp class="use-utility-classes">
        <header class="header">
            <div class="header__content header__content--with-actions">
                <div class="actions">
                    <h2>
                        {{ $gettext('Abonnements') }}
                    </h2>
                </div>

                <div class="actions">

                </div>
            </div>
        </header>
        <div class="py-10">
            <table class="default forum-table --subscription-index">
                <colgroup>
                    <col>
                    <col style="width: 5%">
                    <col style="width: 20%">
                    <col style="width: 15%">
                    <col style="width: 15%">
                </colgroup>
                <thead>
                    <tr class="sortable">
                    <th
                        :class="getSortClass('subject.name')"
                        :aria-sort="getAriaSortString('subject.name')"
                        :aria-label="getAriaSortLabel('subject.name', $gettext('Thema Name'))"
                    >
                        <a
                            href="#"
                            @click.prevent="sortBy('subject.name')"
                            :title="$gettext('Nach Thema Name sortieren')">
                            {{ $gettext('Thema') }}
                        </a>
                    </th>
                    <th></th>
                    <th
                        :class="getSortClass('subject.type')"
                        :aria-sort="getAriaSortString('subject.type')"
                        :aria-label="getAriaSortLabel('subject.type', $gettext('Typ'))"
                    >
                        <a
                            href="#"
                            @click.prevent="sortBy('subject.type')"
                            :title="$gettext('Nach Typ sortieren')">
                            {{ $gettext('Typ') }}
                        </a>
                    </th>
                    <th
                        :class="getSortClass('mkdate')"
                        :aria-sort="getAriaSortString('mkdate')"
                        :aria-label="getAriaSortLabel('mkdate', $gettext('Abonniert datum'))"
                    >
                        <a
                            href="#"
                            @click.prevent="sortBy('mkdate')"
                            :title="$gettext('Nach Abonniert am sortieren')">
                            {{ $gettext('Abonniert am') }}
                        </a>
                    </th>
                    <th
                        class="actions"
                        :class="getSortClass('notification_type')"
                        :aria-sort="getAriaSortString('notification_type')"
                        :aria-label="getAriaSortLabel('notification_type', $gettext('Typ des Abonnements'))"
                    >
                        <a
                            href="#"
                            @click.prevent="sortBy('notification_type')"
                            :title="$gettext('Nach Typ des Abonnements sortieren')">
                            {{ $gettext('Typ des Abonnements') }}
                        </a>
                    </th>
                </tr>
                </thead>
                <tbody>
                    <tr v-if="isLoading" >
                        <td colspan="5">
                            <Loader />
                        </td>
                    </tr>
                    <tr v-else v-for="subscription in sortedData" :key="subscription.id">
                    <td>
                        <div class="table-row-overview">
                            <div class="title-with-actions">
                                <div class="title-with-actions__content">
                                    <a v-if="subscription.subject.type === 'forum-topics'" :href="getTopicURL(subscription.subject.id)" :title="$gettext('Zum Thema')">
                                        <span class="subscription-title as-link line-clamp-2">{{ subscription.subject.name }}</span>
                                    </a>
                                    <a v-else-if="subscription.subject.type === 'forum-discussions'" :href="getDiscussionURL(subscription.subject.id)" :title="$gettext('Zur Diskussion')">
                                        <span class="subscription-title as-link line-clamp-2">{{ subscription.subject.title }}</span>
                                    </a>
                                </div>

                                <div class="title-with-actions__actions-xs">
                                    <SubscriptionDropdown
                                        :title="getSubscriptionDropdownTitle(subscription.subject.type)"
                                        :subject="subscription.subject"
                                        :subject_id="subscription.subject_id"
                                        :user_subscription="subscription"
                                        @deleted="removeSubscription(subscription.id)"
                                    />
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <StudipIcon
                            v-if="subscription.subject.type === 'forum-discussions' && subscription.subject.closed_at"
                            :title="$gettext('Diskussion ist geschlossen')"
                            shape="lock-locked2"
                            :size="20"
                            role="inactive" />
                    </td>
                    <td>
                        {{ getSubjectLabel(subscription.subject.type) }}
                    </td>
                    <td>
                        <StudipDateTime :iso="subscription.mkdate" :relative="true" />
                    </td>
                    <td class="actions">
                        <div class="inline-flex">
                            <SubscriptionDropdown
                                :title="getSubscriptionDropdownTitle(subscription.subject.type)"
                                :subject="subscription.subject"
                                :user_subscription="subscription"
                                @deleted="removeSubscription(subscription.id)"
                            />
                        </div>
                    </td>
                </tr>
                </tbody>
                <tfoot v-if="pagination.total > pagination.limit">
                    <tr>
                        <td colspan="5">
                            <StudipPagination
                                :currentPage="pagination.currentPage"
                                :totalItems="pagination.total"
                                :itemsPerPage="pagination.limit"
                                @pageUpdated="fetchSubscribedDiscussions" />
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </ForumApp>
</template>
