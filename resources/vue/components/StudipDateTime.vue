<script setup>
import { ref, computed, onMounted } from "vue"
import {datetime} from "../../assets/javascripts/lib/datetime";

const props = defineProps({
    timestamp: {
        type: Number,
        default: 0
    },
    iso: {
        type: String,
        default: null
    },
    relative: {
        type: Boolean,
        default: false
    },
    date_only: {
        type: Boolean,
        default: false
    }
})

const now = ref(Date.now())

const date = computed(() => {
    if (Number.isInteger(props.timestamp) && props.timestamp !== 0) {
        return new Date(props.timestamp * 1000)
    } else if (props.iso) {
        const parsed = new Date(props.iso)
        return isNaN(parsed.getTime()) ? null : parsed
    }
    return null
})

const current_datetime = computed(() => (date.value ? date.value.toISOString() : ''))

const displayRelative = () => {
    if (!date.value || !props.relative) {
        return false
    }
    return now.value - date.value.getTime() < 12 * 60 * 60 * 1000
}

const title = computed(() => (displayRelative() ? formattedDate(true) : null))
const formattedDate = (forceAbsolute = false) => {
    if (!date.value) {
        return 'Invalid date'
    }
    const relativeValue = !forceAbsolute && props.relative && displayRelative()
    return datetime.getStudipDate(date.value, relativeValue, props.date_only)
}

onMounted(() => {
    window.setInterval(() => {
        now.value = Date.now()
    }, 1000)
})
</script>

<template>
    <time :datetime="current_datetime" v-if="date" :title="title">
        {{ formattedDate() }}
    </time>
</template>
