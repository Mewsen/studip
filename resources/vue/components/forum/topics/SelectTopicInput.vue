<script setup>
import StudipIcon from "@/vue/components/StudipIcon.vue";
import {$gettext} from "@/assets/javascripts/lib/gettext";
import StudipSelect from "@/vue/components/StudipSelect.vue";
const selectedTopics = defineModel();
</script>

<template>
    <StudipSelect
        v-bind="$attrs"
        class="multi-select-input"
        :placeholder="$gettext('Thema')"
        label="name"
        v-model="selectedTopics"
        :reduce="(topic) => {
            if(topic.name) {
                return topic;
            }

            return { name: topic };
        }"
    >
        <template #search="{attributes, events}">
            <input
                class="vs__search"
                :required="!selectedTopics && $attrs.required"
                v-bind="attributes"
                v-on="events"
            />
        </template>
        <template #open-indicator>
            <StudipIcon shape="add" :size="15"/>
        </template>
        <template #selected-option="{name, color}">
            <div class="flex items-center">
                <span v-if="color" :style="{ backgroundColor: color, height: '14px', width: '14px', marginRight: '8px'}"></span>
                <span class="line-clamp-1 flex-1">{{ name }}</span>
            </div>
        </template>
        <template #option="{name, color}">
            <div :style="{ display: 'flex', alignItems: 'center' }">
                <span v-if="color" :style="{ backgroundColor: color, height: '14px', width: '14px', marginRight: '8px'}"></span>
                <span :style="{ flex: '1'}" class="line-clamp-1">{{ name }}</span>
            </div>
        </template>
        <template #no-options>
            <div>
                {{ $gettext('Es gibt keine Themen.') }}
            </div>
        </template>
    </StudipSelect>
</template>
