<template>
    <header class="chat-header">
        <h2>{{ room.name }}</h2>
        <div class="chat-header-button-group">
            <button class="button icon-button" :title="$gettext('Details anzeigen')" @click="showDetails('room')">
                <studip-icon shape="info-circle" :size="24" />
            </button>
            <button
                class="button icon-button"
                :title="$gettext('Details anzeigen')"
                @click="showDetails('participants')"
            >
                <studip-icon shape="group2" :size="24" />
            </button>
        </div>
    </header>
</template>
<script setup>
import { computed } from 'vue';
import { useSettingStore } from '@/vue/store/pinia/chat/chat-settings.js';

const settingStore = useSettingStore();

const props = defineProps({
    room: {
        type: Object,
        required: true,
    },
});
const detailsScope = computed(() => settingStore.detailsScope);
const showDetails = (scope) => {
    if (detailsScope.value === scope) {
        settingStore.setDetailsDrawer(!settingStore.showDetailsDrawer);
        return;
    }
    settingStore.setDetailsScope(scope);
}
</script>
<style lang="scss">
.chat-header {
    display: flex;
    margin-bottom: 1em;

    h2 {
        margin: 0;
        line-height: 38px;
    }

    .chat-header-button-group {
        margin-left: auto;
        display: flex;
        gap: 4px;

        button.button.icon-button {
            min-width: unset;
            margin: 0;
            padding: 7px;

            .studip-icon {
                vertical-align: middle;
            }
        }
    }
}
</style>
