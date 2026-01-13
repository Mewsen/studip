<template>
    <time :datetime="datetime" :title="title" v-if="date">
        {{ formattedDisplay }}
    </time>
    <span v-else>{{ formattedDisplay }}</span>
</template>

<script setup>
import { computed, getCurrentInstance, onUnmounted, ref, watch } from 'vue'

const props = defineProps({
    timestamp: {
        type: Number,
        default: 0,
    },
    iso: {
        type: String,
        default: null,
    },
    relative: {
        type: Boolean,
        default: false,
    },
    dateOnly: {
        type: Boolean,
        default: false,
    },
})

const { proxy } = getCurrentInstance();
const $gettext = proxy?.$gettext ?? ((str) => str);

const now = ref(Date.now());
let interval = null;

const date = computed(() => {
    if (Number.isInteger(props.timestamp) && props.timestamp !== 0) {
        return new Date(props.timestamp * 1000);
    } else if (props.iso) {
        const parsed = new Date(props.iso);
        return isNaN(parsed.getTime()) ? null : parsed;
    }
    return null;
})

const datetime = computed(() => (date.value ? date.value.toISOString() : ''));

const isRelativeDisplayTime = computed(() => {
    if (!date.value || !props.relative) {
        return false;
    }
    return now.value - date.value.getTime() < 12 * 60 * 60 * 1000;
})

const pad = (num) => String(num).padStart(2, '0');

const getAbsoluteFormattedTime = (dateObj, dateOnly) => {
    const timeOptions = dateOnly ? {} : { hour: '2-digit', minute: '2-digit' };

    return dateObj.toLocaleDateString('de-DE', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        ...timeOptions,
    }).replace(/,/, '');
}

const getFormattedTime = (forceAbsolute = false) => {
    if (!date.value) {
        return '—';
    }

    const isRelative = !forceAbsolute && props.relative && isRelativeDisplayTime.value;

    if (isRelative) {
        let date_ms = date.value.getTime();
        let now_ms = now.value;
        const diffMinutes = Math.floor((now_ms - date_ms) / (1000 * 60));

        if (diffMinutes < 1) {
            return $gettext('Jetzt');
        }
        if (diffMinutes < 120) {
            return diffMinutes === 1
                ? $gettext('Vor 1 Minute')
                : $gettext('Vor %{ minutes } Minuten', { minutes: diffMinutes });
        }

        if (props.dateOnly) {
            return getAbsoluteFormattedTime(date.value, true);
        }

        return pad(date.value.getHours()) + ':' + pad(date.value.getMinutes());
    }

    return getAbsoluteFormattedTime(date.value, props.dateOnly);
}

const title = computed(() => {
    if (props.relative && isRelativeDisplayTime.value) {
        return getAbsoluteFormattedTime(date.value, false);
    }
    return null;
})

const formattedDisplay = computed(() => getFormattedTime());

const stopInterval = () => {
    if (interval) {
        clearInterval(interval);
        interval = null;
    }
};

const startInterval = () => {
    stopInterval();
    interval = window.setInterval(() => {
        now.value = Date.now();
    }, 5000);
};

watch(() => props.relative, (isRelative) => {
    if (isRelative) {
        startInterval();
    } else {
        stopInterval();
    }
}, { immediate: true });

onUnmounted(() => {
    stopInterval();
})
</script>
