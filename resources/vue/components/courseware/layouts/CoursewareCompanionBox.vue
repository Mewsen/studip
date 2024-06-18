<script>
export default {
    name: 'courseware-companion-box',
    props: {
        msgCompanion: String,
        mood: {
            type: String,
            default: 'default',
            validator: value => {
                return ['default','unsure', 'special', 'sad', 'pointing', 'curious'].includes(value);
            }
        }
    },
    computed: {
        msgType() {
            let type = 'info';
            switch (this.mood) {
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
    watch: {
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
