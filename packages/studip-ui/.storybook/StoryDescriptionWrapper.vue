<template>
    <div class="storybook-story-wrapper">
        <div v-if="description" class="storybook-story-description">
            <p v-html="formattedDescription"></p>
        </div>
        <slot></slot>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { marked } from 'marked'

const props = defineProps({
    description: {
        type: String,
        default: '',
    },
})

const formattedDescription = computed(() => {
    if (!props.description) return ''

    return marked(props.description)
})
</script>

<style>
.storybook-story-description {
    background: rgb(246, 249, 252);
    border: 1px solid rgba(38, 85, 115, 0.15);
    padding: 1rem;
    margin-bottom: 2rem;
    border-radius: 4px;
    font-family: 'Lato', sans-serif;
    color: #333;
    line-height: 1.5rem;
}

.storybook-story-description ul {
    padding-left: 2rem;
}
</style>
