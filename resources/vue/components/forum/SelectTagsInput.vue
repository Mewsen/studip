<script setup>
import {$gettext} from "../../../assets/javascripts/lib/gettext";
import StudipSelect from "..//StudipSelect.vue";
import StudipIcon from "@/vue/components/StudipIcon.vue";
</script>

<template>
    <StudipSelect
        v-bind="$attrs"
        class="multi-select-input"
        :placeholder="$gettext('Schlagworte')"
        label="name"
        :clearable="true"
        :reduce="(tag) => {
            if(tag.name) {
                return tag;
            }

            return { name: tag };
        }"
        :closeOnSelect="false"
    >
        <template #open-indicator>
            <StudipIcon shape="add" :size="15" />
        </template>

        <template #selected-option="{name}">
            <span>{{ name }}</span>
        </template>

        <template #no-options>
          <div>{{ $gettext('Es gibt keine Schlagworte.') }}</div>
        </template>
    </StudipSelect>
</template>
