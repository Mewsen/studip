<template>
    <div v-cloak
         class="system-notification"
         :class="cssClasses"
         @mouseover="disruptTimeout"
         @mouseout="initTimeout"
         @focus="disruptTimeout"
         @blur="initTimeout"
    >
        <div class="system-notification-icon">
            <studip-icon :shape="icon.shape"
                         :size="48"
                         :role="icon.color"
                         alt=""
                         title=""></studip-icon>
        </div>
        <div class="system-notification-content">
            <p v-html="notification.message"></p>
            <p class="sr-only" v-if="hasTimeout">
                {{ $gettext('Strg+Alt+T hält das automatische Ausblenden der Meldung an bzw. setzt es wieder fort.') }}
            </p>
            <details v-if="notification.details?.length > 0"
                     class="system-notification-details"
                     :open="showDetails"
            >
                <summary @click="toggleDetails()">
                    {{ $gettext('Details') }}
                </summary>
                <template v-if="Array.isArray(notification.details)">
                    <p v-for="(detail, index) in notification.details"
                       :key="index"
                       v-html="detail"></p>
                </template>
                <p v-else v-html="notification.details"></p>
            </details>
        </div>
        <button v-if="allowClosing"
                class="system-notification-close undecorated"
                :title="$gettext('Diese Meldung schließen')"
                @click.prevent="destroyMe"
                @keydown.space="destroyMe"
                tabindex="0">
            <studip-icon shape="decline"
                         :size="20"
                         class="close-system-notification"/>
        </button>
        <transition v-if="hasTimeout"
                    name="system-notification-timeout"
                    appear
        >
            <div v-if="!stopTimeout"
                 class="system-notification-timeout"
                 ref="timeout-counter"></div>
        </transition>
    </div>
</template>

<script>
export default {
    name: 'SystemNotification',
    props: {
        allowClosing: {
            type: Boolean,
            default: true
        },
        appendTo: {
            type: String,
            default: null
        },
        notification: {
            type: Object,
            required: true
        },
        visibleFor: {
            type: Number,
            default: 5000
        }
    },
    data() {
        return {
            showDetails: !this.notification.close_details,
            stopTimeout: false,
            timeout: null,
            windowIsBlurred: false,
        }
    },
    computed: {
        cssClasses() {
            const classes = [`system-notification-${this.notification.type}`];
            if (this.isDisrupted) {
                classes.push('system-notification-disrupted');
            }
            return classes;
        },
        hasTimeout() {
            return !['exception', 'error'].includes(this.notification.type);
        },
        icon() {
            let iconShape = 'info-circle';
            let iconColor = 'info';
            switch (this.type) {
                case 'exception':
                    iconShape = 'exclaim-circle';
                    iconColor = 'info_alt';
                    break;
                case 'error':
                    iconShape = 'exclaim-circle';
                    iconColor = 'status-red';
                    break;
                case 'warning':
                    iconShape = 'exclaim-circle';
                    iconColor = 'status-yellow';
                    break;
                case 'success':
                    iconShape = 'check-circle';
                    iconColor = 'status-green';
                    break;
            }
            return {shape: iconShape, color: iconColor};
        },
        isDisrupted() {
            return this.timeout !== null && this.stopTimeout;
        }
    },
    methods: {
        destroyMe() {
            this.$emit('destroyMe');
        },
        disruptTimeout() {
            this.stopTimeout = true;
            clearTimeout(this.timeout);
        },
        initTimeout() {
            if (this.hasTimeout && this.visibleFor > 0) {
                this.stopTimeout = false;
                this.timeout = setTimeout(
                    () => this.destroyMe(),
                    this.visibleFor
                );
            }
        },
        toggleDetails() {
            this.showDetails = !this.showDetails;
        }
    },
    mounted() {
        if (this.appendTo !== null) {
            const target = document.querySelector(this.appendTo);

            // Create a live area for screen reader compatibility.
            const div = document.createElement('div');
            div.setAttribute('role', 'alert');
            div.appendChild(this.$el);
            if (target) {
                target.prepend(div);
            }
        }

        this.initTimeout();

        this.globalOn('disrupt-system-notifications', this.disruptTimeout);
        this.globalOn('resume-system-notifications', this.initTimeout);

        if (!STUDIP.config?.PERSONAL_NOTIFICATIONS_AUDIO_DEACTIVATED) {
            const audio = new Audio(STUDIP.ASSETS_URL + '/sounds/blubb.mp3');
            audio.play();
        }
    },
    destroyed() {
        this.globalOff('disrupt-system-notifications', this.disruptTimeout);
        this.globalOff('resume-system-notifications', this.initTimeout);
    }
}
</script>
<style scoped>
[v-cloak] {
    display: none;
}
</style>
