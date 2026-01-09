<template>
    <span data-tooltip class="tooltip tooltip-icon" :id="id" :class="cssClass" tabindex="0" :aria-label="cleanText">
        <span class="tooltip-content" v-if="isHtml" v-html="text"></span>
        <span class="tooltip-content" v-else>{{ text }}</span>
    </span>
</template>

<script>
    export default {
        name: 'studip-tooltip-icon',
        props: {
            text: String,
            id: {
                type: String,
                default: null
            },
            isHtml: {
                type: Boolean,
                required: false,
                default: false
            },
            isImportant: {
                type: Boolean,
                required: false,
                default: false
            },
            size: {
                type: Number,
                default: 16
            }
        },
        computed: {
            cleanText() {
                const div = document.createElement('div');
                div.innerHTML = this.text;
                return div.innerText.trim();
            },
            cssClass () {
                return this.isImportant ? 'tooltip-important' : '';
            }
        }
    }
</script>
