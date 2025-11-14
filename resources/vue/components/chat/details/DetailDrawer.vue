<template>
    <studip-drawer v-if="attachTarget" :visible="showDrawer" :attachTo="attachTarget" side="right" @close="close">
        <detail-info v-if="detailsScope === 'room'" />
        <detail-participants v-else-if="detailsScope === 'participants'" />
    </studip-drawer>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useSettingStore } from '@/vue/store/pinia/chat/chat-settings.js';
import StudipDrawer from '@/vue/components/StudipDrawer.vue';
import DetailInfo from '@/vue/components/chat/details/DetailInfo.vue';
import DetailParticipants from '@/vue/components/chat/details/DetailParticipants.vue';

const attachTarget = ref(null);

const settingStore = useSettingStore();

const showDrawer = computed(() => settingStore.showDetailsDrawer);
const detailsScope = computed(() => settingStore.detailsScope);

onMounted(() => {
    attachTarget.value = document.querySelector('#content-wrapper');
});

const close = () => {
    settingStore.setDetailsDrawer(false);
};
</script>
