<template>
    <div role="alert"
         :class="'system-notifications ' + (placement === 'topcenter' ? 'top-center' : 'bottom-right')">
        <system-notification v-for="(notification, index) in allNotifications"
                             :key="'message-' + index"
                             :notification="notification"></system-notification>
    </div>
</template>

<script>
import SystemNotification from './SystemNotification.vue';

export default {
    name: 'SystemNotificationManager',
    components: { SystemNotification },
    props: {
        notifications: {
            type: Array,
            default: () => []
        },
        placement: {
            type: String,
            default: 'topcenter',
            validator: value => {
                return ['topcenter', 'bottomright'].includes(value);
            }
        },
        appendAllTo: {
            type: String,
            default: null
        }
    },
    data() {
        return {
            allNotifications: this.notifications
        }
    },
    methods: {
        getNotifications(type) {
            return this.allNotifications.filter((n) => n.type === type);
        },
        destroyNotification(type, index) {

        }
    },
    mounted() {
        this.globalOn('push-system-notification', notification => {
            this.allNotifications.push(notification);
        });
    }
}
</script>
