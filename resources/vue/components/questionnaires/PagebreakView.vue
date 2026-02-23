<template>
    <button :style="{visibility: hasPrevContent ? 'visible' : 'hidden'}" class="button arr_left" @click="prevContent">
        {{ $gettext('zurück') }}
    </button>

    <button :style="{visibility: hasNextContent ? 'visible' : 'hidden'}" class="button arr_right" @click="nextContent">
        {{ $gettext('weiter') }}
    </button>


</template>


<script setup>
import { computed } from 'vue';
import {$gettext} from "../../../assets/javascripts/lib/gettext";
import StudipIcon from "../StudipIcon.vue";

const props = defineProps({
    question: {
        type: Object,
        required: true
    }
})

const hasPrevContent = computed(() => {
        return true;
})

const hasNextContent = computed(() => {
    return true;
})



</script>
