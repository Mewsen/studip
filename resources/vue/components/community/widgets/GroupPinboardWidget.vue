<template>
    <WidgetWrapper :title="widgetTitle" v-bind="$attrs">
        <template #content>
        </template>

        <template #settings>
            <h3 class="settings-header"></h3>
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

const widgetTitle = 'GroupPinboard';

const config = computed(() => {
    return props.widgetData.config;
});

</script>