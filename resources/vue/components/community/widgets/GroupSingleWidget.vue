<template>
    <WidgetWrapper :title="widgetTitle" v-bind="$attrs">
        <template #content>
        </template>

        <template #settings>
            <h3 class="settings-header"></h3>
            <group-picker 
                label="Wählen Sie eine Gruppe aus" 
                v-model="currentConfig.groupId" 
                :multi-select="false" :sortable="false"
            />
        </template>
    </WidgetWrapper>
</template>
<script setup>
import { computed } from 'vue';
import WidgetWrapper from '@/vue/components/widget/WidgetWrapper.vue';
import GroupPicker from '@/vue/components/community/GroupPicker.vue';

const props = defineProps({
    widgetData: {
        type: Object,
        required: true,
    },
});

const widgetTitle = 'GroupSingle';

const config = computed(() => {
    return props.widgetData.config;
});

const currentConfig = config.value;

</script>