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
    navigator.clipboard.writeText(props.content);
    STUDIP.Report.info($gettext('Der Inhalt wurde in die Zwischenablage kopiert.'));
}
</script>

<template>
    <div class="copyable-textarea">
        <textarea class="copyable-textarea__field" :value="content" readonly v-bind="{...$attrs}"></textarea>
        <button
            type="button"
            class="copyable-textarea__button button-base"
            @click="copyToClipboard"
            :title="$gettext('Kopieren')"
            :aria-label="$gettext('Kopieren')"
        >
            <StudipIcon shape="clipboard" :size="16" />
        </button>
    </div>
</template>
