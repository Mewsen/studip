<template>
    <WidgetWrapper :title="widgetTitle" :widget-data="props.widgetData" v-bind="$attrs">
        <template #content>
            <div class="contact-card">user data goes here: {{ config.contactId }}</div>
        </template>

        <template #settings>
            <h3 class="settings-header">Konfiguration: Einzelner Kontakt</h3>
        </template>
    </WidgetWrapper>
</template>

<script setup>
import { computed } from 'vue';
import WidgetWrapper from '@/vue/components/widget/WidgetWrapper.vue';

const props = defineProps({
    widgetData: {
        type: Object,
        required: true,
    },
});

const widgetTitle = 'ContactSingle';

const config = computed(() => {
    return props.widgetData.config;
});


</script>
