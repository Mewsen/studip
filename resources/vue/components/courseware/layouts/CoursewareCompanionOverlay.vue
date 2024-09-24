<script>
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-companion-overlay',
    render(createElement) {
        return null;
    },
    computed: {
        ...mapGetters({
            showCompanion: 'showCompanionOverlay',
            msgCompanion: 'msgCompanionOverlay',
            styleCompanion: 'styleCompanionOverlay',
            showToolbar: 'showToolbar',
        }),
        msgType() {
            let type = 'info';
            switch (this.styleCompanion) {
                case 'special':
                case 'unsure':
                    type = 'warning';
                    break;
                case 'sad':
                    type = 'error';
                    break;
                case 'happy':
                    type = 'success';
                    break
                case 'pointing':
                case 'curious':
            }
            return type;
        }
    },
    methods: {
        ...mapActions({
            coursewareShowCompanionOverlay: 'coursewareShowCompanionOverlay'
        }),
        hideCompanion() {
            this.coursewareShowCompanionOverlay(false);
        },
    },
    watch: {
        showCompanion(newValue, oldValue) {
            let view = this;
            if (newValue === true && oldValue === false) {
                setTimeout(() => {
                    view.hideCompanion();
                }, 4000);
            }
        },
        showToolbar(newValue, oldValue) {
            // hide companion when toolbar is closed
            if (oldValue === true && newValue === false) {
                this.hideCompanion();
            }
        },
        msgCompanion: {
            handler(current) {
                if (current.trim().length === 0) {
                    return;
                }
                const notification = {
                    type: this.msgType,
                    message: current
                };
                this.globalEmit('push-system-notification', notification);
            },
            immediate: true
        }
    }
};
</script>
