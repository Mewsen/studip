<template>
    <transition-group name="system-notification-slide"
                      :class="'system-notifications ' + (placement === 'topcenter' ? 'top-center' : 'bottom-right')"
                      tag="div"
                      role="alert"
                      appear
    >
        <system-notification v-for="notification in allNotifications"
                             :key="`message-${notification.key}`"
                             :notification="notification"
                             :placement="placement"
                             @destroyMe="destroyNotification(notification)"
        ></system-notification>
    </transition-group>
</template>

<script>
import SystemNotification from './SystemNotification.vue';

export default {
    name: 'SystemNotificationManager',
    components: { SystemNotification },
    props: {
        appendAllTo: String,
        notifications: {
            type: [Array, Object],
            default: () => []
        },
        placement: {
            type: String,
            default: 'topcenter',
            validator: value => {
                return ['topcenter', 'bottomright'].includes(value);
            }
        }
    },
    data() {
        return {
            allNotifications: [],
            counter: 0,
            stoppedNotifications: false
        }
    },
    methods: {
        addNotification(notification) {
            this.allNotifications.push({
                key: this.counter++,
                ...notification
            });
        },
        destroyNotification(notification) {
            this.allNotifications = this.allNotifications.filter(n => n !== notification);
        }
    },
    created() {
        if (Array.isArray(this.notifications)) {
            this.notifications.map(this.addNotification);
        } else {
            Object.values(this.notifications).map(this.addNotification);
        }
    },
    mounted() {
        this.globalOn('push-system-notification', this.addNotification);

        window.addEventListener('keydown', evt => {
            if (evt.altKey && evt.ctrlKey && evt.code === 'KeyT') {
                this.stoppedNotifications = !this.stoppedNotifications;

                const event = this.stoppedNotifications ? 'disrupt-system-notifications' : 'resume-system-notifications';
                this.globalEmit(event);
            }
        });
    }
}
</script>
