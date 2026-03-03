<script setup>
import {computed, ref} from 'vue';
import {$gettext} from '@/assets/javascripts/lib/gettext';
import Dropdown from '@/vue/components/Dropdown.vue';
import StudipIcon from '@/vue/components/StudipIcon.vue';
import {SubscriptionNotificationType} from '@/vue/components/forum/enums/SubscriptionNotificationType';
import {deserializeJSONAPIResponse} from '@/assets/javascripts/lib/jsonapiUtils';

const emit = defineEmits(['updated', 'deleted']);
const props = defineProps({
    userSubscription: {
        type: Object,
        required: true
    },
    subject: {
        type: Object,
        required: true
    },
    type: {
        type: String,
        default: $gettext('Diskussion')
    },
    context: {
        type: String,
        default: ''
    }
});

const isOpen = ref(false);
const subscription = ref(props.userSubscription);
const isLoading = ref(false);

const subscriptionButtonLabel = computed(() =>  {
    if (subscription.value) {
        switch (subscription.value.notification_type) {
            case SubscriptionNotificationType.All:
                return $gettext('Alle');
            case SubscriptionNotificationType.RepliesOnly:
                return $gettext('Nur Zitate');
            case SubscriptionNotificationType.None:
                return $gettext('Keine');
        }
    }

    return '';
})

const subscriptionButtonIcon = computed(() =>  {
    if (subscription.value) {
        switch (subscription.value.notification_type) {
            case SubscriptionNotificationType.All:
                return 'subscription-all';
            case SubscriptionNotificationType.RepliesOnly:
                return 'subscription-quotes';
            case SubscriptionNotificationType.None:
                return 'subscription-none';
        }
    }

    return 'subscription-all';
});

const title = computed(() => $gettext('%{type} abonnieren', {type: props.type }));
const computedContext = computed(() => props.context || props.subject.name || props.subject.title);

const getSubscriptionJSONAPIObject = (notificationType = 'all') => ({
    data: {
        id: subscription.value?.id,
        type: 'forum-subscriptions',
        attributes: {
            'notification-type': notificationType
        },
        relationships: {
            subject: {
                data: {
                    type: props.subject.type,
                    id: props.subject.id
                }
            },
            range: {
                data: {
                    type: 'courses',
                    id: STUDIP.URLHelper.parameters.cid
                }
            }
        }
    }
})

const unSubscribe = async () => {
    if (!subscription.value?.notification_type) {
        return;
    }

    try {
        isLoading.value = true;

        await STUDIP.jsonapi.withPromises().DELETE(`forum-subscriptions/${subscription.value.id}`);

        emit('deleted', subscription);
        subscription.value = null;

        STUDIP.Report.success($gettext('Sie haben das Abonnement erfolgreich beendet.'));
    } catch (error) {
        STUDIP.Report.error(error);
    } finally {
        isLoading.value = false;
    }
}

const subscribe = async (notificationType = 'all') => {
    try {
        isLoading.value = false;

        const response = await STUDIP.jsonapi.withPromises().POST(
            'forum-subscriptions',
            {
                data: getSubscriptionJSONAPIObject(notificationType)
            }
        );

        const data = await deserializeJSONAPIResponse(response);
        subscription.value = data;
        emit('updated', data);

        STUDIP.Report.success($gettext('Erfolgreich abonniert!'), subscriptionButtonLabel);
    } catch (error) {
        STUDIP.Report.error(error);
    } finally {
        isLoading.value = false;
    }
}
</script>

<template>
    <Dropdown class="forum-subscriptions-dropdown" v-model="isOpen" :title="title">
        <template #trigger>
            <button
                type="button"
                class="button subscription-button"
                :class="subscriptionButtonLabel ? 'button--icon-label' : 'button--icon-only'"
                :title="$gettext('%{type} abonnieren (Menü öffnen)', { type })"
                :aria-label="$gettext('Menü zum Abonnieren für „%{ context }“ öffnen)', { context: computedContext ?? type })"
                aria-haspopup="menu"
                :aria-expanded="isOpen"
                @click="isOpen = !isOpen"
            >
                <span v-if="subscriptionButtonLabel">
                    {{ subscriptionButtonLabel }}
                </span>
                <StudipIcon :shape="subscriptionButtonIcon" :size="20" />
            </button>
        </template>

        <template #items>
            <li>
                <button
                    role="menuitem"
                    type="button"
                    class="button-base"
                    :class="{
                        'active': subscription?.notification_type === SubscriptionNotificationType.All
                    }"
                    @click="subscribe(SubscriptionNotificationType.All)"
                >
                    <StudipIcon shape="subscription-all" :size="20" />
                    <span class="subscription-option">
                        <span class="option-title">{{ $gettext('Alle Benachrichtigungen') }}</span>
                        <StudipIcon
                            v-if="subscription?.notification_type === SubscriptionNotificationType.All"
                            shape="accept"
                            :size="20"
                            role="accept" />
                    </span>
                </button>
            </li>
            <li>
                <button
                    role="menuitem"
                    type="button"
                    class="button-base"
                    :class="{
                        'active': subscription?.notification_type === SubscriptionNotificationType.RepliesOnly
                    }"
                    @click="subscribe(SubscriptionNotificationType.RepliesOnly)"
                >
                    <StudipIcon shape="subscription-quotes" :size="20" />
                    <span class="subscription-option">
                        <span class="option-title">{{ $gettext('Nur Zitat') }}</span>
                        <StudipIcon
                            v-if="subscription?.notification_type === SubscriptionNotificationType.RepliesOnly"
                            shape="accept"
                            :size="20"
                            role="accept" />
                    </span>
                </button>
            </li>
            <li>
                <button
                    role="menuitem"
                    type="button"
                    class="button-base"
                    :class="{
                        'active': subscription?.notification_type === SubscriptionNotificationType.None
                    }"
                    @click="subscribe(SubscriptionNotificationType.None)"
                >
                    <StudipIcon shape="subscription-none" :size="20" />
                    <span class="subscription-option">
                        <span class="option-title">{{ $gettext('Keine') }}</span>
                        <StudipIcon
                            v-if="subscription?.notification_type === SubscriptionNotificationType.None"
                            shape="accept"
                            :size="20"
                            role="accept" />
                    </span>
                </button>
            </li>
            <li>
                <button
                    role="menuitem"
                    type="button"
                    class="button-base"
                    :disabled="!subscription?.notification_type"
                    @click="unSubscribe"
                >
                    <StudipIcon shape="subscription-end" :size="20" />
                    <p class="option-title">{{ $gettext('Abonnieren beenden') }}</p>
                </button>
            </li>
        </template>
    </Dropdown>
</template>
