<template>
    <li class="chapter" :class="{ active: toc.active }">
        <span v-if="toc.active">
            <StudipIcon v-if="toc.icon" :shape="toc.icon.shape" role="info" :size="24" />
            {{ toc.title }}
        </span>
        <a v-else class="navigate" :href="toc.url">
            <StudipIcon v-if="toc.icon" :shape="toc.icon.shape" role="info" :size="24" />
            {{ toc.title }}
        </a>
        <ul v-if="toc.children.length > 0">
            <ContentBarTocItemList v-for="child in toc.children" :key="child.url" :toc="child" />
        </ul>
    </li>
</template>

<script lang="ts">
import { defineComponent, PropType } from 'vue';

import { TOCItem } from './table-of-contents';
import StudipIcon from './StudipIcon.vue';

export default defineComponent({
    name: 'ContentBarTocItemList',
    components: { StudipIcon },
    props: {
        toc: {
            required: true,
            type: Object as PropType<TOCItem>,
        },
    },
});
</script>
