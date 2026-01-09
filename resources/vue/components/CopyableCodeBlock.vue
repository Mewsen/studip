<script setup>
import {$gettext} from '@/assets/javascripts/lib/gettext';
import StudipIcon from '@/vue/components/StudipIcon.vue';

const props = defineProps({
    content: {
        type: String,
        default: ''
    }
});

const copyToClipboard = () => {
    if (props.content) {
        navigator.clipboard.writeText(props.content);
    } else {
        navigator.clipboard.writeText(document.getElementById('copyable-code-block-content').innerText.trim());
    }
    STUDIP.Report.info($gettext('Der Inhalt wurde in die Zwischenablage kopiert.'));
}
</script>

<template>
    <div class="copyable-code-block">
        <pre id="copyable-code-block-content"><slot><template v-if="content">{{ content }}</template></slot></pre>
        <button
            type="button"
            class="copyable-code-block__button button-base"
            @click="copyToClipboard"
            :title="$gettext('Kopieren')"
            :aria-label="$gettext('Kopieren')"
        >
            <StudipIcon shape="clipboard" :size="16" />
        </button>
    </div>
</template>
