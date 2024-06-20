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
        destroyNotification(notification) {
            this.allNotifications = this.allNotifications.filter(n => n !== notification);
        }
    },
    created() {
        if (Array.isArray(this.notifications)) {
            this.allNotifications = [...this.notifications];
        } else {
            this.allNotifications = Object.values(this.notifications);
        }
    },
    mounted() {
        this.globalOn('push-system-notification', notification => {
            this.allNotifications.push({
                key: this.counter++,
                ...notification
            });
        });

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
