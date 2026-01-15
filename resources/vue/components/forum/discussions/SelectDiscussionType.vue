<script setup>
import {$gettext} from '@/assets/javascripts/lib/gettext';
import StudipIcon from '@/vue/components/StudipIcon.vue';
import StudipSelect from '@/vue/components/StudipSelect.vue';
</script>

<template>
    <StudipSelect
        :placeholder="$gettext('Diskussionstyp (Optional)')"
        label="name"
        v-bind="{...$props, ...$attrs}"
    >
        <template #selected-option="{name, icon}">
            <div class="flex items-center">
                <StudipIcon :shape="icon" :size="18" :style="{ marginRight: '8px'}"/>
                <span class="line-clamp-1 flex-1">{{name}}</span>
            </div>
        </template>
        <template #option="{name, icon}">
            <div :style="{ display: 'flex', alignItems: 'center' }">
                <StudipIcon :shape="icon" :size="18" :style="{ marginRight: '8px'}"/>
                <span :style="{ flex: '1'}" class="line-clamp-1">{{name}}</span>
            </div>
        </template>
        <template #no-options>
            <div>
                {{ $gettext('Es gibt keine Diskussionstypen.') }}
            </div>
        </template>
    </StudipSelect>
</template>
