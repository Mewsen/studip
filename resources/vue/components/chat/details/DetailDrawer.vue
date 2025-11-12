<template>
    <studip-drawer v-if="attachTarget" :visible="showDrawer" :attachTo="attachTarget" side="right" @close="close">
        <h3>The Drawer</h3>
    </studip-drawer>
    <button @click="showDrawer = true">Open Drawer</button>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import StudipDrawer from '@/vue/components/StudipDrawer.vue';

const attachTarget = ref(null);

const showDrawer = ref(true);

onMounted(() => {
    attachTarget.value = document.querySelector('#content-wrapper');
});

const close = () => {
    showDrawer.value = false;
};
</script>
